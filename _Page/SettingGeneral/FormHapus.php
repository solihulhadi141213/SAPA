<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

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

    // Validasi id_setting_general
    if (empty($_POST['id_setting_general'])) {
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

    // Variabel And Sanitizer
    $id_setting_general = validateAndSanitizeInput($_POST['id_setting_general']);

    // Open Data With Prepared Statement
    $Qry = $Conn->prepare("SELECT * FROM setting_general WHERE id_setting_general = ? LIMIT 1");
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
    $Qry->bind_param("i", $id_setting_general);
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

    // Jika Tidak Ditemukan
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

    $Data             = $Result->fetch_assoc();
    $app_name         = htmlspecialchars($Data['app_name']);
    $company_name     = htmlspecialchars($Data['company_name']);
    $environment_status = htmlspecialchars($Data['environment_status']);

    $Qry->close();
?>
<input type="hidden" name="id_setting_general" value="<?php echo $id_setting_general; ?>">

<div class="row mb-3">
    <div class="col-6"><small>App Name</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo $app_name; ?>
        </small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6"><small>Company Name</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo $company_name; ?>
        </small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6"><small>Environment Status</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo $environment_status; ?>
        </small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING!</b><br>
                Menghapus data ini akan turut menghapus file <b>App Icon</b> dan <b>Company Logo</b> dari server secara permanen.<br>
                <b>Apakah anda yakin akan menghapus data pengaturan umum tersebut?</b>
            </small>
        </div>
    </div>
</div>
