<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION, HELPER, SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // RESPONSE HELPER
    // =========================================================
    function ResponseJson($status, $message){
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
        ResponseJson("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    // =========================================================
    // AMBIL DATA
    // =========================================================
    $id_pasien            = trim($_POST['id_pasien'] ?? '');
    $id_kunjungan         = trim($_POST['id_kunjungan'] ?? '');
    $respondent_name      = trim($_POST['respondent_name'] ?? ($_POST['nama_pasien'] ?? ''));
    $respondent_sex       = trim($_POST['respondent_sex'] ?? ($_POST['gender'] ?? ''));
    $respondent_brithdate = trim($_POST['respondent_brithdate'] ?? '');
    $tanggal_kunjungan    = trim($_POST['tanggal_kunjungan'] ?? '');
    $kunjungan_tujuan    = trim($_POST['kunjungan_tujuan'] ?? '');
    $no_kontak    = trim($_POST['no_kontak'] ?? '');

    // =========================================================
    // VALIDASI MANDATORI
    // =========================================================
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

    // =========================================================
    // VALIDASI FORMAT DATA
    // =========================================================
    if (!filter_var($id_pasien, FILTER_VALIDATE_INT) || (int)$id_pasien <= 0) {
        ResponseJson("error", "Format nomor rekam medis tidak valid.");
    }

    if (!filter_var($id_kunjungan, FILTER_VALIDATE_INT) || (int)$id_kunjungan <= 0) {
        ResponseJson("error", "Format ID kunjungan tidak valid.");
    }

    $allowedSex = ["Male", "Female"];
    if ($respondent_sex === "Laki-laki") {
        $respondent_sex = "Male";
    } elseif ($respondent_sex === "Perempuan") {
        $respondent_sex = "Female";
    }

    if (!in_array($respondent_sex, $allowedSex, true)) {
        ResponseJson("error", "Format jenis kelamin tidak valid.");
    }

    $allowedTujuan = ["Rajal", "Ranap"];
    if (!in_array($kunjungan_tujuan, $allowedTujuan, true)) {
        ResponseJson("error", "Format tujuan kunjungan tidak valid.");
    }

    $date_kunjungan_obj = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal_kunjungan);
    if (!$date_kunjungan_obj || $date_kunjungan_obj->format('Y-m-d H:i:s') !== $tanggal_kunjungan) {
        $date_kunjungan_obj = DateTime::createFromFormat('Y-m-d', $tanggal_kunjungan);
        if (!$date_kunjungan_obj) {
            ResponseJson("error", "Format tanggal kunjungan tidak valid.");
        }
        $tanggal_kunjungan = $date_kunjungan_obj->format('Y-m-d 00:00:00');
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

    $id_pasien    = (int)$id_pasien;
    $id_kunjungan = (int)$id_kunjungan;

    // =========================================================
    // CEK DUPLIKASI BERDASARKAN ID KUNJUNGAN
    // =========================================================
    $stmt_duplicate = $Conn->prepare("
        SELECT id_respondent
        FROM respondent
        WHERE id_kunjungan = ?
        LIMIT 1
    ");

    if (!$stmt_duplicate) {
        ResponseJson("error", "Gagal mempersiapkan validasi duplikasi.");
    }

    $stmt_duplicate->bind_param("i", $id_kunjungan);
    $stmt_duplicate->execute();
    $result_duplicate = $stmt_duplicate->get_result();

    if ($result_duplicate->num_rows > 0) {
        $stmt_duplicate->close();
        ResponseJson("error", "Data responden untuk ID kunjungan tersebut sudah ada.");
    }

    $stmt_duplicate->close();

    // =========================================================
    // SIMPAN DATA
    // =========================================================
    $stmt_insert = $Conn->prepare("
        INSERT INTO respondent (
            id_pasien,
            id_kunjungan,
            respondent_name,
            respondent_sex,
            respondent_brithdate,
            tanggal_kunjungan,
            kunjungan_tujuan,
            no_kontak
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    if (!$stmt_insert) {
        ResponseJson("error", "Gagal mempersiapkan query penyimpanan.");
    }

    $stmt_insert->bind_param(
        "iissssss",
        $id_pasien,
        $id_kunjungan,
        $respondent_name,
        $respondent_sex,
        $respondent_brithdate,
        $tanggal_kunjungan,
        $kunjungan_tujuan,
        $no_kontak
    );

    if (!$stmt_insert->execute()) {
        $stmt_insert->close();
        ResponseJson("error", "Gagal menyimpan data responden.");
    }

    $stmt_insert->close();

    ResponseJson("success", "Data responden berhasil ditambahkan.");
?>
