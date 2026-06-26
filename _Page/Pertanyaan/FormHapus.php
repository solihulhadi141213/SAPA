<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Validasi Session
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

    // Validasi id_survey_question 
    if(empty($_POST['id_survey_question'])){
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Anda belum memilih data manapun
                </small>
            </div>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $id_survey_question=validateAndSanitizeInput($_POST['id_survey_question']);

    // Open Data With Prepared Statmnet
    $Qry = $Conn->prepare("SELECT*FROM survey_question WHERE id_survey_question = ? LIMIT 1");
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan query database!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }
    $Qry->bind_param("s", $id_survey_question);
    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka data dari database!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Result = $Qry->get_result();

    // Jika Tidak Ditemukan
    if ($Result->num_rows == 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Data tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }
    $Data               = $Result->fetch_assoc();

    // Buat Variabel
    $question_order      = htmlspecialchars($Data['question_order']);
    $question_type       = htmlspecialchars($Data['question_type']);
    $mandatory           = htmlspecialchars($Data['mandatory']);
    $question_text       = htmlspecialchars($Data['question_text']);
    $alternative_answers = $Data['alternative_answers'];
    $status              = htmlspecialchars($Data['status']);
    $Qry->close();

    // Routing Mantaory
    if($mandatory==1){
        $mandatory_label = "Ya";
    }else{
        $mandatory_label = "Tidak";
    }

    //Routing status
    if((int)$status===0){
        $status_note = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';
    }else{
        $status_note = '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>';
    }
?>

    <input type="hidden" name="id_survey_question" value="<?php echo $id_survey_question; ?>">
    <div class="row mb-3">
        <div class="col-4"><small>Order</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$question_order"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Type</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$question_type"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Mandatory</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$mandatory_label"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Pertanyaan</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$question_text"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4"><small>Status</small></div>
        <div class="col-1"><small>:</small></div>
        <div class="col-6">
            <small class="text-grayish">
                <?php echo "$status_note"; ?>
            </small>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <small>
                    <b>PENTING!</b><br>
                    Menghapus data daftar pertanyaan akan menyebabkan jawaban yang sudah ada ikut terhapus.<br>
                    <b>Apakah anda yakin akan menghapus data tersebut?</b>
                </small>
            </div>
        </div>
    </div>