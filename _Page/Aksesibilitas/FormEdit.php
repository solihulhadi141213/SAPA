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
    <div class="col-md-12">
        <label for="nama_akses_edit">Nama Pengguna</label>
        <input type="text" class="form-control" name="nama_akses" id="nama_akses_edit" value="<?php echo $nama_akses; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="kontak_akses_edit">Nomor Kontak</label>
        <input type="text" class="form-control" name="kontak_akses" id="kontak_akses_edit" value="<?php echo $kontak_akses; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="email_edit">Alamat Email</label>
        <input type="email" class="form-control" name="email" id="email_edit"  value="<?php echo $email; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="akses_edit">Level/Entitas</label>
        <select name="akses" id="akses_edit" class="form-control">
            <option value="">Pilih</option>
            <option <?php if($akses=="Admin"){echo "selected";} ?> value="Admin">Admin</option>
            <option <?php if($akses=="Manajer Mutu"){echo "selected";} ?> value="Manajer Mutu">Manajer Mutu</option>
            <option <?php if($akses=="Direktur"){echo "selected";} ?> value="Direktur">Direktur</option>
        </select>
    </div>
</div>