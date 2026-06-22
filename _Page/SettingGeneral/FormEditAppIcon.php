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

    $Data = $Result->fetch_assoc();

    $app_name             = htmlspecialchars($Data['app_name']);
    $app_description      = htmlspecialchars($Data['app_description']);
    $app_author           = htmlspecialchars($Data['app_author']);
    $metatag_keyword      = htmlspecialchars($Data['metatag_keyword']);
    $metatag_description  = htmlspecialchars($Data['metatag_description']);
    $company_name         = htmlspecialchars($Data['company_name']);
    $company_address      = htmlspecialchars($Data['company_address']);
    $company_email        = htmlspecialchars($Data['company_email']);
    $company_phone        = htmlspecialchars($Data['company_phone']);
    $base_url             = htmlspecialchars($Data['base_url']);
    $environment_status   = htmlspecialchars($Data['environment_status']);
    $configuration_status = htmlspecialchars($Data['configuration_status']);

    $Qry->close();
?>
<input type="hidden" name="id_setting_general" value="<?php echo $id_setting_general; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="app_icon_edit">
            App Pavicon <small title="Wajib Diisi">*</small>
        </label>
        <input type="file" name="app_icon" id="app_icon_edit" class="form-control" required>
        <small>
            <small class="text text-grayish">Pavicon maksimal 2 mb (File type : PNG, JPG, GIF, WEBP, SVG)</small>
        </small>
    </div>
</div>