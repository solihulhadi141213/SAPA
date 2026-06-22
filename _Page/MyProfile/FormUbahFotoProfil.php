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
        SELECT * FROM akses 
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
<div class="row mb-4">
    <div class="col-md-12 text-center">

        <img 
            src="<?php echo $path_foto_profil; ?>" 
            id="preview-image-profil"
            class="rounded-circle shadow"
            style="
                width: 140px;
                height: 140px;
                object-fit: cover;
                object-position: center;
            "
        >

    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">

        <label for="image_akses_edit">
            Upload Foto
        </label>

        <input 
            type="file" 
            name="image_akses" 
            id="image_akses_edit" 
            class="form-control"
            accept=".jpg,.jpeg,.png,.gif"
        >

        <small class="text-muted">
            Maksimal 2 MB (JPG, JPEG, PNG, GIF)
        </small>

    </div>
</div>

<script>
    $('#image_akses_edit').on('change', function(e){

        const file = e.target.files[0];

        if(file){

            // Validasi ukuran
            if(file.size > 2000000){

                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2 MB'
                });

                $(this).val('');

                return false;
            }

            // Preview gambar
            const reader = new FileReader();

            reader.onload = function(event){

                $('#preview-image-profil').attr(
                    'src',
                    event.target.result
                );
            }

            reader.readAsDataURL(file);
        }
    });
</script>