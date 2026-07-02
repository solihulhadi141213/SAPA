<?php
    // Connection, Session dan Helper
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Validasi Session Akses
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
    $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_survey_question FROM survey_question WHERE status=1"));
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

    echo '
        <div class="alert alert-info text-center mb-3">
            <small>
                Untuk memulai menampilkan data laporan, silahkan pilih salah satu dari daftar pertanyaan berikut ini.
            </small>
        </div>
    ';

    //Inisiasi Nomor Baris
    $no=1;


    // Query
    $qry = mysqli_query($Conn, "SELECT * FROM survey_question WHERE status=1 ORDER BY question_order ASC");
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
        
        //Tampilkan Data
        echo '
            <div class="row mb-3 mt-3">
                <div class="col-12">
                    <a href="javascript:void(0);" class="text-primery pilih_pertanyaan" data-id="'.$id_survey_question.'">
                        <small>'.$no.'. '.$question_text.'</small>
                    </a>
                </div>
            </div>
            <hr>
        ';
        $no++;
    }
?>