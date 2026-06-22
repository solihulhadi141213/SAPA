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
 <div class="col-md-4 mb-3">
        <div class="content-card content-card-heavy h-100">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <img src="<?php echo "$path_foto_profil"; ?>" alt="" class="image-my-profile  mx-auto d-block">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 text-center">
                    <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahIdentitasProfil">
                        <i class="bi bi-pencil"></i> 
                    </button>
                    <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahFotoProfil">
                        <i class="bi bi-image"></i> 
                    </button>
                    <button class="btn btn-md btn-floating btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ModalUbahPasswordProfil">
                        <i class="bi bi-key"></i> 
                    </button>
                </div>
            </div>
            
        </div>
    </div>

    <div class="col-md-8  mb-3">
        <div class="content-card content-card-heavy h-100">
            <div class="row mb-2">
                <div class="col-4"><small>Nama</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text-grayish"><?php echo "$nama_akses"; ?></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>No.Kontak</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text-grayish"><?php echo "$kontak_akses"; ?></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Email</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text-grayish"><?php echo "$email_akses"; ?></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Level</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text-grayish"><?php echo "$akses"; ?></small></div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Update</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7"><small class="text-grayish"><?php echo date('d/m/Y H:i', strtotime($datetime_update)); ?></small></div>
            </div>

            
        </div>
    </div>
</div>
