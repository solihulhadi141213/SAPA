<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set('Asia/Jakarta');

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan'
];

if (empty($SessionIdAkses)) {
    $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
    echo json_encode($response);
    exit;
}

$id_setting_email_gateway = isset($_POST['id_setting_email_gateway']) ? validateAndSanitizeInput($_POST['id_setting_email_gateway']) : '';
$email_gateway            = isset($_POST['email_gateway']) ? trim(htmlspecialchars($_POST['email_gateway'])) : '';
$password_gateway         = isset($_POST['password_gateway']) ? trim($_POST['password_gateway']) : '';
$url_provider             = isset($_POST['url_provider']) ? trim(htmlspecialchars($_POST['url_provider'])) : '';
$port_gateway             = isset($_POST['port_gateway']) ? trim(htmlspecialchars($_POST['port_gateway'])) : '';
$nama_pengirim            = isset($_POST['nama_pengirim']) ? trim(htmlspecialchars($_POST['nama_pengirim'])) : '';
$url_service              = isset($_POST['url_service']) ? trim(htmlspecialchars($_POST['url_service'])) : '';
$status                   = isset($_POST['status']) ? (int) $_POST['status'] : 0;

if ($id_setting_email_gateway == '') {
    $response['message'] = 'ID email gateway tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if ($email_gateway == '') {
    $response['message'] = 'Email gateway tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if (!filter_var($email_gateway, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Format email gateway tidak valid!';
    echo json_encode($response);
    exit;
}

if ($url_provider == '') {
    $response['message'] = 'URL provider tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if ($port_gateway == '') {
    $response['message'] = 'Port gateway tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if (!ctype_digit($port_gateway)) {
    $response['message'] = 'Port gateway harus berupa angka!';
    echo json_encode($response);
    exit;
}

if ($nama_pengirim == '') {
    $response['message'] = 'Nama pengirim tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if ($url_service == '') {
    $response['message'] = 'URL service tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if (!filter_var($url_service, FILTER_VALIDATE_URL)) {
    $response['message'] = 'Format URL service tidak valid!';
    echo json_encode($response);
    exit;
}

if (!in_array($status, [0, 1], true)) {
    $response['message'] = 'Status tidak valid!';
    echo json_encode($response);
    exit;
}

$Qry = $Conn->prepare("
    SELECT
        id_setting_email_gateway,
        password_gateway
    FROM setting_email_gateway
    WHERE id_setting_email_gateway = ?
    LIMIT 1
");

if (!$Qry) {
    $response['message'] = 'Gagal mempersiapkan query database!';
    echo json_encode($response);
    exit;
}

$Qry->bind_param("i", $id_setting_email_gateway);

if (!$Qry->execute()) {
    $response['message'] = 'Gagal membuka data email gateway!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    $response['message'] = 'Data email gateway tidak ditemukan!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();
$password_gateway_lama = $Data['password_gateway'];
$Qry->close();

if ($password_gateway == '') {
    $password_gateway = $password_gateway_lama;
}

$Conn->begin_transaction();

try {
    if ($status === 1) {
        $stmt_deactivate = $Conn->prepare("UPDATE setting_email_gateway SET status = 0 WHERE id_setting_email_gateway <> ?");

        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan gateway lain!');
        }

        $stmt_deactivate->bind_param("i", $id_setting_email_gateway);

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan gateway lain!');
        }

        $stmt_deactivate->close();
    }

    $stmt_update = $Conn->prepare("
        UPDATE setting_email_gateway
        SET
            email_gateway = ?,
            password_gateway = ?,
            url_provider = ?,
            port_gateway = ?,
            nama_pengirim = ?,
            url_service = ?,
            status = ?
        WHERE id_setting_email_gateway = ?
    ");

    if (!$stmt_update) {
        throw new Exception('Gagal menyiapkan query untuk update data!');
    }

    $stmt_update->bind_param(
        "ssssssii",
        $email_gateway,
        $password_gateway,
        $url_provider,
        $port_gateway,
        $nama_pengirim,
        $url_service,
        $status,
        $id_setting_email_gateway
    );

    if (!$stmt_update->execute()) {
        throw new Exception('Gagal memperbarui data email gateway!');
    }

    $stmt_update->close();
    $Conn->commit();

    $response = [
        'status' => 'success',
        'message' => 'Email gateway berhasil diperbarui.'
    ];
} catch (Exception $e) {
    $Conn->rollback();

    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
