<?php
    // Koneksi, Global Function, Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Set Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
    if(empty($SessionIdAkses)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan Login Ulang!</small>
            </div>
        ';
        exit;
    }

    // Validasi id_kunjungan tidak boleh kosong
    if(empty($_POST['id_kunjungan'])){
        include "../../_Page/Responden/FormTambah.php";
        exit;
    }

    // Buat Variabel id_kunjungan dan sanitasi
    $id_kunjungan = validateAndSanitizeInput($_POST['id_kunjungan']);

    // Buka URL SIMRS
    $status = 1;
    $url_simrs = GetDetailData($Conn,'setting_simrs','status',$status,'url_simrs');

    //Dapatkan Token SIMRS
    $token = GetSimrsToken($Conn);

    // Jika Token Tidak Valid Dan Gagal Dibuat
    if ($token === false) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal mendapatkan token SIMRS!</small>
            </div>
        ';
        exit;
    }

    // Mulai CURL service API SIMRS Untuk Mendapatkan Detail Kunjungan
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => ''.$url_simrs.'/API/SIMRS/get_detail_kunjungan.php?id='.$id_kunjungan.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token.'',
            'X-API-Key: ••••••'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Ubah Response Menjadi Arry
    $data = json_decode($response, true);

    // Jika Response Tidak Valid
    if (empty($data['response']['code']) ||$data['response']['code'] != 200) {
        echo '
            <div class="alert alert-danger">
                <small>Gagal memuat data kunjungan<br> Pesan : '.$data['response']['message'].'</small>
            </div>
        ';
        exit;
    }

    // Buka Metadata
    $metadata = $data['metadata'];

    // Buat Variabel Penting
    $id_encounter      = $metadata['id_encounter'];
    $tujuan            = $metadata['tujuan'];
    $id_dokter         = $metadata['id_dokter'];
    $gender            = $metadata['pasien']['gender'];
    $tanggal_lahir     = $metadata['pasien']['tanggal_lahir'];
    $tanggal_kunjungan = $metadata['tanggal'];

    // Rouing Gender
    if($gender=="Laki-laki"){
        $gender_label = "Male";
    }else{
        $gender_label = "Female";
    }

    // Format tanggal kunjungan
    $tanggal_kunjungan_date = date('Y-m-d H:i:s', strtotime($tanggal_kunjungan));
    $tanggal_kunjungan_time = date('d-m-Y', strtotime($tanggal_kunjungan));

    //Tampilkan Form
    echo '
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_pasien"><small>No.RM</small></label>
            </div>
            <div class="col-md-8">
                <input type="text" readonly name="id_pasien" id="id_pasien" class="form-control bg-secondary" value="'.$metadata['pasien']['id_pasien'].'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_kunjungan"><small>ID.Kunjungan</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" readonly name="id_kunjungan" id="id_kunjungan" class="form-control bg-secondary" value="'.$id_kunjungan.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="nama_pasien"><small>Nama Pasien</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" value="'.$metadata['pasien']['nama'].'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="gender"><small>Gender</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" name="gender" id="gender" class="form-control" value="'.$gender_label.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="respondent_brithdate"><small>Tanggal Lahir</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="date" name="respondent_brithdate" id="respondent_brithdate" class="form-control" value="'.$tanggal_lahir.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tanggal_kunjungan"><small>Tanggal Kunjungan</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" readonly name="tanggal_kunjungan" id="tanggal_kunjungan" class="form-control bg-secondary" value="'.$tanggal_kunjungan_date.'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kunjungan_tujuan"><small>Tujuan Kunjungan</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" readonly name="kunjungan_tujuan" id="kunjungan_tujuan" class="form-control bg-secondary" value="'.$metadata['tujuan'].'">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="no_kontak"><small>No.Kontak/WA</small></label>
            </div>
            
            <div class="col-md-8">
                <input type="text" name="no_kontak" id="no_kontak" class="form-control" value="'.$metadata['pasien']['kontak'].'" placeholder="62">
            </div>
        </div>
    ';

?>