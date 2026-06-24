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

$api_key_length = strlen($api_key);
if ($api_key_length <= 4) {
    $api_key_masked = str_repeat('*', max(4, $api_key_length));
} else {
    $api_key_masked = str_repeat('*', $api_key_length);
}
?>
<input type="hidden" id="put_id_setting_wa" name="id_setting_wa" value="<?php echo $id_setting_wa; ?>">

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
        <small class="text-grayish text-long" title="API Key dimasked">
            <?php echo $api_key_masked; ?>
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
<hr>
<?php
    // Persiapan CURL
    $url = "$url_service/api/status";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'x-api-key: ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    // ===============================
    // DECODE RESPONSE
    // ===============================
    $data = json_decode($response, true);

    if (empty($data['success'])) {
        echo '
            <div class="row">
                <div class="alert alert-danger text-center">
                    <small class="text-danger">Gagal memuat data</small>
                    <pre>'.$response.'</pre>
                </div>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $success          = $data['success'];
    $ready            = $data['ready'];
    $phoneNumber = $data['phoneNumber'];
    $profileImg       = $data['profileImg'];
    $qr               = $data['qr'];

    //Apabila Belum Konek
    if(empty($phoneNumber)){
        echo '
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <b class="text-danger">Perangkat Belum Terkoneksi</b><br>
                    Pindai QR Code Berikut ini
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <img src="'.$qr.'" class="" width="80%">
                </div>
            </div>
        ';
    }else{
        echo '
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <b class="text-success">Perangkat Sudah Terkoneksi</b><br>
                    Phone Number : <br>
                    <h3>'.$phoneNumber.'</h3>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12 text-center">
                    <a href="javascript:void(0);" class="button_disconnect" data-id="'.$id_setting_wa.'">
                        <i class="bi bi-plug"></i> Hapus Perangkat
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12 notifikasi_disconnection">
                   
                </div>
            </div>
        ';
    }
?>
