<?php

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

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

    if (empty($_POST['id_survey_question'])) {
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

    $id_survey_question = validateAndSanitizeInput($_POST['id_survey_question']);

    $Qry = $Conn->prepare("SELECT * FROM survey_question WHERE id_survey_question = ? LIMIT 1");
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

    $Data = $Result->fetch_assoc();
    $Qry->close();

    $question_type = htmlspecialchars($Data['question_type']);
    $mandatory = (string) $Data['mandatory'];
    $question_text = htmlspecialchars($Data['question_text']);
    $alternative_answers = $Data['alternative_answers'];
    $status = htmlspecialchars($Data['status']);
    $id_survey_question = htmlspecialchars($Data['id_survey_question']);

    $alternative_list = [];
    if ($question_type === 'coded' && !empty($alternative_answers)) {
        $decoded = json_decode($alternative_answers, true);
        if (is_array($decoded)) {
            $alternative_list = $decoded;
        }
    }

    if (count($alternative_list) === 0) {
        $alternative_list = [
            ['label' => '', 'value' => '']
        ];
    }
?>
<input type="hidden" name="id_survey_question" value="<?php echo $id_survey_question; ?>">

<div class="row mb-3">
    <div class="col-md-12">
        <label for="question_type_edit">
            <small>Tipe Pertanyaan</small>
        </label>
        <select name="question_type" id="question_type_edit" class="form-control">
            <option value="">Pilih</option>
            <option value="number" <?php if($question_type === 'number'){ echo 'selected'; } ?>>Number</option>
            <option value="decimal" <?php if($question_type === 'decimal'){ echo 'selected'; } ?>>Decimal</option>
            <option value="text" <?php if($question_type === 'text'){ echo 'selected'; } ?>>Text</option>
            <option value="coded" <?php if($question_type === 'coded'){ echo 'selected'; } ?>>Coded</option>
            <option value="boolean" <?php if($question_type === 'boolean'){ echo 'selected'; } ?>>Boolean</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="mandatory_edit">
            <small>Apakah Pertanyaan Wajib Diisi?</small>
        </label>
        <select name="mandatory" id="mandatory_edit" class="form-control">
            <option value="1" <?php if($mandatory === '1'){ echo 'selected'; } ?>>Ya</option>
            <option value="0" <?php if($mandatory === '0'){ echo 'selected'; } ?>>Tidak</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="question_text_edit">
            <small>Pertanyaan</small>
        </label>
        <textarea name="question_text" id="question_text_edit" class="form-control"><?php echo $question_text; ?></textarea>
    </div>
</div>

<div id="form_alternative_answers_edit" <?php if($question_type !== 'coded'){ echo 'style="display:none;"'; } ?>>
    <div class="row mb-3">
        <div class="col-12">
            <label>
                <small>Alternatif Jawaban</small>
            </label>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-outline-primary w-100 tambah_alternatif_edit">
                <i class="bi bi-plus"></i> Tambah Alternatif
            </button>
        </div>
    </div>

    <div id="list_alternatif_edit">
        <?php foreach ($alternative_list as $alternative) { ?>
            <?php
                $label = htmlspecialchars((string) ($alternative['label'] ?? ''));
                $value = htmlspecialchars((string) ($alternative['value'] ?? ''));
            ?>
            <div class="row mb-2 item_alternatif">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" name="alternatif_label[]" class="form-control" placeholder="Label" value="<?php echo $label; ?>">
                        <input type="text" name="alternatif_value[]" class="form-control" placeholder="Value" value="<?php echo $value; ?>">
                        <button type="button" class="btn btn-danger hapus_alternatif_edit">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label for="status_edit">
            <small>Status</small>
        </label>
        <select name="status" id="status_edit" class="form-control">
            <option value="1" <?php if((string)$status === '1'){ echo 'selected'; } ?>>Aktif</option>
            <option value="0" <?php if((string)$status === '0'){ echo 'selected'; } ?>>Nonaktif</option>
        </select>
    </div>
</div>
