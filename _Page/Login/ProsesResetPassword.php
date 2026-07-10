<?php
    // Set header agar selalu mengembalikan JSON
    header('Content-Type: application/json');

    // Tambahkan beberapa header keamanan
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');

    // Start session
    session_start();

    // Zona waktu
    date_default_timezone_set('Asia/Jakarta');

    // Connection dan function
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Setting.php";

    // VALIDASI INPUT MANDATORY
    if (empty($_POST["email"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST["captcha"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kode Captcha Tidak Boleh Kosong!'
        ]);
        exit;
    }

    $email      = trim($_POST["email"]);
    $captcha    = trim($_POST["captcha"]);

    // VALIDASI CAPTCHA
    if (empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Captcha Invalid!'
        ]);
        exit;
    }

    // VALIDASI FORMAT EMAIL
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format Email Tidak Valid!'
        ]);
        exit;
    }

    // AMBIL USER BERDASARKAN EMAIL
    $queryAkses = $Conn->prepare("SELECT * FROM akses WHERE email_akses = ? LIMIT 1");
    $queryAkses->bind_param("s", $email);
    $queryAkses->execute();
    $resultAkses = $queryAkses->get_result();
    $DataAkses = $resultAkses->fetch_assoc();

    if (!$DataAkses) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email Invalid!'
        ]);
        exit;
    }

    $id_akses   = $DataAkses['id_akses'];
    $nama_akses = $DataAkses['nama_akses'];

    // HAPUS TOKEN LAMA
    $deleteTokenStmt = $Conn->prepare("DELETE FROM akses_reset WHERE id_akses = ?");
    $deleteTokenStmt->bind_param("i", $id_akses);
    $deleteTokenStmt->execute();

    // GENERATE TOKEN BARU
    $utc = new DateTime('now', new DateTimeZone('UTC'));
    $current_utc = $utc->format('Y-m-d H:i:s');

    // Tambah 1 jam
    $expired = clone $utc;
    $expired->modify('+1 hour');
    $date_expired = $expired->format('Y-m-d H:i:s');
    $token = GenerateToken(36);

    // Simpan Token Baru Ke Database
    $insertTokenStmt = $Conn->prepare("
        INSERT INTO akses_reset (
            id_akses,
            datetime_creat, 
            datetime_expired, 
            token
        ) VALUES (?, ?, ?, ?)
    ");

    $insertTokenStmt->bind_param(
        "isss",
        $id_akses,
        $current_utc,
        $date_expired,
        $token
    );

    if (!$insertTokenStmt->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan token login!'
        ]);
        exit;
    }
    unset($_SESSION['captcha']);

    // Link Tautan
    $link_tautan = "$base_url/ResetPassword.php?token=$token";

    // Kirim Email Tautan
    // Buka Pengaturan Email Gateway
    $status = 1;
    $Qry = $Conn->prepare("SELECT * FROM setting_email_gateway WHERE status = ? LIMIT 1");
    if (!$Qry) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada saat mempersiapkan query database! Keterangan : ' . htmlspecialchars($Conn->error) . ''
        ]);
        exit;
    }

    $Qry->bind_param("i", $status);

    if (!$Qry->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada saat mempersiapkan query database! Keterangan : ' . htmlspecialchars($Conn->error) . ''
        ]);
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Pengaturan Email Gateway Tidak Ditemukan'
        ]);
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

    // Buat Subject
    $subject = "Reset Password";
    $pesan = '
        Kepada Yth. <b>'.$nama_akses.'</b>
        Berikut ini adalah link tautan untuk mengubah password anda : <br>
        <a href="'.$link_tautan.'">'.$link_tautan.'</a>
        <p>
            Apabila anda tidak merasa meminta reset password. Silahkan abaikan pesan ini.
        </p>
    ';

    // Susun Payload
    $payload = [
        "subjek"               => $subject,
        "email_asal"           => $email_gateway,
        "password_email_asal"  => $password_gateway,
        "url_provider"         => $url_provider,
        "nama_pengirim"        => $nama_pengirim,
        "email_tujuan"         => $email,
        "nama_tujuan"          => $nama_akses,
        "pesan"                => $pesan,
        "port"                 => $port_gateway
    ];

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
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada saat mengirim tautan ke email anda. Keterangan : '.$error.''
        ]);
        exit;
    }
    
    // Decode Response
    $result = json_decode($response,true);

    // Response Success
    echo json_encode([
        'status' => 'success',
        'message' => 'Reset Password Berhasil. Kami telah mengirimkan tautan reset passwprd ke email anda'
    ]);
    exit;
?>