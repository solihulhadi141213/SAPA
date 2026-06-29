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
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_google_credential FROM google_credential"));

    //Jika Tidak Ada Data Kelas
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak ada pengaturan <b>Google Credential</b> yang ditampilkan</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Tampilkan Data
    $no=1;
    $qry = mysqli_query($Conn, "SELECT * FROM google_credential ORDER BY id_google_credential DESC");
    while ($data = mysqli_fetch_array($qry)) {
        $id_google_credential = $data['id_google_credential'];
        $credential_env       = $data['credential_env'];
        $client_id            = $data['client_id'];
        $status               = $data['status'];

        // Client Secret
        if (!empty($data['client_secret'])) {
            $client_secret = htmlspecialchars($data['client_secret']);
            $client_secret = substr($client_secret, 0, 3) . str_repeat('*', max(0, strlen($client_secret) - 3));
        } else {
            $client_secret = "-";
        }
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
                    <a href="javascript:void(0);" class="modal_detail" data-id="'.$id_google_credential .'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Lihat Detail Whatsapp gateway">
                        <small class="text text-primary underscore_doted">
                            '.$credential_env.'
                        </small>
                    </a>
                </td>
                <td><small>'.$client_id.'</small></td>
                <td><small>'.$client_secret.'</small></td>
                <td class="text-center">'.$label_status.'</td>
                <td class="text-center">
                    <button 
                        class="btn btn-md btn-outline-secondary btn-floating"
                        data-bs-toggle="dropdown">

                        <i class="bi bi-three-dots-vertical"></i>

                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_google_credential .'">
                                Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_google_credential .'">
                               Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_google_credential .'">
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