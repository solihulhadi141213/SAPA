<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";
    include "../../_Config/Setting.php";

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
<div class="row mb-3">
    <div class="col-12">
        <small>
            <b>A. Identitas Responden</b>
        </small>
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

<!-- Buka Data Undangan -->
<?php
    $Qry = $Conn->prepare("SELECT * FROM survey_log WHERE id_respondent = ? LIMIT 1");
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

    if ($Result->num_rows !== 0) {
        $Data                = $Result->fetch_assoc();
        $invitation_token    = $Data['invitation_token'];
        $datetime_invitation = $Data['datetime_invitation'];
        $method_invitation   = $Data['method_invitation'];
        $no_wa               = $Data['no_wa'];
        $email               = $Data['email'];
        $survey_link         = $base_url.'/Questionnaire.php?token='.$invitation_token;

        // Tampilkan Data Undangan
        echo '
            <hr>
            <div class="row mb-3">
                <div class="col-12">
                    <small>
                        <b>B. Detail Undangan</b>
                    </small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6"><small>Kode Akses</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-5">
                    <small class="text-grayish">'.$invitation_token.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6"><small>Datetime</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-5">
                    <small class="text-grayish">'.$datetime_invitation.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6"><small>Metode</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-5">
                    <small class="text-grayish">'.$method_invitation.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6"><small>Kontak</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-5">
                    <small class="text-grayish">'.$no_wa.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6"><small>Email</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-5">
                    <small class="text-grayish">'.$email.'</small>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <div class="fw-semibold mb-1">Link Form Kuesioner</div>
                            <small class="text-muted">Salin tautan berikut untuk dibagikan ke pasien.</small>
                        </div>
                        <span class="badge rounded-pill text-bg-info-subtle text-info border border-info-subtle">Public Link</span>
                    </div>
                    <div class="input-group copy-group mb-3">
                        <input type="text" class="form-control" id="link_questionnaire_detail" value="'.$survey_link.'" readonly>
                        <button type="button" class="btn btn-outline-primary" id="copyQuestionnaireLink">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div class="d-grid">
                        <a href="'.$survey_link.'" class="btn btn-info btn-md" target="_blank" rel="noopener">
                            Lihat Form <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        ';
        $Qry->close();
    }

    
 ?>
