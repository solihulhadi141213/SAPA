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

    $id_pasien            = htmlspecialchars($Data['id_pasien']);
    $id_kunjungan         = htmlspecialchars($Data['id_kunjungan']);
    $respondent_name      = htmlspecialchars($Data['respondent_name']);
    $respondent_sex       = htmlspecialchars($Data['respondent_sex']);
    $respondent_brithdate = htmlspecialchars($Data['respondent_brithdate']);
    $tanggal_kunjungan    = htmlspecialchars($Data['tanggal_kunjungan']);
    $kunjungan_tujuan     = htmlspecialchars($Data['kunjungan_tujuan']);
    $no_kontak     = htmlspecialchars($Data['no_kontak']);

    $respondent_brithdate_format = !empty($respondent_brithdate) ? date('Y-m-d', strtotime($respondent_brithdate)) : '';
    $tanggal_kunjungan_format    = !empty($tanggal_kunjungan) ? date('Y-m-d\TH:i', strtotime($tanggal_kunjungan)) : '';

    $Qry->close();

    // Buat Token
    $token = GenerateToken(16);
?>
<input type="hidden" name="id_respondent" value="<?php echo $id_respondent; ?>">
<div class="row mb-3">
    <div class="col-md-12">
        <label for="respondent_name"><small>Nama Responden</small></label>
    </div>
    <div class="col-md-12">
        <input type="text" readonly name="respondent_name" id="respondent_name" class="form-control bg-secondary-subtle" value="<?php echo $respondent_name; ?>">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="invitation_token"><small>Kode Formulir</small></label>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <input type="text" name="invitation_token" id="invitation_token" class="form-control" value="<?php echo $token; ?>">
            <a href="javascript:void(0);" class="input-group-text" id="reload_token">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="no_kontak"><small>Phone/WA</small></label>
    </div>
    <div class="col-md-12">
        <input type="text" name="no_kontak" id="no_kontak" class="form-control" value="<?php echo $no_kontak; ?>">
    </div>
</div>


<div class="row mb-3">
    <div class="col-md-12">
        <label for="email"><small>Email</small></label>
    </div>
    <div class="col-md-12">
        <input type="text" name="email" id="email" class="form-control" value="">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="method_invitation"><small>Metode Undangan</small></label>
    </div>
    <div class="col-md-12">
        <select name="method_invitation" id="method_invitation" class="form-control" required>
            <option value="">Pilih</option>
            <option value="Whatsapp">Whatsapp</option>
            <option value="Email">Email</option>
            <option value="Manual">Manual</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="isi_pesan">
            <small>Isi Pesan</small>
        </label>
    </div>
    <div class="col-md-12">
        <textarea
            name="isi_pesan"
            id="isi_pesan"
            class="form-control"
            rows="12">Yth. Bapak/Ibu {{respondent_name}},

Terima kasih telah mempercayakan pelayanan kesehatan kepada Rumah Sakit El-Syifa Kuningan.

Untuk membantu kami meningkatkan kualitas pelayanan, kami mengundang Bapak/Ibu untuk mengisi Survei Kepuasan Pasien melalui tautan berikut:

{{link_survey}}

Pengisian survei hanya membutuhkan waktu sekitar 2–3 menit. Setiap masukan yang diberikan akan menjadi bahan evaluasi kami untuk terus meningkatkan mutu pelayanan.

Terima kasih atas waktu dan partisipasi Bapak/Ibu.

Hormat kami,

Tim Rumah Sakit El-Syifa Kuningan</textarea>
    </div>
</div>
