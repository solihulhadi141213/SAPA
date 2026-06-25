<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    function ResponseJson($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    if (empty($SessionIdAkses)) {
        ResponseJson("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    $id_respondent        = trim($_POST['id_respondent'] ?? '');
    $id_pasien            = trim($_POST['id_pasien'] ?? '');
    $id_kunjungan         = trim($_POST['id_kunjungan'] ?? '');
    $respondent_name      = trim($_POST['respondent_name'] ?? '');
    $respondent_sex       = trim($_POST['respondent_sex'] ?? '');
    $respondent_brithdate = trim($_POST['respondent_brithdate'] ?? '');
    $tanggal_kunjungan    = trim($_POST['tanggal_kunjungan'] ?? '');
    $kunjungan_tujuan     = trim($_POST['kunjungan_tujuan'] ?? '');
    $no_kontak     = trim($_POST['no_kontak'] ?? '');

    if ($id_respondent === '') {
        ResponseJson("error", "ID responden tidak valid.");
    }
    if (!filter_var($id_respondent, FILTER_VALIDATE_INT) || (int)$id_respondent <= 0) {
        ResponseJson("error", "ID responden tidak valid.");
    }
    if ($id_pasien === '') {
        ResponseJson("error", "Nomor rekam medis tidak boleh kosong.");
    }
    if ($id_kunjungan === '') {
        ResponseJson("error", "ID kunjungan tidak boleh kosong.");
    }
    if ($respondent_name === '') {
        ResponseJson("error", "Nama responden tidak boleh kosong.");
    }
    if ($respondent_sex === '') {
        ResponseJson("error", "Jenis kelamin responden wajib dipilih.");
    }
    if ($tanggal_kunjungan === '') {
        ResponseJson("error", "Tanggal kunjungan tidak boleh kosong.");
    }
    if ($kunjungan_tujuan === '') {
        ResponseJson("error", "Tujuan kunjungan tidak boleh kosong.");
    }

    if (!filter_var($id_pasien, FILTER_VALIDATE_INT) || (int)$id_pasien <= 0) {
        ResponseJson("error", "Format nomor rekam medis tidak valid.");
    }
    if (!filter_var($id_kunjungan, FILTER_VALIDATE_INT) || (int)$id_kunjungan <= 0) {
        ResponseJson("error", "Format ID kunjungan tidak valid.");
    }

    if ($respondent_sex === "Laki-laki") {
        $respondent_sex = "Male";
    } elseif ($respondent_sex === "Perempuan") {
        $respondent_sex = "Female";
    }

    if (!in_array($respondent_sex, ["Male", "Female"], true)) {
        ResponseJson("error", "Format jenis kelamin tidak valid.");
    }

    if (!in_array($kunjungan_tujuan, ["Rajal", "Ranap"], true)) {
        ResponseJson("error", "Format tujuan kunjungan tidak valid.");
    }

    $date_kunjungan_obj = DateTime::createFromFormat('Y-m-d\TH:i', $tanggal_kunjungan);
    if ($date_kunjungan_obj && $date_kunjungan_obj->format('Y-m-d\TH:i') === $tanggal_kunjungan) {
        $tanggal_kunjungan = $date_kunjungan_obj->format('Y-m-d H:i:00');
    } else {
        $date_kunjungan_obj = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal_kunjungan);
        if ($date_kunjungan_obj && $date_kunjungan_obj->format('Y-m-d H:i:s') === $tanggal_kunjungan) {
            $tanggal_kunjungan = $date_kunjungan_obj->format('Y-m-d H:i:s');
        } else {
            ResponseJson("error", "Format tanggal kunjungan tidak valid.");
        }
    }

    if ($respondent_brithdate !== '') {
        $birthdate_obj = DateTime::createFromFormat('Y-m-d', $respondent_brithdate);
        if (!$birthdate_obj || $birthdate_obj->format('Y-m-d') !== $respondent_brithdate) {
            ResponseJson("error", "Format tanggal lahir tidak valid.");
        }
        $respondent_brithdate = $birthdate_obj->format('Y-m-d');
    } else {
        $respondent_brithdate = null;
    }

    $id_respondent = (int)$id_respondent;
    $id_pasien     = (int)$id_pasien;
    $id_kunjungan  = (int)$id_kunjungan;

    $stmt_old = $Conn->prepare("SELECT id_respondent FROM respondent WHERE id_respondent = ? LIMIT 1");
    if (!$stmt_old) {
        ResponseJson("error", "Gagal mempersiapkan validasi data.");
    }
    $stmt_old->bind_param("i", $id_respondent);
    if (!$stmt_old->execute()) {
        $stmt_old->close();
        ResponseJson("error", "Gagal membuka data responden.");
    }
    $result_old = $stmt_old->get_result();
    if ($result_old->num_rows == 0) {
        $stmt_old->close();
        ResponseJson("error", "Data responden tidak ditemukan.");
    }
    $stmt_old->close();

    $stmt_duplicate = $Conn->prepare("
        SELECT id_respondent
        FROM respondent
        WHERE id_kunjungan = ? AND id_respondent <> ?
        LIMIT 1
    ");
    if (!$stmt_duplicate) {
        ResponseJson("error", "Gagal mempersiapkan validasi duplikasi.");
    }
    $stmt_duplicate->bind_param("ii", $id_kunjungan, $id_respondent);
    $stmt_duplicate->execute();
    $result_duplicate = $stmt_duplicate->get_result();
    if ($result_duplicate->num_rows > 0) {
        $stmt_duplicate->close();
        ResponseJson("error", "ID kunjungan sudah digunakan oleh data responden lain.");
    }
    $stmt_duplicate->close();

    $stmt_update = $Conn->prepare("
        UPDATE respondent
        SET
            id_pasien = ?,
            id_kunjungan = ?,
            respondent_name = ?,
            respondent_sex = ?,
            respondent_brithdate = ?,
            tanggal_kunjungan = ?,
            kunjungan_tujuan = ?,
            no_kontak = ?
        WHERE id_respondent = ?
    ");
    if (!$stmt_update) {
        ResponseJson("error", "Gagal mempersiapkan query update.");
    }

    $stmt_update->bind_param(
        "iissssssi",
        $id_pasien,
        $id_kunjungan,
        $respondent_name,
        $respondent_sex,
        $respondent_brithdate,
        $tanggal_kunjungan,
        $kunjungan_tujuan,
        $no_kontak,
        $id_respondent
    );

    if (!$stmt_update->execute()) {
        $stmt_update->close();
        ResponseJson("error", "Gagal menyimpan perubahan data responden.");
    }

    $stmt_update->close();

    ResponseJson("success", "Data responden berhasil diperbarui.");
?>
