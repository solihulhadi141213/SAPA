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
    if(empty($_POST['id_setting_general'])){
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

    // Variabel And Sanitazer
    $id_setting_general=validateAndSanitizeInput($_POST['id_setting_general']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM setting_general WHERE id_setting_general = ? LIMIT 1");
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
    $Qry->bind_param("s", $id_akses);
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
    $Data               = $Result->fetch_assoc();

    // Creat
    $app_name             = htmlspecialchars($Data['app_name']);
    $app_description      = htmlspecialchars($Data['app_description']);
    $app_icon             = htmlspecialchars($Data['app_icon']);
    $app_author           = htmlspecialchars($Data['app_author']);
    $metatag_keyword      = htmlspecialchars($Data['metatag_keyword']);
    $metatag_description  = htmlspecialchars($Data['metatag_description']);
    $company_name         = htmlspecialchars($Data['company_name']);
    $company_address      = htmlspecialchars($Data['company_address']);
    $company_email        = htmlspecialchars($Data['company_email']);
    $company_phone        = htmlspecialchars($Data['company_phone']);
    $company_logo         = htmlspecialchars($Data['company_logo']);
    $base_url             = htmlspecialchars($Data['base_url']);
    $environment_status   = htmlspecialchars($Data['environment_status']);
    $configuration_status = htmlspecialchars($Data['configuration_status']);

    // Image
    $app_icon_path     = "assets/img/logo/" . $app_icon;
    $company_logo_path = "assets/img/logo/" . $company_logo;

    // Routing $environment_status
    if($environment_status=="Development"){
        $label_env_status = '<label class="badge bg-danger-subtle text-danger">Development</label>';
    }else{
        if($environment_status=="Staging"){
            $label_env_status = '<label class="badge bg-warning-subtle text-warning">Staging</label>';
        }else{
            if($environment_status=="Production"){
                $label_env_status = '<label class="badge bg-success-subtle text-success">Production</label>';
            }
        }
    }

    // Routing configuration_status
    if($configuration_status==1){
        $label_configuration_status = '<label class="badge bg-success text-light">Active</label>';
    }else{
        $label_configuration_status = '<label class="badge bg-danger text-light">Inactive</label>';
    }

    // Query Close
    $Qry->close();
?>
    <div class="row mb-3">
        <div class="col-4"><small>App Name</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$app_name"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>App Description</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$app_description"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>App Logo</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="d-inline-block text-truncate" style="max-width: 250px;" title="<?php echo $app_icon; ?>">
                <a href="<?php echo $app_icon_path; ?>" target="_blank" class="text-success">
                    <?php echo $app_icon; ?>
                </a>
            </small>
        </div>
    </div>
   
    <div class="row mb-3">
        <div class="col-4"><small>App Author</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$app_author"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Metatag Keyword</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$metatag_keyword"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Metatag Description</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$metatag_description"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Company Name</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$company_name"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Company Address</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$company_address"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Company Email</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$company_email"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Company Phone</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$company_phone"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Company Logo</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish d-inline-block text-truncate" style="max-width: 250px;" title="<?php echo $company_logo; ?>">
                <a href="<?php echo $company_logo_path; ?>" target="_blank" class="text-success">
                    <?php echo $company_logo; ?>
                </a>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Base URL</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$base_url"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Environment Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <?php echo "$label_env_status"; ?>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Configuration Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <?php echo "$label_configuration_status"; ?>
        </div>
    </div>
    