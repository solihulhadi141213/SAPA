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
    $credential_env   = htmlspecialchars($Data['credential_env']);
    $client_id       = htmlspecialchars($Data['client_id']);
    $client_secret       = htmlspecialchars($Data['client_secret']);
    $status        = (int) $Data['status'];

    if ($status == 1) {
        $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
    } else {
        $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
    }
?>
<input type="hidden" id="id_google_credential" name="id_google_credential" value="<?php echo $id_google_credential; ?>">

<div class="row mb-3">
    <div class="col-4"><small>Credentian Environment</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long">
            <?php echo $credential_env; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Client ID</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long" title="API Key dimasked">
            <?php echo $client_id; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Client Secret</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <small class="text-grayish text-long" title="API Key dimasked">
            <?php echo $client_secret; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-4"><small>Status</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-7">
        <?php echo $label_status; ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <small>
                <b>PENTING!</b><br>
                Menghapus pengaturan <b>Google Credential</b> akan menghapus konfigurasi ini dari sistem.<br>
                <b>Apakah anda yakin akan menghapusnya?</b>
            </small>
        </div>
    </div>
</div>
