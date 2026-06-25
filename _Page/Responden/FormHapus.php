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
    $id_pasien       = htmlspecialchars($Data['id_pasien']);
    $respondent_name = htmlspecialchars($Data['respondent_name']);
    $id_kunjungan    = htmlspecialchars($Data['id_kunjungan']);
    $Qry->close();
?>
<input type="hidden" name="id_respondent" value="<?php echo $id_respondent; ?>">

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
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING!</b><br>
                Data responden berikut akan dihapus dari sistem.<br>
                <b>Apakah anda yakin akan menghapus data tersebut?</b>
            </small>
        </div>
    </div>
</div>
