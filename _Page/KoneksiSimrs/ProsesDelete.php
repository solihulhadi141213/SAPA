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

if (empty($_POST['id_setting_simrs'])) {
    $response['message'] = 'ID koneksi SIMRS tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

$id_setting_simrs = validateAndSanitizeInput($_POST['id_setting_simrs']);

$Qry = $Conn->prepare("
    SELECT
        id_setting_simrs,
        url_simrs
    FROM setting_simrs
    WHERE id_setting_simrs = ?
    LIMIT 1
");

if (!$Qry) {
    $response['message'] = 'Gagal mempersiapkan query database!';
    echo json_encode($response);
    exit;
}

$Qry->bind_param("i", $id_setting_simrs);

if (!$Qry->execute()) {
    $response['message'] = 'Gagal membuka data koneksi SIMRS!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    $response['message'] = 'Data koneksi SIMRS tidak ditemukan!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();
$url_simrs = $Data['url_simrs'];

$Qry->close();

$Conn->begin_transaction();

try {
    $stmt_delete = $Conn->prepare("
        DELETE FROM setting_simrs
        WHERE id_setting_simrs = ?
    ");

    if (!$stmt_delete) {
        throw new Exception('Gagal menyiapkan query untuk menghapus data!');
    }

    $stmt_delete->bind_param("i", $id_setting_simrs);

    if (!$stmt_delete->execute()) {
        throw new Exception('Gagal menghapus data koneksi SIMRS!');
    }

    $stmt_delete->close();

    $Conn->commit();

    $response = [
        'status'  => 'success',
        'message' => 'Koneksi SIMRS "' . $url_simrs . '" berhasil dihapus.'
    ];
} catch (Exception $e) {
    $Conn->rollback();

    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
