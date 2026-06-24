<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan'
];

if (empty($SessionIdAkses)) {
    $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
    echo json_encode($response);
    exit;
}

$id_setting_wa = isset($_POST['id_setting_wa']) ? validateAndSanitizeInput($_POST['id_setting_wa']) : '';
$url_service   = isset($_POST['url_service']) ? trim(htmlspecialchars($_POST['url_service'])) : '';
$api_key       = isset($_POST['api_key']) ? trim(htmlspecialchars($_POST['api_key'])) : '';
$status        = isset($_POST['status']) ? (int) $_POST['status'] : 0;

if ($id_setting_wa == '') {
    $response['message'] = 'ID Whatsapp Gateway tidak boleh kosong!';
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

if ($api_key == '') {
    $response['message'] = 'API key tidak boleh kosong!';
    echo json_encode($response);
    exit;
}

if (!in_array($status, [0, 1], true)) {
    $response['message'] = 'Status tidak valid!';
    echo json_encode($response);
    exit;
}

$Qry = $Conn->prepare("
    SELECT id_setting_wa
    FROM setting_wa
    WHERE id_setting_wa = ?
    LIMIT 1
");

if (!$Qry) {
    $response['message'] = 'Gagal mempersiapkan query database!';
    echo json_encode($response);
    exit;
}

$Qry->bind_param("i", $id_setting_wa);

if (!$Qry->execute()) {
    $response['message'] = 'Gagal membuka data Whatsapp Gateway!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    $response['message'] = 'Data Whatsapp Gateway tidak ditemukan!';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Qry->close();

$Conn->begin_transaction();

try {
    if ($status === 1) {
        $stmt_deactivate = $Conn->prepare("UPDATE setting_wa SET status = 0 WHERE id_setting_wa <> ?");

        if (!$stmt_deactivate) {
            throw new Exception('Gagal menyiapkan query untuk menonaktifkan koneksi lain!');
        }

        $stmt_deactivate->bind_param("i", $id_setting_wa);

        if (!$stmt_deactivate->execute()) {
            throw new Exception('Gagal menonaktifkan koneksi lain!');
        }

        $stmt_deactivate->close();
    }

    $stmt_update = $Conn->prepare("
        UPDATE setting_wa
        SET
            url_service = ?,
            api_key = ?,
            status = ?
        WHERE id_setting_wa = ?
    ");

    if (!$stmt_update) {
        throw new Exception('Gagal menyiapkan query untuk update data!');
    }

    $stmt_update->bind_param(
        "ssii",
        $url_service,
        $api_key,
        $status,
        $id_setting_wa
    );

    if (!$stmt_update->execute()) {
        throw new Exception('Gagal memperbarui data Whatsapp Gateway!');
    }

    $stmt_update->close();
    $Conn->commit();

    $response = [
        'status' => 'success',
        'message' => 'Whatsapp Gateway berhasil diperbarui.'
    ];
} catch (Exception $e) {
    $Conn->rollback();

    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
