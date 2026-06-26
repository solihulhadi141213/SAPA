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
    $posisi             = trim($_POST['posisi'] ?? '');

    if ($id_survey_question === '') {
        ResponseJson("error", "ID pertanyaan tidak boleh kosong.");
    }

    if (!ctype_digit($id_survey_question)) {
        ResponseJson("error", "ID pertanyaan tidak valid.");
    }

    if (!in_array($posisi, ['atas', 'bawah'], true)) {
        ResponseJson("error", "Posisi pertanyaan tidak valid.");
    }

    $id_survey_question = (int) $id_survey_question;

    mysqli_begin_transaction($Conn);

    try {
        $stmt_question = $Conn->prepare("SELECT id_survey_question, question_order FROM survey_question WHERE id_survey_question = ? LIMIT 1");
        if (!$stmt_question) {
            throw new Exception("Gagal mempersiapkan query data pertanyaan.");
        }

        $stmt_question->bind_param("i", $id_survey_question);
        if (!$stmt_question->execute()) {
            throw new Exception("Gagal membaca data pertanyaan.");
        }

        $result_question = $stmt_question->get_result();
        $data_question = $result_question->fetch_assoc();
        $stmt_question->close();

        if (empty($data_question)) {
            throw new Exception("Data pertanyaan tidak ditemukan.");
        }

        $current_order = (int) $data_question['question_order'];

        if ($posisi === 'atas') {
            $stmt_neighbour = $Conn->prepare("
                SELECT id_survey_question, question_order
                FROM survey_question
                WHERE question_order < ?
                ORDER BY question_order DESC, id_survey_question DESC
                LIMIT 1
            ");
        } else {
            $stmt_neighbour = $Conn->prepare("
                SELECT id_survey_question, question_order
                FROM survey_question
                WHERE question_order > ?
                ORDER BY question_order ASC, id_survey_question ASC
                LIMIT 1
            ");
        }

        if (!$stmt_neighbour) {
            throw new Exception("Gagal mempersiapkan query posisi pembanding.");
        }

        $stmt_neighbour->bind_param("i", $current_order);
        if (!$stmt_neighbour->execute()) {
            throw new Exception("Gagal membaca posisi pertanyaan lain.");
        }

        $result_neighbour = $stmt_neighbour->get_result();
        $data_neighbour = $result_neighbour->fetch_assoc();
        $stmt_neighbour->close();

        if (empty($data_neighbour)) {
            throw new Exception("Pertanyaan sudah berada di posisi paling " . ($posisi === 'atas' ? 'atas' : 'bawah') . ".");
        }

        $neighbour_id = (int) $data_neighbour['id_survey_question'];
        $neighbour_order = (int) $data_neighbour['question_order'];

        $stmt_update_first = $Conn->prepare("UPDATE survey_question SET question_order = ? WHERE id_survey_question = ?");
        if (!$stmt_update_first) {
            throw new Exception("Gagal mempersiapkan query update urutan.");
        }

        $stmt_update_first->bind_param("ii", $neighbour_order, $id_survey_question);
        if (!$stmt_update_first->execute()) {
            throw new Exception("Gagal memperbarui urutan pertanyaan.");
        }
        $stmt_update_first->close();

        $stmt_update_second = $Conn->prepare("UPDATE survey_question SET question_order = ? WHERE id_survey_question = ?");
        if (!$stmt_update_second) {
            throw new Exception("Gagal mempersiapkan query update urutan.");
        }

        $stmt_update_second->bind_param("ii", $current_order, $neighbour_id);
        if (!$stmt_update_second->execute()) {
            throw new Exception("Gagal menukar urutan pertanyaan.");
        }
        $stmt_update_second->close();

        mysqli_commit($Conn);

        ResponseJson("success", "Posisi pertanyaan berhasil diubah.");

    } catch (Exception $e) {
        mysqli_rollback($Conn);
        ResponseJson("error", $e->getMessage());
    }
?>
