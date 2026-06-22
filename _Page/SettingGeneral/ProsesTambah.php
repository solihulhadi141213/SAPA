<?php
header('Content-Type: application/json');

require_once "../../_Config/Connection.php";
require_once "../../_Config/Session.php";
require_once "../../_Config/Helper.php";

// Validasi Session
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status" => "error",
        "message" => "Sesi akses sudah berakhir, silakan login ulang."
    ]);
    exit;
}

// Fungsi upload file
function UploadFile($file, $folder)
{
    $allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if (!isset($file) || $file['error'] != 0) {
        return [
            "success" => false,
            "message" => "File gagal diupload."
        ];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return [
            "success" => false,
            "message" => "Format file tidak diizinkan."
        ];
    }

    if ($file['size'] > $maxSize) {
        return [
            "success" => false,
            "message" => "Ukuran file maksimal 2 MB."
        ];
    }

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $nama_file = date('YmdHis') . '_' . uniqid() . '.' . $ext;
    $target = $folder . '/' . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return [
            "success" => false,
            "message" => "Gagal menyimpan file."
        ];
    }

    return [
        "success" => true,
        "filename" => $nama_file
    ];
}

// Ambil Data Form
$app_name              = trim($_POST['app_name'] ?? '');
$app_description       = trim($_POST['app_description'] ?? '');
$app_author            = trim($_POST['app_author'] ?? '');
$metatag_keyword       = trim($_POST['metatag_keyword'] ?? '');
$metatag_description   = trim($_POST['metatag_description'] ?? '');
$company_name          = trim($_POST['company_name'] ?? '');
$company_address       = trim($_POST['company_address'] ?? '');
$company_email         = trim($_POST['company_email'] ?? '');
$company_phone         = trim($_POST['company_phone'] ?? '');
$base_url              = trim($_POST['base_url'] ?? '');
$environment_status    = trim($_POST['environment_status'] ?? '');
$configuration_status  = trim($_POST['configuration_status'] ?? '1');

// Validasi
if (
    empty($app_name) ||
    empty($app_author) ||
    empty($metatag_keyword) ||
    empty($metatag_description) ||
    empty($company_name) ||
    empty($company_address) ||
    empty($company_email) ||
    empty($company_phone) ||
    empty($base_url) ||
    empty($environment_status)
) {
    echo json_encode([
        "status" => "error",
        "message" => "Masih ada data yang belum diisi."
    ]);
    exit;
}

// Validasi Email
if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Format email tidak valid."
    ]);
    exit;
}

// Validasi URL
if (!filter_var($base_url, FILTER_VALIDATE_URL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Format Base URL tidak valid."
    ]);
    exit;
}

// Validasi Environment
$environment_list = ['Development', 'Staging', 'Production'];

if (!in_array($environment_status, $environment_list)) {
    echo json_encode([
        "status" => "error",
        "message" => "Environment status tidak valid."
    ]);
    exit;
}

// Upload App Icon
$uploadIcon = UploadFile(
    $_FILES['app_icon'],
    "../../assets/img/logo"
);

if (!$uploadIcon['success']) {
    echo json_encode([
        "status" => "error",
        "message" => "App Icon : " . $uploadIcon['message']
    ]);
    exit;
}

$app_icon = $uploadIcon['filename'];

// Upload Logo
$uploadLogo = UploadFile(
    $_FILES['company_logo'],
    "../../assets/img/logo"
);

if (!$uploadLogo['success']) {

    // Hapus icon yang sudah terupload
    @unlink("../../assets/img/logo/" . $app_icon);

    echo json_encode([
        "status" => "error",
        "message" => "Company Logo : " . $uploadLogo['message']
    ]);
    exit;
}

$company_logo = $uploadLogo['filename'];

// Simpan Data
$query = "
    INSERT INTO setting_general (
        app_name,
        app_description,
        app_icon,
        app_author,
        metatag_keyword,
        metatag_description,
        company_name,
        company_address,
        company_email,
        company_phone,
        company_logo,
        base_url,
        environment_status,
        configuration_status
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
";

$stmt = mysqli_prepare($Conn, $query);

if (!$stmt) {

    @unlink("../../assets/img/logo/" . $app_icon);
    @unlink("../../assets/img/logo/" . $company_logo);

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyiapkan query."
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssssssssssi",
    $app_name,
    $app_description,
    $app_icon,
    $app_author,
    $metatag_keyword,
    $metatag_description,
    $company_name,
    $company_address,
    $company_email,
    $company_phone,
    $company_logo,
    $base_url,
    $environment_status,
    $configuration_status
);

$execute = mysqli_stmt_execute($stmt);

if ($execute) {

    echo json_encode([
        "status" => "success",
        "message" => "Data pengaturan umum berhasil disimpan."
    ]);
} else {

    @unlink("../../assets/img/logo/" . $app_icon);
    @unlink("../../assets/img/logo/" . $company_logo);

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan data ke database."
    ]);
}

mysqli_stmt_close($stmt);
?>