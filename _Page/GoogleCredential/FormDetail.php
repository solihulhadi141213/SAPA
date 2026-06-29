<?php
include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

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

    if (empty($_POST['id_google_credential'])) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Data Google Credential belum dipilih.
                </small>
            </div>
        ';
        exit;
    }

    $id_google_credential = validateAndSanitizeInput($_POST['id_google_credential']);

    $Qry = $Conn->prepare("
        SELECT
            id_google_credential,
            credential_env,
            client_id,
            client_secret,
            status
        FROM google_credential
        WHERE id_google_credential = ?
        LIMIT 1
    ");

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

    $Qry->bind_param("i", $id_google_credential);

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
    $Qry->close();

    $id_google_credential = htmlspecialchars($Data['id_google_credential']);
    $credential_env       = htmlspecialchars($Data['credential_env']);
    $client_id            = htmlspecialchars($Data['client_id']);
    $client_secret        = htmlspecialchars($Data['client_secret']);
    $status               = (int) $Data['status'];

    //Routing status koneksi
    if(empty($status)){
        $label_status = 'Inactive';
    }else{
        $label_status = 'Active';
    }
?>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="credential_env_detail">
            <small>Credential Env</small>
        </label>
        <input type="text" disabled name="credential_env_detail" id="credential_env_detail" class="form-control bg-info-subtle" value="<?php echo $credential_env; ?>">
    </div>
</div>
 <div class="row mb-3">
    <div class="col-md-12">
        <label for="client_id_detail">
            <small>Client ID</small>
        </label>
        <textarea name="client_id_detail" id="client_id_detail" class="form-control bg-info-subtle"><?php echo $client_id; ?></textarea>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="client_secret_detail">
            <small>Client Secret</small>
        </label>
        <textarea name="client_secret_detail" id="client_secret_detail" class="form-control bg-info-subtle"><?php echo $client_secret; ?></textarea>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="status_detail">
            <small>Status</small>
        </label>
        <input type="text" disabled name="credential_estatus_detailv_detail" id="status_detail" class="form-control bg-info-subtle" value="<?php echo $label_status; ?>">
    </div>
</div>
