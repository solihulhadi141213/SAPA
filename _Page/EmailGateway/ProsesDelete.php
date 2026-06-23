<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set('Asia/Jakarta');

$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan'
];

if (empty($SessionIdAkses)) {
    $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
    echo json_encode($response);
    exit;
}

if (empty($_POST['id_setting_email_gateway'])) {
    $response['message'] = 'ID koneksi SIMRS tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

$id_setting_email_gateway = validateAndSanitizeInput($_POST['id_setting_email_gateway']);

$Qry = $Conn->prepare("
    SELECT * FROM setting_email_gateway WHERE id_setting_email_gateway = ?
    LIMIT 1
");

if (!$Qry) {
    $response['message'] = 'Gagal mempersiapkan query database!';
    echo json_encode($response);
    exit;
}

$Qry->bind_param("i", $id_setting_email_gateway);

if (!$Qry->execute()) {
    $response['message'] = 'Gagal membuka data Pengaturan!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    $response['message'] = 'Data Pengaturan tidak ditemukan!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();


$Qry->close();

$Conn->begin_transaction();

try {
    $stmt_delete = $Conn->prepare("
        DELETE FROM setting_email_gateway
        WHERE id_setting_email_gateway = ?
    ");

    if (!$stmt_delete) {
        throw new Exception('Gagal menyiapkan query untuk menghapus data!');
    }

    $stmt_delete->bind_param("i", $id_setting_email_gateway);

    if (!$stmt_delete->execute()) {
        throw new Exception('Gagal menghapus data Pengaturan!');
    }

    $stmt_delete->close();

    $Conn->commit();

    $response = [
        'status'  => 'success',
        'message' => 'Pengaturan Email Gateway berhasil dihapus.'
    ];
} catch (Exception $e) {
    $Conn->rollback();

    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
