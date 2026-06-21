<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silakan login ulang."
        ]);

        exit;
    }

    // =========================================================
    // FUNCTION RESPONSE
    // =========================================================
    function Response($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $nama_akses   = trim($_POST['nama_akses'] ?? '');
    $kontak_akses = trim($_POST['kontak_akses'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $akses        = trim($_POST['akses'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($nama_akses)) {
        Response("error", "Nama pengguna tidak boleh kosong.");
    }

    if (empty($email)) {
        Response("error", "Email tidak boleh kosong.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response("error", "Format email tidak valid.");
    }

    if (empty($password)) {
        Response("error", "Password tidak boleh kosong.");
    }

    if (strlen($password) < 6) {
        Response("error", "Password minimal 6 karakter.");
    }

    if (empty($akses)) {
        Response("error", "Level/entitas akses wajib dipilih.");
    }

    // =========================================================
    // VALIDASI EMAIL DUPLIKAT
    // =========================================================
    $stmt_email = $Conn->prepare("
        SELECT id_akses 
        FROM akses 
        WHERE email_akses = ?
    ");

    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();

    $result_email = $stmt_email->get_result();

    if ($result_email->num_rows > 0) {
        Response("error", "Alamat email sudah digunakan.");
    }

    $stmt_email->close();

    // =========================================================
    // HANDLE FILE UPLOAD
    // =========================================================
    $image_akses = "";

    if (
        isset($_FILES['image_akses']) &&
        $_FILES['image_akses']['error'] != 4
    ) {

        $file_tmp   = $_FILES['image_akses']['tmp_name'];
        $file_name  = $_FILES['image_akses']['name'];
        $file_size  = $_FILES['image_akses']['size'];
        $file_error = $_FILES['image_akses']['error'];

        // =====================================================
        // VALIDASI ERROR
        // =====================================================
        if ($file_error !== UPLOAD_ERR_OK) {
            Response("error", "Terjadi kesalahan saat upload file.");
        }

        // =====================================================
        // VALIDASI SIZE
        // =====================================================
        $max_size = 2 * 1024 * 1024;

        if ($file_size > $max_size) {
            Response("error", "Ukuran file maksimal 2 MB.");
        }

        // =====================================================
        // VALIDASI MIME TYPE
        // =====================================================
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowedMime = [
            'image/png'  => 'png',
            'image/jpeg' => 'jpg',
            'image/gif'  => 'gif',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowedMime)) {
            Response("error", "Tipe file tidak didukung.");
        }

        // =====================================================
        // GENERATE FILE NAME
        // =====================================================
        $extension = $allowedMime[$mime];

        $image_akses = strtolower(
            bin2hex(random_bytes(16)) . '.' . $extension
        );

        // =====================================================
        // PATH FILE
        // =====================================================
        $upload_dir  = "../../assets/img/user/";
        $upload_path = $upload_dir . $image_akses;

        // =====================================================
        // CEK DIRECTORY
        // =====================================================
        if (!is_dir($upload_dir)) {

            if (!mkdir($upload_dir, 0777, true)) {
                Response("error", "Gagal membuat folder upload.");
            }
        }

        // =====================================================
        // SIMPAN FILE
        // =====================================================
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            Response("error", "Gagal menyimpan file upload.");
        }
    }

    // =========================================================
    // HASH PASSWORD
    // =========================================================
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // =========================================================
    // DATETIME
    // =========================================================
    $datetime = date('Y-m-d H:i:s');

    // =========================================================
    // START TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        // =====================================================
        // INSERT TABEL AKSES
        // =====================================================
        $stmt_insert = $Conn->prepare("
            INSERT INTO akses (
                nama_akses,
                kontak_akses,
                email_akses,
                password,
                image_akses,
                akses,
                datetime_update
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt_insert->bind_param(
            "sssssss",
            $nama_akses,
            $kontak_akses,
            $email,
            $password_hash,
            $image_akses,
            $akses,
            $datetime
        );

        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data akses.");
        }

        $id_akses = $Conn->insert_id;

        $stmt_insert->close();

        // =====================================================
        // COMMIT
        // =====================================================
        mysqli_commit($Conn);

        Response("success", "Data akses pengguna berhasil ditambahkan.");

    } catch (Exception $e) {

        // =====================================================
        // ROLLBACK
        // =====================================================
        mysqli_rollback($Conn);

        // =====================================================
        // HAPUS FILE JIKA ADA
        // =====================================================
        if (!empty($image_akses)) {

            $file_delete = "../../assets/img/user/" . $image_akses;

            if (file_exists($file_delete)) {
                unlink($file_delete);
            }
        }

        Response("error", $e->getMessage());
    }
?>