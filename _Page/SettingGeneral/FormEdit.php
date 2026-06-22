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
        <label for="app_name_edit">App Name <small title="Wajib Diisi">*</small></label>
        <input type="text" class="form-control" name="app_name" id="app_name_edit" value="<?php echo $app_name; ?>" placeholder="Aplikasi Saya" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="app_description_edit">App Description</label>
        <textarea name="app_description" id="app_description_edit" class="form-control"><?php echo $app_description; ?></textarea>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="app_author_edit">App Author <small title="Wajib Diisi">*</small></label>
        <input type="text" class="form-control" name="app_author" id="app_author_edit" value="<?php echo $app_author; ?>" placeholder="Jhon Doe" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="metatag_keyword_edit">Metatag Keyword <small title="Wajib Diisi">*</small></label>
        <input type="text" class="form-control" name="metatag_keyword" id="metatag_keyword_edit" value="<?php echo $metatag_keyword; ?>" placeholder="Keyword1, Keyword2, Keyword3" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="metatag_description_edit">Metatag Description <small title="Wajib Diisi">*</small></label>
        <textarea name="metatag_description" id="metatag_description_edit" class="form-control" required><?php echo $metatag_description; ?></textarea>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="company_name_edit">Company Name <small title="Wajib Diisi">*</small></label>
        <input type="text" class="form-control" name="company_name" id="company_name_edit" value="<?php echo $company_name; ?>" placeholder="My Company Ltd" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="company_address_edit">Company Address <small title="Wajib Diisi">*</small></label>
        <textarea name="company_address" id="company_address_edit" class="form-control" required><?php echo $company_address; ?></textarea>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="company_email_edit">Company Email <small title="Wajib Diisi">*</small></label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" class="form-control" name="company_email" id="company_email_edit" value="<?php echo $company_email; ?>" placeholder="companyemail@domain.com" required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="company_phone_edit">Company Phone <small title="Wajib Diisi">*</small></label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-phone"></i>
            </span>
            <input type="text" class="form-control" name="company_phone" id="company_phone_edit" value="<?php echo $company_phone; ?>" placeholder="62" required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="base_url_edit">Base URL <small title="Wajib Diisi">*</small></label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-link"></i>
            </span>
            <input type="url" class="form-control" name="base_url" id="base_url_edit" value="<?php echo $base_url; ?>" placeholder="https://" required>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="environment_status_edit">Environment Status <small title="Wajib Diisi">*</small></label>
        <select name="environment_status" id="environment_status_edit" class="form-control" required>
            <option value="">Pilih</option>
            <option <?php if ($environment_status == "Development") { echo "selected"; } ?> value="Development">Development</option>
            <option <?php if ($environment_status == "Staging") { echo "selected"; } ?> value="Staging">Staging</option>
            <option <?php if ($environment_status == "Production") { echo "selected"; } ?> value="Production">Production</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="configuration_status_edit">Configuration Status <small title="Wajib Diisi">*</small></label>
        <select name="configuration_status" id="configuration_status_edit" class="form-control">
            <option <?php if ($configuration_status == "1") { echo "selected"; } ?> value="1">Active</option>
            <option <?php if ($configuration_status == "0") { echo "selected"; } ?> value="0">Inactive</option>
        </select>
    </div>
</div>
