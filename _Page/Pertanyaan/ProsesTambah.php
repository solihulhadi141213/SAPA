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

    $question_type = trim($_POST['question_type'] ?? '');
    $mandatory     = trim($_POST['mandatory'] ?? '');
    $question_text = trim($_POST['question_text'] ?? '');

    $alternatif_label = $_POST['alternatif_label'] ?? [];
    $alternatif_value = $_POST['alternatif_value'] ?? [];

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

    $mandatory = (int) $mandatory;

    // =========================================================
    // HANDLE ALTERNATIVE ANSWERS
    // =========================================================
    $alternative_answers = null;

    if ($question_type === 'coded') {

        if (!is_array($alternatif_label) || !is_array($alternatif_value)) {
            ResponseJson("error", "Format alternatif jawaban tidak valid.");
        }

        $alternatives = [];
        $countLabel = count($alternatif_label);
        $countValue = count($alternatif_value);
        $total = max($countLabel, $countValue);

        for ($i = 0; $i < $total; $i++) {
            $label = trim((string)($alternatif_label[$i] ?? ''));
            $value = trim((string)($alternatif_value[$i] ?? ''));

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

    // =========================================================
    // HITUNG QUESTION ORDER OTOMATIS
    // =========================================================
    $question_order = 1;
    $qry_order = $Conn->query("SELECT MAX(question_order) AS max_order FROM survey_question");

    if ($qry_order) {
        $data_order = $qry_order->fetch_assoc();
        if (!empty($data_order['max_order'])) {
            $question_order = ((int)$data_order['max_order']) + 1;
        }
        $qry_order->free();
    }

    // =========================================================
    // STATUS DEFAULT AKTIF
    // =========================================================
    $status = 1;

    // =========================================================
    // TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        $stmt_insert = $Conn->prepare("
            INSERT INTO survey_question (
                question_order,
                question_type,
                mandatory,
                question_text,
                alternative_answers,
                status
            ) VALUES (
                ?, ?, ?, ?, ?, ?
            )
        ");

        if (!$stmt_insert) {
            throw new Exception("Gagal mempersiapkan query penyimpanan.");
        }

        $stmt_insert->bind_param(
            "isissi",
            $question_order,
            $question_type,
            $mandatory,
            $question_text,
            $alternative_answers,
            $status
        );

        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data pertanyaan.");
        }

        $stmt_insert->close();

        mysqli_commit($Conn);

        ResponseJson("success", "Data pertanyaan berhasil ditambahkan.");

    } catch (Exception $e) {

        mysqli_rollback($Conn);
        ResponseJson("error", $e->getMessage());
    }
?>
