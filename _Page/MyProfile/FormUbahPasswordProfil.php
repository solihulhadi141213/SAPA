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
            $path_foto_profil = "assets/img/User/No-Image.png";
        }else{
            $image_akses      = $DataDetailAkses['image_akses'];
            $path_foto_profil = "assets/img/User/$image_akses";
        }

    }else{

        // Default jika data tidak ditemukan
        $nama_akses      = "";
        $kontak_akses    = "";
        $email_akses     = "";
        $akses           = "";
        $datetime_update = "";
        $path_foto_profil = "assets/img/No-Image.png";
    }

    // Tutup statement
    $QryDetailAkses->close();
    
?>
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="password1"><small>Password Baru</small></label>
        <input type="password" name="password1" id="password1_edit" class="form-control">
        <small class="credit">Password hanya boleh terdiri dari 6-20 karakter angka dan huruf</small>
    </div>
    <div class="col-md-12 mb-3">
        <label for="password2"><small>Ulangi Password</small></label>
        <input type="password" name="password2" id="password2_edit" class="form-control">
        <small class="credit">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Tampilkan" id="TampilkanPassword2" name="TampilkanPassword2">
                <label class="form-check-label" for="TampilkanPassword2">
                    Tampilkan Password
                </label>
            </div>
        </small>
    </div>
</div>
<script>
    //Kondisi saat tampilkan password
    $('#TampilkanPassword2').click(function(){
        if($(this).is(':checked')){
            $('#password1_edit').attr('type','text');
            $('#password2_edit').attr('type','text');
        }else{
            $('#password1_edit').attr('type','password');
            $('#password2_edit').attr('type','password');
        }
    });
</script>
