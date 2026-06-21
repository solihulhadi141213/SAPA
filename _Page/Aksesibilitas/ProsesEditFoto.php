<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // RESPONSE FUNCTION
    // =========================================================
    function Response($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        Response("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    // =========================================================
    // VALIDASI ID AKSES
    // =========================================================
    $id_akses = trim($_POST['id_akses'] ?? '');

    if (empty($id_akses)) {
        Response("error", "ID akses tidak valid.");
    }

    // =========================================================
    // VALIDASI FILE
    // =========================================================
    if (!isset($_FILES['image_akses'])) {
        Response("error", "File foto belum dipilih.");
    }

    $file = $_FILES['image_akses'];

    // =========================================================
    // VALIDASI ERROR FILE
    // =========================================================
    if ($file['error'] !== UPLOAD_ERR_OK) {

        switch ($file['error']) {

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                Response("error", "Ukuran file melebihi batas maksimum.");

            case UPLOAD_ERR_PARTIAL:
                Response("error", "File gagal diupload secara sempurna.");

            case UPLOAD_ERR_NO_FILE:
                Response("error", "File foto belum dipilih.");

            default:
                Response("error", "Terjadi kesalahan upload file.");
        }
    }

    // =========================================================
    // VALIDASI SIZE
    // =========================================================
    $max_size = 2 * 1024 * 1024;

    if ($file['size'] > $max_size) {
        Response("error", "Ukuran file maksimal 2 MB.");
    }

    // =========================================================
    // VALIDASI MIME TYPE
    // =========================================================
    $allowed_mime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!array_key_exists($mime, $allowed_mime)) {
        Response("error", "Format file tidak didukung.");
    }

    // =========================================================
    // VALIDASI DATA AKSES
    // =========================================================
    $stmt = $Conn->prepare("
        SELECT 
            id_akses,
            image_akses
        FROM akses
        WHERE id_akses = ?
        LIMIT 1
    ");

    if (!$stmt) {
        Response("error", "Gagal mempersiapkan query database.");
    }

    $stmt->bind_param("i", $id_akses);

    if (!$stmt->execute()) {
        Response("error", "Gagal membuka data akses.");
    }

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        Response("error", "Data akses tidak ditemukan.");
    }

    $data = $result->fetch_assoc();

    $old_image = $data['image_akses'];

    $stmt->close();

    // =========================================================
    // GENERATE FILE NAME
    // =========================================================
    $extension  = $allowed_mime[$mime];
    $new_name   = md5(uniqid(rand(), true)) . '.' . $extension;

    // =========================================================
    // PATH FILE
    // =========================================================
    $upload_dir = "../../assets/img/user/";

    // Pastikan folder ada
    if (!is_dir($upload_dir)) {

        if (!mkdir($upload_dir, 0777, true)) {
            Response("error", "Gagal membuat direktori upload.");
        }
    }

    $upload_path = $upload_dir . $new_name;

    // =========================================================
    // UPLOAD FILE
    // =========================================================
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        Response("error", "Gagal menyimpan file upload.");
    }

    // =========================================================
    // DATETIME UPDATE
    // =========================================================
    $datetime_update = date('Y-m-d H:i:s');

    // =========================================================
    // UPDATE DATABASE
    // =========================================================
    $stmt_update = $Conn->prepare("
        UPDATE akses SET
            image_akses     = ?,
            datetime_update = ?
        WHERE id_akses = ?
    ");

    if (!$stmt_update) {

        // Hapus file baru jika query gagal
        @unlink($upload_path);

        Response("error", "Gagal mempersiapkan query update.");
    }

    $stmt_update->bind_param(
        "ssi",
        $new_name,
        $datetime_update,
        $id_akses
    );

    if (!$stmt_update->execute()) {

        // Hapus file baru jika gagal update
        @unlink($upload_path);

        Response("error", "Gagal memperbarui foto profil.");
    }

    $stmt_update->close();

    // =========================================================
    // HAPUS FILE LAMA
    // =========================================================
    if (!empty($old_image)) {

        $old_path = $upload_dir . $old_image;

        if (file_exists($old_path)) {
            @unlink($old_path);
        }
    }

    // =========================================================
    // RESPONSE SUCCESS
    // =========================================================
    Response("success", "Foto profil berhasil diperbarui.");
?>