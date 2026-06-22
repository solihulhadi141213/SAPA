<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";
    
    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Session Akses
    if(empty($SessionIdAkses)){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //id_setting_simrs wajib terisi
    if(empty($_POST['id_setting_simrs'])){
        echo '
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <div class="alert alert-danger"><small>Koneksi SIMRS Tiidak Boleh Kosong!</small></div>
                </div>
            </div>
        ';
        exit;
    }

    //Buat variabel 'id_setting_simrs' dan sanitasi
    $id_setting_simrs      = validateAndSanitizeInput($_POST['id_setting_simrs']);

    //Buka Detail Koneksi Dengan Prepared Statment
    $Qry = $Conn->prepare("SELECT * FROM setting_simrs WHERE id_setting_simrs = ?");
    $Qry->bind_param("i", $id_setting_simrs);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel
        $id_setting_simrs     = $Data['id_setting_simrs'];
        $url_setting_simrs    = $Data['url_simrs'];
        $client_id            = $Data['client_id'];
        $client_key           = $Data['client_key'];
        $status_setting_simrs = $Data['status'];

        //Routing Status
        if(empty($status_setting_simrs)){
            $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
        }

        // Tampilkan Data Detail
        if(empty($Data['id_setting_simrs'])){
            echo '
                <div class="alert alert-danger">
                    <small>Data Tidak Ditemukan</small>
                </div>
            '; 
        }else{
            echo '
                <div class="row mb-2">
                    <div class="col-4"><small>URL SIMRS</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long" id="url_simrs">
                            '.$url_setting_simrs.'
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Client ID</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long" id="client_id">
                            '.$client_id.'
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Client Key</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish text-long" id="client_key">
                            '.$client_key.'
                        </small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4"><small>Status Koneksi</small></div>
                    <div class="col-1"><small>:</small></div>
                    <div class="col-7">
                        <small class="text text-grayish">'.$label_status.'</small>
                    </div>
                </div>
            ';

        }
    }
?>