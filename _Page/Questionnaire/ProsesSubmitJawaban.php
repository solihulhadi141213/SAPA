<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    function ResponseJson($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    $token = trim($_POST['token'] ?? '');
    $id_respondent = trim($_POST['id_respondent'] ?? '');
    $answers = $_POST['answer'] ?? [];

    if ($token === '') {
        ResponseJson("error", "Token tidak valid.");
    }

    if ($id_respondent === '' || !ctype_digit($id_respondent)) {
        ResponseJson("error", "ID responden tidak valid.");
    }

    if (!is_array($answers) || count($answers) === 0) {
        ResponseJson("error", "Tidak ada jawaban yang dikirim.");
    }

    $id_respondent = (int) $id_respondent;

    $stmt_log = $Conn->prepare("SELECT id_survey_log, answer FROM survey_log WHERE invitation_token = ? AND id_respondent = ? LIMIT 1");
    if (!$stmt_log) {
        ResponseJson("error", "Gagal mempersiapkan validasi sesi.");
    }
    $stmt_log->bind_param("si", $token, $id_respondent);
    if (!$stmt_log->execute()) {
        $stmt_log->close();
        ResponseJson("error", "Gagal membuka sesi undangan.");
    }
    $result_log = $stmt_log->get_result();
    if (!$result_log || $result_log->num_rows === 0) {
        $stmt_log->close();
        ResponseJson("error", "Sesi undangan tidak ditemukan.");
    }
    $row_log = $result_log->fetch_assoc();
    if ((int) ($row_log['answer'] ?? 0) === 1) {
        $stmt_log->close();
        ResponseJson("error", "Sesi pertanyaan berdasarkan token tersebut sudah diisi.");
    }
    $stmt_log->close();

    $stmt_existing = $Conn->prepare("SELECT COUNT(*) AS total_answer FROM survey_answer WHERE id_respondent = ?");
    if ($stmt_existing) {
        $stmt_existing->bind_param("i", $id_respondent);
        $stmt_existing->execute();
        $result_existing = $stmt_existing->get_result();
        $row_existing = $result_existing ? $result_existing->fetch_assoc() : [];
        if ((int)($row_existing['total_answer'] ?? 0) > 0) {
            $stmt_existing->close();
            ResponseJson("error", "Sesi pertanyaan berdasarkan token tersebut sudah diisi.");
        }
        $stmt_existing->close();
    }

    $stmt_question = $Conn->prepare("SELECT id_survey_question, question_type, mandatory, alternative_answers FROM survey_question WHERE status = 1 ORDER BY question_order ASC");
    if (!$stmt_question) {
        ResponseJson("error", "Gagal mempersiapkan daftar pertanyaan.");
    }
    if (!$stmt_question->execute()) {
        $stmt_question->close();
        ResponseJson("error", "Gagal membuka daftar pertanyaan.");
    }
    $result_question = $stmt_question->get_result();
    $questions = [];
    while ($row = $result_question->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmt_question->close();

    if (count($questions) === 0) {
        ResponseJson("error", "Tidak ada pertanyaan aktif.");
    }

    $Conn->begin_transaction();
    try {
        $stmt_insert = $Conn->prepare("INSERT INTO survey_answer (id_survey_question, id_respondent, answer_text) VALUES (?, ?, ?)");
        if (!$stmt_insert) {
            throw new Exception("Gagal mempersiapkan penyimpanan jawaban.");
        }

        $stmt_update_log = $Conn->prepare("UPDATE survey_log SET answer = 1 WHERE id_respondent = ? AND invitation_token = ? LIMIT 1");
        if (!$stmt_update_log) {
            throw new Exception("Gagal mempersiapkan pembaruan status undangan.");
        }

        foreach ($questions as $question) {
            $qid = (int) $question['id_survey_question'];
            $qtype = (string) $question['question_type'];
            $mandatory = (int) $question['mandatory'];
            $answer = trim((string) ($answers[$qid] ?? ''));

            if ($mandatory === 1 && $answer === '') {
                throw new Exception("Semua pertanyaan wajib diisi.");
            }

            if ($answer === '') {
                continue;
            }

            if ($qtype === 'number' && !preg_match('/^-?\d+$/', $answer)) {
                throw new Exception("Jawaban untuk pertanyaan number harus berupa angka bulat.");
            }

            if ($qtype === 'decimal' && !is_numeric($answer)) {
                throw new Exception("Jawaban untuk pertanyaan decimal harus berupa angka.");
            }

            if ($qtype === 'boolean' && !in_array($answer, ['0', '1'], true)) {
                throw new Exception("Jawaban boolean tidak valid.");
            }

            if ($qtype === 'coded' && !empty($question['alternative_answers'])) {
                $allowed = [];
                $decoded = json_decode($question['alternative_answers'], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $alt) {
                        if (isset($alt['value'])) {
                            $allowed[] = (string) $alt['value'];
                        }
                    }
                }
                if (count($allowed) > 0 && !in_array($answer, $allowed, true)) {
                    throw new Exception("Jawaban coded tidak sesuai pilihan yang tersedia.");
                }
            }

            $stmt_insert->bind_param("iis", $qid, $id_respondent, $answer);
            if (!$stmt_insert->execute()) {
                throw new Exception("Gagal menyimpan jawaban.");
            }
        }

        $stmt_insert->close();
        $stmt_update_log->bind_param("is", $id_respondent, $token);
        if (!$stmt_update_log->execute()) {
            throw new Exception("Gagal memperbarui status jawaban.");
        }
        $stmt_update_log->close();
        $Conn->commit();
        ResponseJson("success", "Jawaban berhasil dikirim. Terima kasih atas partisipasinya.");
    } catch (Exception $e) {
        if (isset($stmt_update_log) && $stmt_update_log) {
            $stmt_update_log->close();
        }
        if (isset($stmt_insert) && $stmt_insert) {
            $stmt_insert->close();
        }
        $Conn->rollback();
        ResponseJson("error", $e->getMessage());
    }
?>
