<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

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
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {
        Response("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $id_akses        = trim($_POST['id_akses'] ?? '');
    $password_1      = trim($_POST['password_edit_1'] ?? '');
    $password_2      = trim($_POST['password_edit_2'] ?? '');

    // =========================================================
    // VALIDASI ID AKSES
    // =========================================================
    if (empty($id_akses)) {
        Response("error", "ID akses tidak valid.");
    }

    // =========================================================
    // VALIDASI PASSWORD
    // =========================================================
    if (empty($password_1)) {
        Response("error", "Password baru tidak boleh kosong.");
    }

    if (empty($password_2)) {
        Response("error", "Ulangi password tidak boleh kosong.");
    }

    // =========================================================
    // VALIDASI PANJANG PASSWORD
    // =========================================================
    $password_length = strlen($password_1);

    if ($password_length < 6) {
        Response("error", "Password minimal 6 karakter.");
    }

    if ($password_length > 20) {
        Response("error", "Password maksimal 20 karakter.");
    }

    // =========================================================
    // VALIDASI KONFIRMASI PASSWORD
    // =========================================================
    if ($password_1 != $password_2) {
        Response("error", "Konfirmasi password tidak sesuai.");
    }

    // =========================================================
    // VALIDASI DATA AKSES
    // =========================================================
    $stmt_check = $Conn->prepare("
        SELECT id_akses
        FROM akses
        WHERE id_akses = ?
        LIMIT 1
    ");

    if (!$stmt_check) {
        Response("error", "Gagal mempersiapkan query database.");
    }

    $stmt_check->bind_param("i", $id_akses);

    if (!$stmt_check->execute()) {
        Response("error", "Gagal membuka data akses.");
    }

    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        Response("error", "Data akses tidak ditemukan.");
    }

    $stmt_check->close();

    // =========================================================
    // HASH PASSWORD
    // =========================================================
    $password_hash = password_hash($password_1, PASSWORD_DEFAULT);

    // =========================================================
    // DATETIME UPDATE
    // =========================================================
    $datetime_update = date('Y-m-d H:i:s');

    // =========================================================
    // UPDATE PASSWORD
    // =========================================================
    $stmt_update = $Conn->prepare("
        UPDATE akses SET
            password        = ?,
            datetime_update = ?
        WHERE id_akses = ?
    ");

    if (!$stmt_update) {
        Response("error", "Gagal mempersiapkan query update.");
    }

    $stmt_update->bind_param(
        "ssi",
        $password_hash,
        $datetime_update,
        $id_akses
    );

    if (!$stmt_update->execute()) {
        Response("error", "Gagal memperbarui password.");
    }

    $stmt_update->close();

    // =========================================================
    // RESPONSE SUCCESS
    // =========================================================
    Response("success", "Password berhasil diperbarui.");
?>