<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

if (empty($SessionIdAkses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi akses sudah berakhir, silakan login ulang.'
    ]);
    exit;
}

$url_service = isset($_POST['url_service'])
    ? trim(htmlspecialchars($_POST['url_service']))
    : '';

$api_key = isset($_POST['api_key'])
    ? trim(htmlspecialchars($_POST['api_key']))
    : '';

$status = isset($_POST['status'])
    ? (int) $_POST['status']
    : 0;

if ($url_service === '' || $api_key === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Masih ada data yang belum diisi.'
    ]);
    exit;
}

if (!filter_var($url_service, FILTER_VALIDATE_URL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Format URL Service tidak valid.'
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
        $stmt_deactivate = $Conn->prepare("UPDATE setting_wa SET status = 0");
        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan koneksi lain.');
        }

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan koneksi lain.');
        }

        $stmt_deactivate->close();
    }

    $query = "
        INSERT INTO setting_wa (
            url_service,
            api_key,
            status
        ) VALUES (?, ?, ?)
    ";

    $stmt = $Conn->prepare($query);

    if (!$stmt) {
        throw new Exception('Gagal menyiapkan query untuk menyimpan data.');
    }

    $stmt->bind_param(
        "ssi",
        $url_service,
        $api_key,
        $status
    );

    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan data Whatsapp Gateway.');
    }

    $Conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Whatsapp Gateway berhasil disimpan.'
    ]);

    $stmt->close();
} catch (Exception $e) {
    $Conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
