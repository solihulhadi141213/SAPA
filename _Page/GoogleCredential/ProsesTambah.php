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

$credential_env = isset($_POST['credential_env'])
    ? trim(htmlspecialchars($_POST['credential_env']))
    : '';

$client_id = isset($_POST['client_id'])
    ? trim(htmlspecialchars($_POST['client_id']))
    : '';

$client_id = trim($client_id);

$client_secret = isset($_POST['client_secret'])
    ? trim(htmlspecialchars($_POST['client_secret']))
    : '';

$client_secret = trim($client_secret);

$status = isset($_POST['status'])
    ? (int) $_POST['status']
    : 0;

if ($credential_env === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Credential ENV Tidak Boleh Kosong'
    ]);
    exit;
}
if ($client_id === '' ) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Client ID Tidak Boleh Kosong'
    ]);
    exit;
}
if ($client_secret === '' ) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Client Secret Tidak Boleh Kosong'
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
        $stmt_deactivate = $Conn->prepare("UPDATE google_credential SET status = 0");
        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan Credential lain.');
        }

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan Credential lain.');
        }

        $stmt_deactivate->close();
    }

    $query = "
        INSERT INTO google_credential (
            credential_env,
            client_id,
            client_secret,
            status
        ) VALUES (?, ?, ?, ?)
    ";

    $stmt = $Conn->prepare($query);

    if (!$stmt) {
        throw new Exception('Gagal menyiapkan query untuk menyimpan data.');
    }

    $stmt->bind_param(
        "sssi",
        $credential_env,
        $client_id,
        $client_secret,
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
