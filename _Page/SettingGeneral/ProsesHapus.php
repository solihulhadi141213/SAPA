<?php
header('Content-Type: application/json');

// Connection, Function dan Session
include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

// =====================================================
// RESPONSE DEFAULT
// =====================================================
$response = [
    'status'  => 'error',
    'message' => 'Terjadi kesalahan'
];

// =====================================================
// VALIDASI SESSION
// =====================================================
if (empty($SessionIdAkses)) {
    $response['message'] = 'Sesi akses sudah berakhir. Silahkan login ulang.';
    echo json_encode($response);
    exit;
}

// =====================================================
// VALIDASI ID SETTING GENERAL
// =====================================================
if (empty($_POST['id_setting_general'])) {
    $response['message'] = 'ID setting general tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

$id_setting_general = validateAndSanitizeInput($_POST['id_setting_general']);

// =====================================================
// CEK DATA SETTING GENERAL
// =====================================================
$Qry = $Conn->prepare("
    SELECT
        id_setting_general,
        app_name,
        app_icon,
        company_logo
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

$app_name      = $Data['app_name'];
$app_icon      = $Data['app_icon'];
$company_logo   = $Data['company_logo'];

$Qry->close();

// =====================================================
// TRANSACTION
// =====================================================
mysqli_begin_transaction($Conn);

try {

    // =================================================
    // HAPUS DATA SETTING GENERAL
    // =================================================
    $Delete = $Conn->prepare("
        DELETE FROM setting_general
        WHERE id_setting_general = ?
    ");

    if (!$Delete) {
        throw new Exception('Gagal mempersiapkan query hapus.');
    }

    $Delete->bind_param("i", $id_setting_general);

    if (!$Delete->execute()) {
        throw new Exception('Gagal menghapus data setting general.');
    }

    $Delete->close();

    // =================================================
    // HAPUS FILE APP ICON
    // =================================================
    if (!empty($app_icon)) {
        $app_icon_path = "../../assets/img/logo/" . $app_icon;
        if (file_exists($app_icon_path) && is_file($app_icon_path)) {
            @unlink($app_icon_path);
        }
    }

    // =================================================
    // HAPUS FILE COMPANY LOGO
    // =================================================
    if (!empty($company_logo)) {
        $company_logo_path = "../../assets/img/logo/" . $company_logo;
        if (file_exists($company_logo_path) && is_file($company_logo_path)) {
            @unlink($company_logo_path);
        }
    }

    // =================================================
    // COMMIT
    // =================================================
    mysqli_commit($Conn);

    $response = [
        'status'  => 'success',
        'message' => 'Data pengaturan umum "' . $app_name . '" berhasil dihapus.'
    ];
} catch (Exception $e) {

    mysqli_rollback($Conn);

    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
