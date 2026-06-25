<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Sesi akses sudah berakhir! Silahkan Login Ulang.
                </small>
            </div>
        ';
        exit;
    }

    // Validasi id_respondent
    if (empty($_POST['id_respondent'])) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    $id_respondent = validateAndSanitizeInput($_POST['id_respondent']);

    if (!filter_var($id_respondent, FILTER_VALIDATE_INT) || (int)$id_respondent <= 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    ID responden tidak valid.
                </small>
            </div>
        ';
        exit;
    }

    $Qry = $Conn->prepare("SELECT * FROM respondent WHERE id_respondent = ? LIMIT 1");
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

    $Qry->bind_param("i", $id_respondent);

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

    $id_pasien            = htmlspecialchars($Data['id_pasien']);
    $id_kunjungan         = htmlspecialchars($Data['id_kunjungan']);
    $respondent_name      = htmlspecialchars($Data['respondent_name']);
    $respondent_sex       = htmlspecialchars($Data['respondent_sex']);
    $respondent_brithdate = htmlspecialchars($Data['respondent_brithdate']);
    $tanggal_kunjungan    = htmlspecialchars($Data['tanggal_kunjungan']);
    $kunjungan_tujuan     = htmlspecialchars($Data['kunjungan_tujuan']);
    $no_kontak            = htmlspecialchars($Data['no_kontak']);

    if (!empty($respondent_brithdate)) {
        $respondent_brithdate_format = date('Y-m-d', strtotime($respondent_brithdate));
    } else {
        $respondent_brithdate_format = '';
    }

    $tanggal_kunjungan_format = date('Y-m-d\TH:i', strtotime($tanggal_kunjungan));

    if(empty($no_kontak)){
        $no_kontak = "-";
    }

    $Qry->close();
?>
<input type="hidden" name="id_respondent" value="<?php echo $id_respondent; ?>">
<div class="row mb-3">
    <div class="col-6"><small>ID Responden</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $id_respondent; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>No. RM</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $id_pasien; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>ID Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $id_kunjungan; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Nama Responden</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $respondent_name; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Gender</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $respondent_sex; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Tanggal Lahir</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo !empty($respondent_brithdate_format) ? $respondent_brithdate_format : '-'; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Tanggal Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo date('d/m/Y H:i', strtotime($tanggal_kunjungan)); ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Tujuan Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $kunjungan_tujuan; ?></small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>No.Kontak</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish"><?php echo $no_kontak; ?></small>
    </div>
</div>
