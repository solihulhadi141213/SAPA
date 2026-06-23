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
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Hitung Jumlah Data
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_setting_email_gateway FROM setting_email_gateway"));

    //Jika Tidak Ada Data Kelas
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Tidak ada pengaturan <b>Email Gateway</b> yang ditampilkan</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Tampilkan Data
    $no=1;
    $qry = mysqli_query($Conn, "SELECT * FROM setting_email_gateway ORDER BY id_setting_email_gateway DESC");
    while ($data = mysqli_fetch_array($qry)) {
        $id_setting_email_gateway = $data['id_setting_email_gateway'];
        $email_gateway            = $data['email_gateway'];
        $password_gateway         = $data['password_gateway'];
        $url_provider             = $data['url_provider'];
        $port_gateway             = $data['port_gateway'];
        $nama_pengirim            = $data['nama_pengirim'];
        $url_service              = $data['url_service'];
        $status                   = $data['status'];

        // Potong hanya 10 karakter pertama lalu tambahkan ***
        $password_gateway_masked  = substr($password_gateway, 0, 10) . '***';


        //Routing status koneksi
        if(empty($status)){
            $label_status = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactive</span>';
        }else{
            $label_status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
        }
        //Tampilkan Data
        echo '
            <tr>
                <td class="text-center"><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_setting_email_gateway .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Email gateway">
                        <small class="text text-primary underscore_doted">
                            '.$url_service.'
                        </small>
                    </a>
                </td>
                <td><small>'.$email_gateway.'</small></td>
                <td><small>'.$url_provider.'</small></td>
                <td><small>'.$port_gateway.'</small></td>
                <td class="text-center">'.$label_status.'</td>
                <td class="text-center">
                    <button 
                        class="btn btn-md btn-outline-secondary btn-floating"
                        data-bs-toggle="dropdown">

                        <i class="bi bi-three-dots-vertical"></i>

                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_setting_email_gateway .'">
                                Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_kirim_email" href="javascript:void(0)" data-id="'.$id_setting_email_gateway .'">
                               Kirim Email
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_setting_email_gateway .'">
                               Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_setting_email_gateway .'">
                                Hapus
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        ';
        $no++;
    }
?>