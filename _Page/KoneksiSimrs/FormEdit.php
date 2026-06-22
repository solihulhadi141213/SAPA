<?php
// koneksi dan session
include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

if (empty($SessionIdAkses)) {
    echo '
        <div class="row mb-3">
            <div class="col-12 text-center">
                <div class="alert alert-danger">
                    <small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
                </div>
            </div>
        </div>
    ';
    exit;
}

if (empty($_POST['id_setting_simrs'])) {
    echo '
        <div class="row mb-3">
            <div class="col-12 text-center">
                <div class="alert alert-danger">
                    <small>ID koneksi SIMRS tidak boleh kosong!</small>
                </div>
            </div>
        </div>
    ';
    exit;
}

$id_setting_simrs = validateAndSanitizeInput($_POST['id_setting_simrs']);

$Qry = $Conn->prepare("
    SELECT
        id_setting_simrs,
        url_simrs,
        client_id,
        client_key,
        status
    FROM setting_simrs
    WHERE id_setting_simrs = ?
    LIMIT 1
");

if (!$Qry) {
    echo '
        <div class="alert alert-danger">
            <small>Terjadi kesalahan pada saat mempersiapkan query database!</small>
        </div>
    ';
    exit;
}

$Qry->bind_param("i", $id_setting_simrs);

if (!$Qry->execute()) {
    echo '
        <div class="alert alert-danger">
            <small>Terjadi kesalahan pada saat membuka data dari database!</small>
        </div>
    ';
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    echo '
        <div class="alert alert-danger">
            <small>Data koneksi SIMRS tidak ditemukan!</small>
        </div>
    ';
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();

$url_simrs   = htmlspecialchars($Data['url_simrs']);
$client_id   = htmlspecialchars($Data['client_id']);
$client_key  = htmlspecialchars($Data['client_key']);
$status      = isset($Data['status']) ? (int) $Data['status'] : 0;

$Qry->close();
?>
<input type="hidden" name="id_setting_simrs" value="<?php echo $id_setting_simrs; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label for="url_simrs">
            <small>URL SIMRS</small>
        </label>
        <input type="url" class="form-control" name="url_simrs" id="url_simrs" value="<?php echo $url_simrs; ?>" placeholder="https://" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="client_id">
            <small>Client ID</small>
        </label>
        <input type="text" class="form-control" name="client_id" id="client_id" value="<?php echo $client_id; ?>" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="client_key">
            <small>Client Key</small>
        </label>
        <input type="text" class="form-control" name="client_key" id="client_key" value="<?php echo $client_key; ?>" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="status">
            <small>Status Connection</small>
        </label>
        <select name="status" id="status" class="form-control">
            <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inactive</option>
            <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Active</option>
        </select>
    </div>
</div>
