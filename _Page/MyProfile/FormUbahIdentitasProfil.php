<?php
    // Connection dan Session
    date_default_timezone_set('Asia/Jakarta');
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // Validasi Sesi Akses
    if(empty($SessionIdAkses)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Login Sudah Berakhir, Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Buka identitas pengguna berdasarkan $SessionIdAkses
    $QryDetailAkses = $Conn->prepare("
        SELECT 
            nama_akses,
            kontak_akses,
            email_akses,
            akses,
            datetime_update,
            image_akses
        FROM akses 
        WHERE id_akses = ?
    ");

    // Bind parameter
    $QryDetailAkses->bind_param("i", $SessionIdAkses);

    // Eksekusi query
    $QryDetailAkses->execute();

    // Ambil hasil
    $ResultDetailAkses = $QryDetailAkses->get_result();

    // Validasi data ditemukan
    if($DataDetailAkses = $ResultDetailAkses->fetch_assoc()){

        $nama_akses      = $DataDetailAkses['nama_akses'];
        $kontak_akses    = $DataDetailAkses['kontak_akses'];
        $email_akses     = $DataDetailAkses['email_akses'];
        $akses           = $DataDetailAkses['akses'];
        $datetime_update = $DataDetailAkses['datetime_update'];

        // Routing Foto Profile
        if(empty($DataDetailAkses['image_akses'])){
            $path_foto_profil = "assets/img/No-Image.png";
        }else{
            $image_akses      = $DataDetailAkses['image_akses'];
            $path_foto_profil = "assets/img/user/$image_akses";
        }

    }else{

        // Default jika data tidak ditemukan
        $nama_akses      = "";
        $kontak_akses    = "";
        $email_akses     = "";
        $akses           = "";
        $datetime_update = "";
        $path_foto_profil = "assets/img/User/No-Image.png";
    }

    // Tutup statement
    $QryDetailAkses->close();
    
?>
<div class="row mb-3">
    <div class="col-12">
        <label for="nama_akses_profil">
            <small>Nama Pengguna</small>
        </label>
        <input type="text" name="nama_akses" id="nama_akses_profil" class="form-control" required value="<?php echo "$nama_akses"; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="kontak_akses_profil">
            <small>No.Kontak</small>
        </label>
        <input type="text" name="kontak_akses" id="kontak_akses_profil" class="form-control"  required value="<?php echo "$kontak_akses"; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <label for="email_akses_profil">
            <small>Email</small>
        </label>
        <input type="email" name="email_akses_profil" id="email_akses_profil" class="form-control" required value="<?php echo "$email_akses"; ?>">
    </div>
</div>
   