<?php
header('Content-Type: application/json');

// =========================================================
// CONNECTION & SESSION
// =========================================================
include "../../_Config/Connection.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

// =========================================================
// VALIDASI SESSION
// =========================================================
if (empty($SessionIdAkses)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Sesi akses sudah berakhir. Silakan login ulang."
    ]);
    exit;
}

// =========================================================
// FUNCTION RESPONSE
// =========================================================
function Response($status, $message)
{
    echo json_encode([
        "status"  => $status,
        "message" => $message
    ]);
    exit;
}

// =========================================================
// AMBIL DATA
// =========================================================
$id_setting_general   = trim($_POST['id_setting_general'] ?? '');
$app_name             = trim($_POST['app_name'] ?? '');
$app_description      = trim($_POST['app_description'] ?? '');
$app_author           = trim($_POST['app_author'] ?? '');
$metatag_keyword      = trim($_POST['metatag_keyword'] ?? '');
$metatag_description  = trim($_POST['metatag_description'] ?? '');
$company_name         = trim($_POST['company_name'] ?? '');
$company_address      = trim($_POST['company_address'] ?? '');
$company_email        = trim($_POST['company_email'] ?? '');
$company_phone        = trim($_POST['company_phone'] ?? '');
$base_url             = trim($_POST['base_url'] ?? '');
$environment_status   = trim($_POST['environment_status'] ?? '');
$configuration_status = trim($_POST['configuration_status'] ?? '1');

// =========================================================
// VALIDASI MANDATORY
// =========================================================
if (empty($id_setting_general)) {
    Response("error", "ID pengaturan tidak valid.");
}

if (empty($app_name)) {
    Response("error", "App Name tidak boleh kosong.");
}

if (empty($app_author)) {
    Response("error", "App Author tidak boleh kosong.");
}

if (empty($metatag_keyword)) {
    Response("error", "Metatag Keyword tidak boleh kosong.");
}

if (empty($metatag_description)) {
    Response("error", "Metatag Description tidak boleh kosong.");
}

if (empty($company_name)) {
    Response("error", "Company Name tidak boleh kosong.");
}

if (empty($company_address)) {
    Response("error", "Company Address tidak boleh kosong.");
}

if (empty($company_email)) {
    Response("error", "Company Email tidak boleh kosong.");
}

if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
    Response("error", "Format Company Email tidak valid.");
}

if (empty($company_phone)) {
    Response("error", "Company Phone tidak boleh kosong.");
}

if (empty($base_url)) {
    Response("error", "Base URL tidak boleh kosong.");
}

if (!filter_var($base_url, FILTER_VALIDATE_URL)) {
    Response("error", "Format Base URL tidak valid.");
}

if (empty($environment_status)) {
    Response("error", "Environment Status wajib dipilih.");
}

// Validasi Environment
$environment_list = ['Development', 'Staging', 'Production'];
if (!in_array($environment_status, $environment_list)) {
    Response("error", "Environment Status tidak valid.");
}

// =========================================================
// VALIDASI KEBERADAAN DATA
// =========================================================
$stmt_check = $Conn->prepare("
    SELECT id_setting_general
    FROM setting_general
    WHERE id_setting_general = ?
    LIMIT 1
");

if (!$stmt_check) {
    Response("error", "Gagal menyiapkan query validasi data.");
}

$stmt_check->bind_param("i", $id_setting_general);

if (!$stmt_check->execute()) {
    Response("error", "Gagal menjalankan query validasi data.");
}

$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    Response("error", "Data pengaturan tidak ditemukan.");
}

$stmt_check->close();

// =========================================================
// START TRANSACTION
// =========================================================
mysqli_begin_transaction($Conn);

try {

    // =====================================================
    // UPDATE DATA SETTING GENERAL
    // =====================================================
    $stmt_update = $Conn->prepare("
        UPDATE setting_general SET
            app_name             = ?,
            app_description      = ?,
            app_author           = ?,
            metatag_keyword      = ?,
            metatag_description  = ?,
            company_name         = ?,
            company_address      = ?,
            company_email        = ?,
            company_phone        = ?,
            base_url             = ?,
            environment_status   = ?,
            configuration_status = ?
        WHERE id_setting_general = ?
    ");

    if (!$stmt_update) {
        throw new Exception("Gagal menyiapkan query update.");
    }

    $stmt_update->bind_param(
        "ssssssssssssi",
        $app_name,
        $app_description,
        $app_author,
        $metatag_keyword,
        $metatag_description,
        $company_name,
        $company_address,
        $company_email,
        $company_phone,
        $base_url,
        $environment_status,
        $configuration_status,
        $id_setting_general
    );

    if (!$stmt_update->execute()) {
        throw new Exception("Gagal memperbarui data pengaturan umum.");
    }

    $stmt_update->close();

    // =====================================================
    // COMMIT
    // =====================================================
    mysqli_commit($Conn);

    Response("success", "Data pengaturan umum berhasil diperbarui.");

} catch (Exception $e) {

    // =====================================================
    // ROLLBACK
    // =====================================================
    mysqli_rollback($Conn);

    Response("error", $e->getMessage());
}
?>
