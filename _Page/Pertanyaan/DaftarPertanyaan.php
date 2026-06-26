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
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opps!</b >Sesi Akses Sudah Berakhir! Silahkan Login Ulang!
                </small>
            </div>
        ';
        exit;
    }

    //sampah
    if(empty($_POST['sampah'])){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Opps!</b> Sistem tidak bisa memutuskan apakah status sampah aktif atau tidak !
                </small>
            </div>
        ';
        exit;
    }

    $sampah = $_POST['sampah'];

    //Hitung Jumlah Data
    if($sampah=="true"){
        // Menghitung Jumlah Data Termasuk status 0
        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_survey_question FROM survey_question"));
    }else{
         // Menghitung Jumlah Data Dengan status 1
        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_survey_question FROM survey_question WHERE status=1"));
    }

    //Jika Tidak Ada Data Kelas
    if(empty($jml_data)){
        echo '
            <div class="alert alert-danger text-center">
                <small>
                    <b>Tidak ada data <b>Daftar Pertanyaan</b> yang ditampilkan.
                </small>
            </div>
        ';
        exit;
    }

    //Inisiasi Nomor Baris
    $no=1;

    echo '
        <style>
            .question-title {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                -webkit-line-clamp: 3;
            }

            @media (max-width: 576px) {
                .question-title {
                    -webkit-line-clamp: 1;
                }

                .question-card {
                    padding-right: 3.25rem !important;
                }
            }
        </style>
    ';

    // Query
    if($sampah=="true"){
        $qry = mysqli_query($Conn, "SELECT * FROM survey_question ORDER BY question_order ASC");
    }else{  
        $qry = mysqli_query($Conn, "SELECT * FROM survey_question WHERE status=1 ORDER BY question_order ASC");
    }
    while ($data = mysqli_fetch_array($qry)) {
        $id_survey_question  = $data['id_survey_question'];
        $question_order      = $data['question_order'];
        $question_type       = $data['question_type'];
        $mandatory           = $data['mandatory'];
        $question_text       = $data['question_text'];
        $alternative_answers = $data['alternative_answers'];
        $status              = $data['status'];

        // Routing question type
        if($question_type=="number"){
            $label_question_type = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Number</span>';
        }elseif($question_type=="decimal"){
            $label_question_type = '<span class="badge bg-info-subtle text-info border border-info-subtle">Decimal</span>';
        }elseif($question_type=="text"){
            $label_question_type = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Text</span>';
        }elseif($question_type=="coded"){
            $label_question_type = '<span class="badge bg-success-subtle text-success border border-success-subtle">Coded</span>';
        }elseif($question_type=="boolean"){
            $label_question_type = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Boolean</span>';
        }else{
            $label_question_type = '<span class="badge bg-light text-dark border">Unknown</span>';
        }

        //Routing status
        if((int)$status===0){
            $card_border = 'border border-2 border-danger';
            $status_note = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';
        }else{
            $card_border = 'border border-1 border-light';
            $status_note = '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>';
        }

        // Opsi Pindah Posisi
        $button_pindah_ke_atas = "";
        $button_pindah_ke_bawah = "";
        // Jika $question_order bukan 1 dan data lebih dari 1
        if($question_order!=="1" && $jml_data>1){
            $button_pindah_ke_atas = '
                <li>
                    <a class="dropdown-item ubah_posisi" href="javascript:void(0)" data-posisi="atas" data-id="'.$id_survey_question .'">
                        Pindah Ke Atas <i class="bi bi-arrow-up"></i>
                    </a>
                </li>
            ';
        }

        // Jika $question_order < $jml_data
        if($question_order<$jml_data){
            $button_pindah_ke_bawah = '
                <li>
                    <a class="dropdown-item ubah_posisi" href="javascript:void(0)" data-posisi="bawah" data-id="'.$id_survey_question .'">
                        Pindah Ke Bawah <i class="bi bi-arrow-down"></i>
                    </a>
                </li>
            ';
        }
        
        //Tampilkan Data
        echo '
            <section class="hero-panel mb-3 position-relative question-card '.$card_border.' rounded-3">
                <div class="position-absolute top-0 end-0 p-3">
                    <button 
                        class="btn btn-md btn-outline-secondary btn-floating"
                        data-bs-toggle="dropdown">

                        <i class="bi bi-three-dots-vertical"></i>

                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item modal_detail" href="javascript:void(0)" data-id="'.$id_survey_question .'">
                                Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_edit" href="javascript:void(0)" data-id="'.$id_survey_question .'">
                                Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item modal_delete_soft" href="javascript:void(0)" data-id="'.$id_survey_question .'">
                                Hapus (Soft Delete)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger modal_delete" href="javascript:void(0)" data-id="'.$id_survey_question .'">
                                Hapus (Hard Delete)
                            </a>
                        </li>
                        '.$button_pindah_ke_atas.'
                        '.$button_pindah_ke_bawah.'
                    </ul>
                </div>
                <div class="row g-3 pe-5">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-light text-dark border">#'.$no.'</span>
                            '.$label_question_type.'
                            '.$status_note.'
                        </div>
                        <a href="javascript:void(0);" class="text-primary modal_detail d-block question-title" data-id="'.$id_survey_question .'">
                            '.$question_text.'
                        </a>
                    </div>
                </div>
            </section>

        ';
        $no++;
    }
?>
