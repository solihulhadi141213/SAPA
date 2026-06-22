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

$url_simrs  = htmlspecialchars($Data['url_simrs']);
$client_id  = htmlspecialchars($Data['client_id']);
$client_key = htmlspecialchars($Data['client_key']);

$status = isset($Data['status']) ? (int) $Data['status'] : 0;

if ($status == 1) {
    $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
} else {
    $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
}

$Qry->close();
?>
<input type="hidden" name="id_setting_simrs" value="<?php echo $id_setting_simrs; ?>">

<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-warning">
            <small>
                Anda akan menghapus data koneksi SIMRS berikut. Tindakan ini tidak dapat dibatalkan.
            </small>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4"><small>URL SIMRS</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish text-long"><?php echo $url_simrs; ?></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4"><small>Client ID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish text-long"><?php echo $client_id; ?></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4"><small>Client Key</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text text-grayish text-long"><?php echo $client_key; ?></small>
    </div>
</div>

<div class="row mb-2">
    <div class="col-4"><small>Status</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <?php echo $label_status; ?>
    </div>
</div>
