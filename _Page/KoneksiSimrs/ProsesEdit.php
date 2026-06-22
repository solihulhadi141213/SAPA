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

$id_setting_simrs = isset($_POST['id_setting_simrs']) ? validateAndSanitizeInput($_POST['id_setting_simrs']) : '';
$url_simrs        = isset($_POST['url_simrs']) ? trim(htmlspecialchars($_POST['url_simrs'])) : '';
$client_id        = isset($_POST['client_id']) ? trim(htmlspecialchars($_POST['client_id'])) : '';
$client_key       = isset($_POST['client_key']) ? trim(htmlspecialchars($_POST['client_key'])) : '';
$status           = isset($_POST['status']) ? (int) $_POST['status'] : 0;

if ($id_setting_simrs == '') {
    $response['message'] = 'ID koneksi SIMRS tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if ($url_simrs == '') {
    $response['message'] = 'URL SIMRS tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if (!filter_var($url_simrs, FILTER_VALIDATE_URL)) {
    $response['message'] = 'Format URL SIMRS tidak valid!';
    echo json_encode($response);
    exit;
}

if ($client_id == '' || $client_key == '') {
    $response['message'] = 'Client ID dan Client Key tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

$Qry = $Conn->prepare("
    SELECT id_setting_simrs
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

$Qry->close();

$Conn->begin_transaction();

try {
    if ($status == 1) {
        $stmt_deactivate = $Conn->prepare("UPDATE setting_simrs SET status = 0 WHERE id_setting_simrs <> ?");

        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan koneksi lain!');
        }

        $stmt_deactivate->bind_param("i", $id_setting_simrs);

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan koneksi lain!');
        }

        $stmt_deactivate->close();
    }

    $stmt_update = $Conn->prepare("
        UPDATE setting_simrs
        SET
            url_simrs = ?,
            client_id = ?,
            client_key = ?,
            status = ?
        WHERE id_setting_simrs = ?
    ");

    if (!$stmt_update) {
        throw new Exception('Gagal menyiapkan query untuk update data!');
    }

    $stmt_update->bind_param(
        "sssii",
        $url_simrs,
        $client_id,
        $client_key,
        $status,
        $id_setting_simrs
    );

    if (!$stmt_update->execute()) {
        throw new Exception('Gagal memperbarui data koneksi SIMRS!');
    }

    $stmt_update->close();

    $Conn->commit();

    $response = [
        'status'  => 'success',
        'message' => 'Koneksi SIMRS berhasil diperbarui.'
    ];
} catch (Exception $e) {
    $Conn->rollback();

    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
