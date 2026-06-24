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
            <tr>
                <td colspan="10" class="text-center">
                    <small class="text-danger">Gagal memuat data</small>
                </td>
            </tr>
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
            <hr>
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
            <hr>
            <div class="row mb-2">
                <div class="col-12 text-center">
                    <b class="text-success">Perangkat Sudah Terkoneksi</b><br>
                    Phone Number : '.$phoneNumber.'
                </div>
            </div>
        ';
    }
    
?>