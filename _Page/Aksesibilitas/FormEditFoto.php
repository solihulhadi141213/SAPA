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

    // Validasi id_akses
    if(empty($_POST['id_akses'])){
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
    $id_akses=validateAndSanitizeInput($_POST['id_akses']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM akses WHERE id_akses = ? LIMIT 1");
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
    $nama_akses         = htmlspecialchars($Data['nama_akses']);
    $kontak_akses       = htmlspecialchars($Data['kontak_akses']);
    $image_akses        = htmlspecialchars($Data['image_akses']);
    $email              = htmlspecialchars($Data['email_akses']);
    $akses              = htmlspecialchars($Data['akses']);
    $image_akses        = htmlspecialchars($Data['image_akses']);
    $datetime_update    = date('d/m/Y H:i',strtotime($Data['datetime_update']));
    $Qry->close();
?>
<input type="hidden" name="id_akses" value="<?php echo $id_akses; ?>">
<div class="row mb-3">
    <div class="col-6"><small>Nama Pengguna</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$nama_akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Nomor Kontak</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$kontak_akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Alamat Email</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$email"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Level/Entitas Akses</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$akses"; ?>
        </small>
    </div>
</div>
<div class="row mb-3">
    <div class="col-6"><small>Update</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-5">
        <small class="text-grayish">
            <?php echo "$datetime_update"; ?>
        </small>
    </div>
</div>
<hr>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="image_akses_edit">
            <small>Foto Profil</small>
        </label>
        <input type="file" name="image_akses" id="image_akses_edit" class="form-control">
        <small>
            <small class="text text-grayish">Foto maksimal 2 mb (File type : PNG, JPG, GIF, WEBP)</small>
        </small>
    </div>
</div>