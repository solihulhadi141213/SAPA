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

if (empty($_POST['id_setting_email_gateway'])) {
    echo '
        <div class="alert alert-danger text-center mb-3">
            <small>
                <b>Opss!</b><br>
                Data email gateway belum dipilih.
            </small>
        </div>
    ';
    exit;
}

$id_setting_email_gateway = validateAndSanitizeInput($_POST['id_setting_email_gateway']);

$Qry = $Conn->prepare("
    SELECT
        id_setting_email_gateway,
        email_gateway,
        password_gateway,
        url_provider,
        port_gateway,
        nama_pengirim,
        url_service,
        status
    FROM setting_email_gateway
    WHERE id_setting_email_gateway = ?
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

$Qry->bind_param("i", $id_setting_email_gateway);

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

$id_setting_email_gateway = htmlspecialchars($Data['id_setting_email_gateway']);
$email_gateway            = htmlspecialchars($Data['email_gateway']);
$url_provider             = htmlspecialchars($Data['url_provider']);
$port_gateway             = htmlspecialchars($Data['port_gateway']);
$nama_pengirim            = htmlspecialchars($Data['nama_pengirim']);
$url_service              = htmlspecialchars($Data['url_service']);
$status                   = isset($Data['status']) ? (int) $Data['status'] : 0;
?>
<input type="hidden" name="id_setting_email_gateway" value="<?php echo $id_setting_email_gateway; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label for="email_gateway">
            <small>Email Gateway</small>
        </label>
        <input type="email" class="form-control" name="email_gateway" id="email_gateway" value="<?php echo $email_gateway; ?>" placeholder="your-email@domain.com" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="password_gateway">
            <small>Password Email</small>
        </label>
        <input type="password" class="form-control" name="password_gateway" id="password_gateway" placeholder="Kosongkan jika tidak diubah">
        <small class="text-muted">Password lama tidak ditampilkan. Isi hanya jika ingin mengganti password.</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="url_provider">
            <small>URL Provider</small>
        </label>
        <input type="text" class="form-control" name="url_provider" id="url_provider" value="<?php echo $url_provider; ?>" placeholder="smtp.domain.com atau https://provider.domain.com" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="port_gateway">
            <small>Port</small>
        </label>
        <input type="text" class="form-control" name="port_gateway" id="port_gateway" value="<?php echo $port_gateway; ?>" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="nama_pengirim">
            <small>Nama Pengirim</small>
        </label>
        <input type="text" class="form-control" name="nama_pengirim" id="nama_pengirim" value="<?php echo $nama_pengirim; ?>" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="url_service">
            <small>URL Service (Base URL)</small>
        </label>
        <input type="url" class="form-control" name="url_service" id="url_service" value="<?php echo $url_service; ?>" required>
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
