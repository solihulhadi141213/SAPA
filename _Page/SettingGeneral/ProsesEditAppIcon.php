<?php
header('Content-Type: application/json');

include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan'
];

if (empty($SessionIdAkses)) {
    $response['message'] = 'Sesi akses sudah berakhir. Silahkan login ulang.';
    echo json_encode($response);
    exit;
}

if (empty($_POST['id_setting_general'])) {
    $response['message'] = 'ID setting general tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

if (empty($_FILES['app_icon']['name'])) {
    $response['message'] = 'File app icon tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

$id_setting_general = validateAndSanitizeInput($_POST['id_setting_general']);

function UploadLogoFile($file, $folder)
{
    $allowed_ext = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
    $max_size = 2 * 1024 * 1024;

    if (!isset($file) || $file['error'] !== 0) {
        return [
            'success' => false,
            'message' => 'File gagal diupload.'
        ];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        return [
            'success' => false,
            'message' => 'Format file tidak diizinkan.'
        ];
    }

    if ($file['size'] > $max_size) {
        return [
            'success' => false,
            'message' => 'Ukuran file maksimal 2 MB.'
        ];
    }

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $new_file_name = date('YmdHis') . '_' . uniqid() . '.' . $ext;
    $target_path = $folder . '/' . $new_file_name;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        return [
            'success' => false,
            'message' => 'Gagal menyimpan file.'
        ];
    }

    return [
        'success' => true,
        'filename' => $new_file_name
    ];
}

$Qry = $Conn->prepare("
    SELECT
        id_setting_general,
        app_name,
        app_icon
    FROM setting_general
    WHERE id_setting_general = ?
    LIMIT 1
");

if (!$Qry) {
    $response['message'] = 'Gagal mempersiapkan query database.';
    echo json_encode($response);
    exit;
}

$Qry->bind_param("i", $id_setting_general);

if (!$Qry->execute()) {
    $response['message'] = 'Gagal membuka data setting general.';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    $response['message'] = 'Data setting general tidak ditemukan.';
    echo json_encode($response);
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();

$app_name = $Data['app_name'];
$old_app_icon = $Data['app_icon'];

$Qry->close();

$upload = UploadLogoFile($_FILES['app_icon'], "../../assets/img/logo");

if (!$upload['success']) {
    $response['message'] = $upload['message'];
    echo json_encode($response);
    exit;
}

$new_app_icon = $upload['filename'];

mysqli_begin_transaction($Conn);

try {
    $Update = $Conn->prepare("
        UPDATE setting_general
        SET app_icon = ?
        WHERE id_setting_general = ?
    ");

    if (!$Update) {
        throw new Exception('Gagal mempersiapkan query update.');
    }

    $Update->bind_param("si", $new_app_icon, $id_setting_general);

    if (!$Update->execute()) {
        throw new Exception('Gagal memperbarui app icon.');
    }

    $Update->close();

    mysqli_commit($Conn);

    if (!empty($old_app_icon)) {
        $old_path = "../../assets/img/logo/" . $old_app_icon;
        if (file_exists($old_path) && is_file($old_path)) {
            @unlink($old_path);
        }
    }

    $response = [
        'status'  => 'success',
        'message' => 'App icon untuk "' . $app_name . '" berhasil diperbarui.'
    ];
} catch (Exception $e) {
    mysqli_rollback($Conn);

    if (file_exists("../../assets/img/logo/" . $new_app_icon)) {
        @unlink("../../assets/img/logo/" . $new_app_icon);
    }

    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
