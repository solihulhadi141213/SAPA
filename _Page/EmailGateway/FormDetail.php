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

$Qry = $Conn->prepare("SELECT * FROM setting_email_gateway WHERE id_setting_email_gateway = ? LIMIT 1");
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
$password_gateway         = htmlspecialchars($Data['password_gateway']);
$url_provider             = htmlspecialchars($Data['url_provider']);
$port_gateway             = htmlspecialchars($Data['port_gateway']);
$nama_pengirim            = htmlspecialchars($Data['nama_pengirim']);
$url_service              = htmlspecialchars($Data['url_service']);
$status                   = (int) $Data['status'];

$panjang_password = strlen($password_gateway);

if ($panjang_password <= 4) {
    $password_gateway_masked = str_repeat('*', max(4, $panjang_password));
} else {
    $password_gateway_masked = str_repeat('*', $panjang_password);
}

if ($status == 1) {
    $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
} else {
    $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
}
?>
<div class="row mb-3">
    <div class="col-4"><small>Email Gateway</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo $email_gateway; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Password</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <div class="d-flex align-items-center gap-2">
            <small class="text-grayish text-long mb-0" id="PasswordGatewayText" data-masked="<?php echo $password_gateway_masked; ?>" data-real="<?php echo $password_gateway; ?>" title="Password dimasked">
                <?php echo $password_gateway_masked; ?>
            </small>
            <a href="javascript:void(0);" class="text-primary" id="TogglePasswordGateway" title="Tampilkan password">
                <i class="bi bi-eye"></i>
            </a>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>URL Provider</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo $url_provider; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Port Gateway</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish">
            <?php echo $port_gateway; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Nama Pengirim</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo $nama_pengirim; ?>
        </small>
    </div>
</div>
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
    <div class="col-4"><small>Status</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <?php echo $label_status; ?>
    </div>
</div>
<script>
(function () {
    const toggle = document.getElementById('TogglePasswordGateway');
    const text = document.getElementById('PasswordGatewayText');

    if (!toggle || !text) {
        return;
    }

    let visible = false;

    toggle.addEventListener('click', function () {
        visible = !visible;
        text.textContent = visible ? text.dataset.real : text.dataset.masked;
        toggle.title = visible ? 'Sembunyikan password' : 'Tampilkan password';
        toggle.innerHTML = visible ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
})();
</script>
