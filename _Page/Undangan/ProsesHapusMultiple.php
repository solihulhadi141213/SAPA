<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Set Time Zone
    date_default_timezone_set("Asia/Jakarta");

    // Validasi Sesi Akses
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

    // Jika Tidak Ada Form Yang Dikirim
    if(empty($_POST['id_respondent'])){
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    Belum Ada Data Yang Dipilih!
                </small>
            </div>
        ';
        exit;
    }

    // Jika Count Data Tidak Ada
    if(empty(count($_POST['id_respondent']))){
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    Belum Ada Data Yang Dipilih!
                </small>
            </div>
        ';
        exit;
    }
?>
<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td class="text-center"><small><b>No</b></small></td>
                        <td class="text-left"><small><b>No.RM</b></small></td>
                        <td class="text-left"><small><b>Nama Responden</b></small></td>
                        <td class="text-center"><small><b>Keterangan</b></small></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Inisiasi Nomor Baris
                        $no = 1;

                        // Looping Data  Data
                        foreach ($_POST['id_respondent'] as $id_respondent) {

                            // Buka Data Responden
                            $Qry = $Conn->prepare("SELECT * FROM respondent WHERE id_respondent = ? LIMIT 1");
                            $Qry->bind_param("i", $id_respondent);
                            if (!$Qry->execute()) {
                                echo '
                                    <tr>
                                        <td class="text-center"><small>'.$no.'</small></td>
                                        <td class="text-left" colspan="3">
                                            <small class="text-danger">
                                                Terjadi kesalahan pada saat mempersiapkan query database!<br>
                                                Keterangan : ' . htmlspecialchars($Conn->error) . '
                                            </small>
                                        </td>
                                    </tr>
                                ';
                            }else{
                                $Result = $Qry->get_result();
                                if ($Result->num_rows == 0) {
                                    echo '
                                        <tr>
                                            <td class="text-center"><small>'.$no.'</small></td>
                                            <td class="text-left" colspan="3">
                                                <small class="text-danger">
                                                    Data Tidak Ditemukaan
                                                </small>
                                            </td>
                                        </tr>
                                    ';
                                    $Qry->close();
                                }else{
                                    $Data = $Result->fetch_assoc();
                                    $id_pasien            = htmlspecialchars($Data['id_pasien']);
                                    $respondent_name      = htmlspecialchars($Data['respondent_name']);

                                    // Buka Data Undangan
                                    $QryUndangan = $Conn->prepare("SELECT * FROM survey_log WHERE id_respondent = ? LIMIT 1");
                                    $QryUndangan->bind_param("i", $id_respondent);
                                    if (!$QryUndangan->execute()) {
                                        $undangan = '' . htmlspecialchars($Conn->error) . '';
                                        $QryUndangan->close();
                                    }else{
                                        $ResultUndangan = $QryUndangan->get_result();

                                        if ($ResultUndangan->num_rows !== 0) {

                                            // Hapus Data
                                            $stmt_delete = $Conn->prepare("DELETE FROM survey_log WHERE id_respondent = ? LIMIT 1");
                                            if (!$stmt_delete) {
                                                $undangan = '<span class="badge bg-danger-subtle text-danger">Gagal mempersiapkan query hapus</span>';
                                            }else{
                                                $stmt_delete->bind_param("i", $id_respondent);
                                                if (!$stmt_delete->execute()) {
                                                    $stmt_delete->close();
                                                    $undangan = '<span class="badge bg-danger-subtle text-danger">Gagal Mengahpus Undangan</span>';
                                                }else{
                                                    $stmt_delete->close();
                                                    $undangan = '<span class="badge bg-success-subtle text-success">Berhasil Dihapus</span>';
                                                }
                                            }
                                        }else{
                                            $undangan = '<span class="badge bg-danger-subtle text-danger">Tidak Tersedia</span>';
                                        }
                                    }

                                    echo '
                                        <tr>
                                            <td class="text-center"><small>'.$no.'</small></td>
                                            <td class="text-left"><small>'.$id_pasien.'</small></td>
                                            <td class="text-left"><small>'.$respondent_name.'</small></td>
                                            <td class="text-center">
                                                <small>'.$undangan.'</small>
                                            </td>
                                        </tr>
                                    
                                    ';
                                }
                            }
                            $no++;
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>