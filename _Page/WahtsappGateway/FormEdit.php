<?php
include "../../_Config/Connection.php";
include "../../_Config/Helper.php";
include "../../_Config/Session.php";

date_default_timezone_set("Asia/Jakarta");

if (empty($SessionIdAkses)) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Sesi akses sudah berakhir! Silahkan Login Ulang.
            </small>
        </div>
    ';
    exit;
}

if (empty($_POST['id_setting_wa'])) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Data Whatsapp Gateway belum dipilih.
            </small>
        </div>
    ';
    exit;
}

$id_setting_wa = validateAndSanitizeInput($_POST['id_setting_wa']);

$Qry = $Conn->prepare("
    SELECT
        id_setting_wa,
        url_service,
        api_key,
        status
    FROM setting_wa
    WHERE id_setting_wa = ?
    LIMIT 1
");

if (!$Qry) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Terjadi kesalahan pada saat mempersiapkan query database!<br>
                Keterangan : ' . htmlspecialchars($Conn->error) . '
            </small>
        </div>
    ';
    exit;
}

$Qry->bind_param("i", $id_setting_wa);

if (!$Qry->execute()) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Terjadi kesalahan pada saat membuka data dari database!<br>
                Keterangan : ' . htmlspecialchars($Qry->error) . '
            </small>
        </div>
    ';
    $Qry->close();
    exit;
}

$Result = $Qry->get_result();

if ($Result->num_rows == 0) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Data tidak ditemukan!
            </small>
        </div>
    ';
    $Qry->close();
    exit;
}

$Data = $Result->fetch_assoc();
$Qry->close();

$id_setting_wa = htmlspecialchars($Data['id_setting_wa']);
$url_service   = htmlspecialchars($Data['url_service']);
$api_key       = htmlspecialchars($Data['api_key']);
$status        = isset($Data['status']) ? (int) $Data['status'] : 0;
?>
<input type="hidden" name="id_setting_wa" value="<?php echo $id_setting_wa; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label for="url_service">
            <small>URL Service</small>
        </label>
        <input type="url" class="form-control" name="url_service" id="url_service" value="<?php echo $url_service; ?>" placeholder="https://" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="api_key">
            <small>API Key</small>
        </label>
        <input type="text" class="form-control" name="api_key" id="api_key" value="<?php echo $api_key; ?>" required>
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
