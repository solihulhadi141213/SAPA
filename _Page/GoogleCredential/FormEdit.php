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
?>
<input type="hidden" name="id_google_credential" value="<?php echo $id_google_credential; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="credential_env_edit">
            <small>Credential Env <small title="Wajib Diisi">*</small></small>
        </label>
        <select name="credential_env" id="credential_env_edit" class="form-control" required>
            <option <?php if($credential_env=='Production'){echo "selected";} ?> value="Production">Production</option>
            <option <?php if($credential_env=='Staging'){echo "selected";} ?> value="Staging">Staging</option>
            <option <?php if($credential_env=='Development'){echo "selected";} ?> value="Development">Development</option>
        </select>
    </div>
</div>
 <div class="row mb-3">
    <div class="col-md-12">
        <label for="client_id_edit">
            <small>Client ID <small title="Wajib Diisi">*</small></small>
        </label>
        <input type="text" class="form-control" name="client_id" id="client_id_edit" value="<?php echo $client_id; ?>"  required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="client_secret_edit">
            <small>Client Secret</small>
        </label>
        <input type="password" class="form-control" name="client_secret" id="client_secret_edit" value="<?php echo $client_secret; ?>" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="status_edit">
            <small>Status</small>
        </label>
        <select name="status" id="status_edit" class="form-control">
            <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inactive</option>
            <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Active</option>
        </select>
    </div>
</div>
