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
$status        = (int) $Data['status'];

if ($status == 1) {
    $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
} else {
    $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
}
?>
<input type="hidden" name="id_setting_wa" value="<?php echo $id_setting_wa; ?>">
<div class="row mb-3">
    <div class="col-4"><small>URL Service</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo $url_service; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>API Key</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo str_repeat('*', max(4, strlen($api_key))); ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Status</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <?php echo $label_status; ?>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING!</b><br>
                Menghapus pengaturan <b>Whatsapp gateway</b> akan menghapus konfigurasi ini dari sistem.<br>
                <b>Apakah anda yakin akan menghapusnya?</b>
            </small>
        </div>
    </div>
</div>
