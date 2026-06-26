<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // =====================================================
    // RESPONSE DEFAULT
    // =====================================================
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // =====================================================
    // VALIDASI SESSION
    // =====================================================
    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi akses sudah berakhir. Silahkan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // VALIDASI id_survey_question
    // =====================================================
    if (empty($_POST['id_survey_question'])) {
        $response['message'] = 'ID Pertanyaan tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    $id_survey_question = validateAndSanitizeInput($_POST['id_survey_question']);


    // =====================================================
    // CEK DATA
    // =====================================================
    $Qry = $Conn->prepare("
        SELECT 
            id_survey_question
        FROM survey_question
        WHERE id_survey_question = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response['message'] = 'Gagal mempersiapkan query database.';
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_survey_question);

    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data.';
        echo json_encode($response);
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response['message'] = 'Data tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Update
    $status = 0;
    $stmt_update = $Conn->prepare("
        UPDATE survey_question SET
            status = ?
        WHERE id_survey_question = ?
    ");

    if (!$stmt_update) {
        Response("error", "Gagal mempersiapkan query update.");
    }

    $stmt_update->bind_param(
        "si",
        $status,
        $id_survey_question
    );

    if (!$stmt_update->execute()) {
        Response("error", "Gagal memperbarui password.");
    }

    $stmt_update->close();

    $response['message'] = 'Data Berhasil Dihapus';
    $response['status'] = 'success';
    echo json_encode($response);
    exit;
?>