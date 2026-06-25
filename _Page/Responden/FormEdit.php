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
    $no_kontak     = htmlspecialchars($Data['no_kontak']);

    $respondent_brithdate_format = !empty($respondent_brithdate) ? date('Y-m-d', strtotime($respondent_brithdate)) : '';
    $tanggal_kunjungan_format    = !empty($tanggal_kunjungan) ? date('Y-m-d\TH:i', strtotime($tanggal_kunjungan)) : '';

    $Qry->close();
?>
<input type="hidden" name="id_respondent" value="<?php echo $id_respondent; ?>">
<div class="row mb-3">
    <div class="col-md-4">
        <label for="id_pasien_edit"><small>No. RM</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="id_pasien" id="id_pasien_edit" class="form-control" value="<?php echo $id_pasien; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="id_kunjungan_edit"><small>ID Kunjungan</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="id_kunjungan" id="id_kunjungan_edit" class="form-control" value="<?php echo $id_kunjungan; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="respondent_name_edit"><small>Nama Responden</small></label>
    </div>
    <div class="col-md-8">
        <input type="text" name="respondent_name" id="respondent_name_edit" class="form-control" value="<?php echo $respondent_name; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="respondent_sex_edit"><small>Gender</small></label>
    </div>
    <div class="col-md-8">
        <select name="respondent_sex" id="respondent_sex_edit" class="form-control">
            <option value="">Pilih</option>
            <option value="Male" <?php if($respondent_sex == "Male"){ echo "selected"; } ?>>Male</option>
            <option value="Female" <?php if($respondent_sex == "Female"){ echo "selected"; } ?>>Female</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="respondent_brithdate_edit"><small>Tanggal Lahir</small></label>
    </div>
    <div class="col-md-8">
        <input type="date" name="respondent_brithdate" id="respondent_brithdate_edit" class="form-control" value="<?php echo $respondent_brithdate_format; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="tanggal_kunjungan_edit"><small>Tanggal Kunjungan</small></label>
    </div>
    <div class="col-md-8">
        <input type="datetime-local" name="tanggal_kunjungan" id="tanggal_kunjungan_edit" class="form-control" value="<?php echo $tanggal_kunjungan_format; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="kunjungan_tujuan_edit"><small>Tujuan Kunjungan</small></label>
    </div>
    <div class="col-md-8">
        <select name="kunjungan_tujuan" id="kunjungan_tujuan_edit" class="form-control">
            <option value="">Pilih</option>
            <option value="Rajal" <?php if($kunjungan_tujuan == "Rajal"){ echo "selected"; } ?>>Rajal</option>
            <option value="Ranap" <?php if($kunjungan_tujuan == "Ranap"){ echo "selected"; } ?>>Ranap</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        <label for="no_kontak_edit"><small>No.Kontak/WA</small></label>
    </div>
    
    <div class="col-md-8">
        <input type="text" name="no_kontak" id="no_kontak_edit" class="form-control" value="<?php echo $no_kontak; ?>" placeholder="62">
    </div>
</div>