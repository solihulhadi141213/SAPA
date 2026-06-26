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

    if (empty($SessionIdAkses)) {
        ResponseJson("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    $id_survey_question = trim($_POST['id_survey_question'] ?? '');
    $question_type      = trim($_POST['question_type'] ?? '');
    $mandatory          = trim($_POST['mandatory'] ?? '');
    $question_text      = trim($_POST['question_text'] ?? '');
    $status             = trim($_POST['status'] ?? '');

    $alternatif_label   = $_POST['alternatif_label'] ?? [];
    $alternatif_value   = $_POST['alternatif_value'] ?? [];

    if ($id_survey_question === '') {
        ResponseJson("error", "ID pertanyaan tidak valid.");
    }

    if (!ctype_digit($id_survey_question)) {
        ResponseJson("error", "ID pertanyaan tidak valid.");
    }

    if ($question_type === '') {
        ResponseJson("error", "Tipe pertanyaan wajib dipilih.");
    }

    if (!in_array($question_type, ['number', 'decimal', 'text', 'coded', 'boolean'], true)) {
        ResponseJson("error", "Tipe pertanyaan tidak valid.");
    }

    if ($mandatory === '') {
        ResponseJson("error", "Status wajib diisi.");
    }

    if (!in_array($mandatory, ['0', '1'], true)) {
        ResponseJson("error", "Status wajib tidak valid.");
    }

    if ($question_text === '') {
        ResponseJson("error", "Pertanyaan tidak boleh kosong.");
    }

    if ($status === '') {
        ResponseJson("error", "Status data wajib diisi.");
    }

    if (!in_array($status, ['0', '1'], true)) {
        ResponseJson("error", "Status data tidak valid.");
    }

    $id_survey_question = (int) $id_survey_question;
    $mandatory = (int) $mandatory;
    $status = (int) $status;

    $alternative_answers = null;
    if ($question_type === 'coded') {
        if (!is_array($alternatif_label) || !is_array($alternatif_value)) {
            ResponseJson("error", "Format alternatif jawaban tidak valid.");
        }

        $alternatives = [];
        $total = max(count($alternatif_label), count($alternatif_value));

        for ($i = 0; $i < $total; $i++) {
            $label = trim((string) ($alternatif_label[$i] ?? ''));
            $value = trim((string) ($alternatif_value[$i] ?? ''));

            if ($label === '' && $value === '') {
                continue;
            }

            if ($label === '' || $value === '') {
                ResponseJson("error", "Setiap alternatif jawaban harus memiliki label dan value.");
            }

            $alternatives[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        if (count($alternatives) === 0) {
            ResponseJson("error", "Minimal satu alternatif jawaban wajib diisi untuk tipe coded.");
        }

        $alternative_answers = json_encode($alternatives, JSON_UNESCAPED_UNICODE);
        if ($alternative_answers === false) {
            ResponseJson("error", "Gagal memproses alternatif jawaban.");
        }
    }

    $Conn->begin_transaction();

    try {
        $stmt_check = $Conn->prepare("SELECT id_survey_question FROM survey_question WHERE id_survey_question = ? LIMIT 1");
        if (!$stmt_check) {
            throw new Exception("Gagal mempersiapkan query validasi data.");
        }

        $stmt_check->bind_param("i", $id_survey_question);
        if (!$stmt_check->execute()) {
            throw new Exception("Gagal memeriksa data pertanyaan.");
        }

        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows === 0) {
            $stmt_check->close();
            throw new Exception("Data pertanyaan tidak ditemukan.");
        }
        $stmt_check->close();

        $stmt_update = $Conn->prepare("
            UPDATE survey_question
            SET question_type = ?,
                mandatory = ?,
                question_text = ?,
                alternative_answers = ?,
                status = ?
            WHERE id_survey_question = ?
            LIMIT 1
        ");

        if (!$stmt_update) {
            throw new Exception("Gagal mempersiapkan query pembaruan.");
        }

        $stmt_update->bind_param(
            "sissii",
            $question_type,
            $mandatory,
            $question_text,
            $alternative_answers,
            $status,
            $id_survey_question
        );

        if (!$stmt_update->execute()) {
            throw new Exception("Gagal memperbarui data pertanyaan.");
        }

        $stmt_update->close();

        $Conn->commit();
        ResponseJson("success", "Data pertanyaan berhasil diperbarui.");
    } catch (Exception $e) {
        $Conn->rollback();
        ResponseJson("error", $e->getMessage());
    }
?>
