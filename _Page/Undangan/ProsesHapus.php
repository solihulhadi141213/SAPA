<?php
    // Default Header JSON
    header('Content-Type: application/json');

    // Include Connection, Helper & Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Set Time Zone
    date_default_timezone_set('Asia/Jakarta');

    // Default Response
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // Session Validation
    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
        echo json_encode($response);
        exit;
    }

    // Validation 'id_respondent' is Mandatory
    if (empty($_POST['id_respondent'])) {
        $response['message'] = 'Tidak Ada Data Yang Anda Pilih!';
        echo json_encode($response);
        exit;
    }

    // Creat Variable
    $id_respondent = validateAndSanitizeInput($_POST['id_respondent']);

    // Check Data
    $Qry = $Conn->prepare("SELECT * FROM survey_log WHERE id_respondent = ? LIMIT 1");
    if (!$Qry) {
        $response['message'] = 'Gagal mempersiapkan query database!';
        echo json_encode($response);
        exit;
    }
    $Qry->bind_param("i", $id_respondent);
    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }
    $Result = $Qry->get_result();

    // If Data Not Found From Database
    if ($Result->num_rows == 0) {
        $response['message'] = 'Data yang anda pilih tidak ditemukan!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }
    $Data = $Result->fetch_assoc();
    $Qry->close();

    // Start Transaction Delet Data
    $Conn->begin_transaction();
    try {
        $stmt_delete = $Conn->prepare("DELETE FROM survey_log WHERE id_respondent = ?");

        if (!$stmt_delete) {
            throw new Exception('Gagal menyiapkan query untuk menghapus data!');
        }

        $stmt_delete->bind_param("i", $id_respondent);

        if (!$stmt_delete->execute()) {
            throw new Exception('Gagal menghapus data!');
        }

        $stmt_delete->close();

        $Conn->commit();

        $response = [
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.'
        ];
    } catch (Exception $e) {
        $Conn->rollback();

        $response = [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($response);
?>
