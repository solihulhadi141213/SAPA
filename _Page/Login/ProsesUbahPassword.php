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
    if (empty($_POST["token"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Token Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST["password_1"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password Tidak Boleh Kosong!'
        ]);
        exit;
    }

    if (empty($_POST["password_2"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password Harus Sama!'
        ]);
        exit;
    }

    $token      = trim($_POST["token"]);
    $password_1 = trim($_POST["password_1"]);
    $password_2 = trim($_POST["password_2"]);

    // Password Harus Sama
    if($password_1!==$password_2){
        echo json_encode([
            'status' => 'error',
            'message' => 'Password Harus Sama!'
        ]);
        exit;
    }

    // Validasi Format Password Baru
    $password_length = strlen($password_1);

    if ($password_length < 6) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password minimal 6 karakter.'
        ]);
        exit;
    }

    if ($password_length > 20) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password maksimal 20 karakter.'
        ]);
        exit;
    }

    // Validasi Token
    $token = validateAndSanitizeInput($_POST['token']);

    // Validasi Token
    $id_akses = GetDetailData($Conn, 'akses_reset', 'token', $token, 'id_akses');

    if(empty($id_akses)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Token Tidak Valid!'
        ]);
        exit;
    }

    // Validasi Expired Token
    $datetime_expired = GetDetailData($Conn, 'akses_reset', 'token', $token, 'datetime_expired');
    $now              = new DateTime('now', new DateTimeZone('UTC'));
    $expired          = new DateTime($datetime_expired, new DateTimeZone('UTC'));

    if ($expired < $now) {
        echo '
            <div class="alert alert-danger text-center">Tautan Sudah Expired</div>
        ';
        echo json_encode([
            'status' => 'error',
            'message' => 'Tautan Tang Anda Gunakan Sudah Tidak Berlaku'
        ]);
        exit;
    }

    // AMBIL USER BERDASARKAN EMAIL
    $queryAkses = $Conn->prepare("SELECT * FROM akses WHERE id_akses = ? LIMIT 1");
    $queryAkses->bind_param("i", $id_akses);
    $queryAkses->execute();
    $resultAkses = $queryAkses->get_result();
    $DataAkses = $resultAkses->fetch_assoc();

    if (!$DataAkses) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Akses Pengguna Tidak Ditemukan!'
        ]);
        exit;
    }

    $id_akses   = $DataAkses['id_akses'];
    $nama_akses = $DataAkses['nama_akses'];

    // HASH PASSWORD
    $password_hash = password_hash($password_1, PASSWORD_DEFAULT);

    // Datetime Update
    $datetime_update = date('Y-m-d H:i:s');

    $stmt_update = $Conn->prepare("
        UPDATE akses SET
            password        = ?,
            datetime_update = ?
        WHERE id_akses = ?
    ");

    if (!$stmt_update) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mempersiapkan query update.'
        ]);
        exit;
    }

    $stmt_update->bind_param(
        "ssi",
        $password_hash,
        $datetime_update,
        $id_akses
    );

    if (!$stmt_update->execute()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui password.'
        ]);
        exit;
    }

    $stmt_update->close();

    // HAPUS TOKEN LAMA
    $deleteTokenStmt = $Conn->prepare("DELETE FROM akses_reset WHERE id_akses = ?");
    $deleteTokenStmt->bind_param("i", $id_akses);
    $deleteTokenStmt->execute();

    
    // Response Success
    echo json_encode([
        'status' => 'success',
        'message' => 'Ubah Password Berhasil. Silahkan lakukan login menggunakan password baru.'
    ]);
    exit;
?>