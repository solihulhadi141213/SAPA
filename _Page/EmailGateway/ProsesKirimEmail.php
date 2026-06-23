<?php
    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/helper.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if(empty($SessionIdAkses)){
        echo '
            <div class="alert alert-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</div>
        ';
        exit;
    }

    if(empty($_POST['id_setting_email_gateway'])){
        echo '
            <div class="alert alert-danger">Tidak ada pengaturan email gateway yang anda pilih!</div>
        ';
        exit;
    }

    if(empty($_POST['nama_penerima'])){
        echo '
            <div class="alert alert-danger">Nama Penerima Tidak Boleh Kosong!</div>
        ';
        exit;
    }

    if(empty($_POST['email_tujuan'])){
        echo '
            <div class="alert alert-danger">Alamat Email Tujuan Tidak Boleh Kosong!</div>
        ';
        exit;
    }

    if(empty($_POST['subject'])){
        echo '
            <div class="alert alert-danger">Subject Pesan Tidak Boleh Kosong!</div>
        ';
        exit;
    }

    if(empty($_POST['pesan'])){
        echo '
            <div class="alert alert-danger">Isi Pesan Tidak Boleh Kosong!</div>
        ';
        exit;
    }

    // Ambil Data
    $id_setting_email_gateway = validateAndSanitizeInput($_POST['id_setting_email_gateway']);
    $nama_penerima            = validateAndSanitizeInput($_POST['nama_penerima']);
    $email_tujuan             = validateAndSanitizeInput($_POST['email_tujuan']);
    $subject                  = validateAndSanitizeInput($_POST['subject']);
    $pesan                    = validateAndSanitizeInput($_POST['pesan']);

    // Buka Pengaturan Email Gateway
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

    // Susun Payload
    $payload = [
        "subjek"               => $subject,
        "email_asal"           => $email_gateway,
        "password_email_asal"  => $password_gateway,
        "url_provider"         => $url_provider,
        "nama_pengirim"        => $nama_pengirim,
        "email_tujuan"         => $email_tujuan,
        "nama_tujuan"          => $nama_penerima,
        "pesan"                => $pesan,
        "port"                 => $port_gateway
    ];

    // Log Awal
    $log  = "=== TEST EMAIL GATEWAY ===\n";
    $log .= "URL Service : ".$url_service."\n";
    $log .= "SMTP Host   : ".$url_provider."\n";
    $log .= "Port        : ".$port_gateway."\n";
    $log .= "Pengirim    : ".$email_gateway."\n";
    $log .= "Penerima    : ".$email_tujuan."\n";
    $log .= "----------------------------------------\n";

    // CURL
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url_service,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Error CURL
    if(!empty($error)){

        $log .= "STATUS      : GAGAL\n";
        $log .= "HTTP CODE   : ".$httpCode."\n";
        $log .= "CURL ERROR  : ".$error."\n";

        exit($log);
    }

    // Decode Response
    $result = json_decode($response,true);

    $log .= "STATUS      : RESPONSE DITERIMA\n";
    $log .= "HTTP CODE   : ".$httpCode."\n";

    if(is_array($result)){

        if(!empty($result['code'])){
            $log .= "CODE        : ".$result['code']."\n";
        }

        if(!empty($result['pesan'])){
            $log .= "PESAN       : ".$result['pesan']."\n";
        }

        $log .= "----------------------------------------\n";
        $log .= "RAW RESPONSE:\n";
        $log .= json_encode(
            $result,
            JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE
        );

    }else{

        $log .= "RAW RESPONSE:\n";
        $log .= $response;
    }

    echo $log;
?>