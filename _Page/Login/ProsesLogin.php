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

    // VALIDASI INPUT MANDATORY
    if (empty($_POST["email"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST["password"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password Tidak Boleh Kosong!'
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
    $password   = trim($_POST["password"]);
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

    // VERIFIKASI PASSWORD HASH
    if (!password_verify($password, $DataAkses['password'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password Invalid!'
        ]);
        exit;
    }

    $id_akses = $DataAkses['id_akses'];

    // HAPUS TOKEN LAMA
    $deleteTokenStmt = $Conn->prepare("DELETE FROM akses_login WHERE id_akses = ?");
    $deleteTokenStmt->bind_param("i", $id_akses);
    $deleteTokenStmt->execute();

    // GENERATE TOKEN BARU
    $utc = new DateTime('now', new DateTimeZone('UTC'));
    $current_utc = $utc->format('Y-m-d H:i:s');
    $expired_seconds = 60 * 60; // 1 jam
    $date_expired = date(
        'Y-m-d H:i:s',
        strtotime($current_utc) + $expired_seconds
    );
    $token = GenerateToken(36);

    // Simpan Token Baru Ke Database
    $insertTokenStmt = $Conn->prepare("
        INSERT INTO akses_login (
            id_akses,
            token,
            date_creat,
            date_expired
        ) VALUES (?, ?, ?, ?)
    ");

    $insertTokenStmt->bind_param(
        "isss",
        $id_akses,
        $token,
        $current_utc,
        $date_expired
    );

    if (!$insertTokenStmt->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan token login!'
        ]);
        exit;
    }

    // COOKIE LOGIN
    $cookieOptions = [
        'expires'  => time() + $expired_seconds,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    setcookie("id_akses", $id_akses, $cookieOptions);
    setcookie("login_token", $token, $cookieOptions);

    // Set Session
    $_SESSION['NotifikasiSwal'] = "Login Berhasil";

    // Response Success
    echo json_encode([
        'status' => 'success',
        'message' => 'Login Berhasil'
    ]);
    exit;
?>