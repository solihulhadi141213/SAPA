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
    $id_akses     = trim($_POST['id_akses'] ?? '');
    $nama_akses   = trim($_POST['nama_akses'] ?? '');
    $kontak_akses = trim($_POST['kontak_akses'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $akses        = trim($_POST['akses'] ?? '');

    // =========================================================
    // VALIDASI MANDATORY
    // =========================================================
    if (empty($id_akses)) {
        Response("error", "ID akses tidak valid.");
    }

    if (empty($nama_akses)) {
        Response("error", "Nama pengguna tidak boleh kosong.");
    }

    if (empty($email)) {
        Response("error", "Alamat email tidak boleh kosong.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response("error", "Format email tidak valid.");
    }

    if (empty($akses)) {
        Response("error", "Level/entitas akses wajib dipilih.");
    }

    // =========================================================
    // VALIDASI DATA AKSES
    // =========================================================
    $stmt_old = $Conn->prepare("
        SELECT 
            id_akses,
            email_akses,
            kontak_akses,
            akses
        FROM akses
        WHERE id_akses = ?
        LIMIT 1
    ");

    $stmt_old->bind_param("i", $id_akses);

    if (!$stmt_old->execute()) {
        Response("error", "Gagal membuka data akses.");
    }

    $result_old = $stmt_old->get_result();

    if ($result_old->num_rows == 0) {
        Response("error", "Data akses tidak ditemukan.");
    }

    $old = $result_old->fetch_assoc();

    $old_email              = $old['email_akses'];
    $old_kontak             = $old['kontak_akses'];

    $stmt_old->close();

    // =========================================================
    // VALIDASI EMAIL DUPLIKAT
    // HANYA JIKA EMAIL BERUBAH
    // =========================================================
    if ($email != $old_email) {

        $stmt_email = $Conn->prepare("
            SELECT id_akses
            FROM akses
            WHERE email_akses = ?
            AND id_akses != ?
            LIMIT 1
        ");

        $stmt_email->bind_param("si", $email, $id_akses);

        $stmt_email->execute();

        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            Response("error", "Alamat email sudah digunakan.");
        }

        $stmt_email->close();
    }

    // =========================================================
    // VALIDASI KONTAK DUPLIKAT
    // HANYA JIKA KONTAK BERUBAH
    // =========================================================
    if (!empty($kontak_akses) && $kontak_akses != $old_kontak) {

        $stmt_kontak = $Conn->prepare("
            SELECT id_akses
            FROM akses
            WHERE kontak_akses = ?
            AND id_akses != ?
            LIMIT 1
        ");

        $stmt_kontak->bind_param("si", $kontak_akses, $id_akses);

        $stmt_kontak->execute();

        $result_kontak = $stmt_kontak->get_result();

        if ($result_kontak->num_rows > 0) {
            Response("error", "Nomor kontak sudah digunakan.");
        }

        $stmt_kontak->close();
    }

    // =========================================================
    // DATETIME UPDATE
    // =========================================================
    $datetime_update = date('Y-m-d H:i:s');

    // =========================================================
    // START TRANSACTION
    // =========================================================
    mysqli_begin_transaction($Conn);

    try {

        // =====================================================
        // UPDATE DATA AKSES
        // =====================================================
        $stmt_update = $Conn->prepare("
            UPDATE akses SET
                nama_akses      = ?,
                kontak_akses    = ?,
                email_akses     = ?,
                akses           = ?,
                datetime_update = ?
            WHERE id_akses = ?
        ");

        $stmt_update->bind_param(
            "sssssi",
            $nama_akses,
            $kontak_akses,
            $email,
            $akses,
            $datetime_update,
            $id_akses
        );

        if (!$stmt_update->execute()) {
            throw new Exception("Gagal update data akses.");
        }

        $stmt_update->close();

        // =====================================================
        // COMMIT
        // =====================================================
        mysqli_commit($Conn);

        Response("success", "Data akses pengguna berhasil diperbarui.");

    } catch (Exception $e) {

        // =====================================================
        // ROLLBACK
        // =====================================================
        mysqli_rollback($Conn);

        Response("error", $e->getMessage());
    }
?>