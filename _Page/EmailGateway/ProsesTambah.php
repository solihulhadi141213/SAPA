<?php
header('Content-Type: application/json');

require_once "../../_Config/Connection.php";
require_once "../../_Config/Session.php";

date_default_timezone_set('Asia/Jakarta');

if (empty($SessionIdAkses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi akses sudah berakhir, silakan login ulang.'
    ]);
    exit;
}

$email_gateway    = trim($_POST['email_gateway'] ?? '');
$password_gateway = trim($_POST['password_gateway'] ?? '');
$url_provider     = trim($_POST['url_provider'] ?? '');
$port_gateway     = trim($_POST['port_gateway'] ?? '');
$nama_pengirim    = trim($_POST['nama_pengirim'] ?? '');
$url_service      = trim($_POST['url_service'] ?? '');
$status           = isset($_POST['status']) ? (int) $_POST['status'] : 0;

if (
    $email_gateway === '' ||
    $password_gateway === '' ||
    $url_provider === '' ||
    $port_gateway === '' ||
    $nama_pengirim === '' ||
    $url_service === ''
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Masih ada data yang belum diisi.'
    ]);
    exit;
}

if (!filter_var($email_gateway, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Format email gateway tidak valid.'
    ]);
    exit;
}



if (!filter_var($url_service, FILTER_VALIDATE_URL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Format URL service tidak valid.'
    ]);
    exit;
}

if (!ctype_digit($port_gateway)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Port gateway harus berupa angka.'
    ]);
    exit;
}

if (!in_array($status, [0, 1], true)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Status tidak valid.'
    ]);
    exit;
}

$Conn->begin_transaction();

try {
    if ($status === 1) {
        $stmt_deactivate = $Conn->prepare("UPDATE setting_email_gateway SET status = 0");
        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan gateway lain.');
        }

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan gateway lain.');
        }

        $stmt_deactivate->close();
    }

    $query = "
        INSERT INTO setting_email_gateway (
            email_gateway,
            password_gateway,
            url_provider,
            port_gateway,
            nama_pengirim,
            url_service,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $Conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Gagal menyiapkan query untuk menyimpan data.');
    }

    $stmt->bind_param(
        "ssssssi",
        $email_gateway,
        $password_gateway,
        $url_provider,
        $port_gateway,
        $nama_pengirim,
        $url_service,
        $status
    );

    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan data Email Gateway.');
    }

    $Conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Email gateway berhasil disimpan.'
    ]);

    $stmt->close();
} catch (Exception $e) {
    $Conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
