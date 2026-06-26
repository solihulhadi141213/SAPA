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

    // =====================================================
    // TRANSACTION
    // =====================================================
    mysqli_begin_transaction($Conn);

    try {

        // =================================================
        // HAPUS DATA
        // =================================================
        $Delete = $Conn->prepare("
            DELETE FROM survey_question
            WHERE id_survey_question = ?
        ");

        if (!$Delete) {
            throw new Exception("Gagal mempersiapkan query hapus.");
        }

        $Delete->bind_param("i", $id_survey_question);

        if (!$Delete->execute()) {
            throw new Exception("Gagal menghapus data akses.");
        }

        $Delete->close();

        // =================================================
        // COMMIT
        // =================================================
        mysqli_commit($Conn);

        $response = [
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.'
        ];

    } catch (Exception $e) {

        mysqli_rollback($Conn);

        $response = [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($response);
?>