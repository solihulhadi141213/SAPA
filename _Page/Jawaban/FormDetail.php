<?php
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
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

    // =========================================================
    // VALIDASI ID RESPONDEN
    // =========================================================
    if (empty($_POST['id_respondent'])) {
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

    $id_respondent = validateAndSanitizeInput($_POST['id_respondent']);

    if (!filter_var($id_respondent, FILTER_VALIDATE_INT) || (int)$id_respondent <= 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    ID responden tidak valid.
                </small>
            </div>
        ';
        exit;
    }

    $id_respondent = (int) $id_respondent;

    // =========================================================
    // AMBIL DATA RESPONDEN
    // =========================================================
    $Qry = $Conn->prepare("SELECT * FROM respondent WHERE id_respondent = ? LIMIT 1");
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

    $Qry->bind_param("i", $id_respondent);

    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka data responden!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if (!$Result || $Result->num_rows === 0) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Data responden tidak ditemukan!
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Data = $Result->fetch_assoc();
    $Qry->close();

    $id_pasien            = htmlspecialchars((string)($Data['id_pasien'] ?? '-'));
    $id_kunjungan         = htmlspecialchars((string)($Data['id_kunjungan'] ?? '-'));
    $respondent_name      = htmlspecialchars((string)($Data['respondent_name'] ?? '-'));
    $respondent_sex       = htmlspecialchars((string)($Data['respondent_sex'] ?? '-'));
    $respondent_brithdate = (string)($Data['respondent_brithdate'] ?? '');
    $tanggal_kunjungan    = (string)($Data['tanggal_kunjungan'] ?? '');
    $kunjungan_tujuan     = htmlspecialchars((string)($Data['kunjungan_tujuan'] ?? '-'));
    $no_kontak            = htmlspecialchars((string)($Data['no_kontak'] ?? '-'));

    $respondent_brithdate_format = '-';
    if (!empty($respondent_brithdate) && strtotime($respondent_brithdate) !== false) {
        $respondent_brithdate_format = date('d/m/Y', strtotime($respondent_brithdate));
    }

    $tanggal_kunjungan_format = '-';
    if (!empty($tanggal_kunjungan) && strtotime($tanggal_kunjungan) !== false) {
        $tanggal_kunjungan_format = date('d/m/Y H:i', strtotime($tanggal_kunjungan));
    }

    if (empty($no_kontak)) {
        $no_kontak = '-';
    }

    // =========================================================
    // AMBIL DATA LOG UNDANGAN
    // =========================================================
    $Qry = $Conn->prepare("SELECT * FROM survey_log WHERE id_respondent = ? LIMIT 1");
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

    $Qry->bind_param("i", $id_respondent);

    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka data log jawaban!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();
    $survey_log = null;

    if ($Result && $Result->num_rows > 0) {
        $survey_log = $Result->fetch_assoc();
    }

    $Qry->close();

    $invitation_token    = '-';
    $datetime_invitation = '-';
    $method_invitation   = '-';
    $datetime_answer     = '-';
    $answer_status       = '<span class="badge bg-danger text-light">Belum</span>';

    if (is_array($survey_log)) {
        $invitation_token  = htmlspecialchars((string)($survey_log['invitation_token'] ?? '-'));
        $method_invitation = htmlspecialchars((string)($survey_log['method_invitation'] ?? '-'));

        if (!empty($survey_log['datetime_invitation']) && strtotime($survey_log['datetime_invitation']) !== false) {
            $datetime_invitation = date('d/m/Y H:i', strtotime($survey_log['datetime_invitation']));
        }

        if (!empty($survey_log['datetime_answer']) && strtotime($survey_log['datetime_answer']) !== false) {
            $datetime_answer = date('d/m/Y H:i', strtotime($survey_log['datetime_answer']));
        }

        if ((int)($survey_log['answer'] ?? 0) === 1) {
            $answer_status = '<span class="badge bg-success text-light">Selesai</span>';
        }
    }

    // =========================================================
    // AMBIL DAFTAR PERTANYAAN DAN JAWABAN
    // =========================================================
    $sql = "
        SELECT
            q.id_survey_question,
            q.question_order,
            q.question_type,
            q.mandatory,
            q.question_text,
            q.alternative_answers,
            q.status,
            a.answer_text
        FROM survey_question q
        LEFT JOIN survey_answer a
            ON a.id_survey_question = q.id_survey_question
           AND a.id_respondent = ?
        WHERE q.status = 1
        ORDER BY q.question_order ASC, q.id_survey_question ASC
    ";
    $Qry = $Conn->prepare($sql);
    if (!$Qry) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat mempersiapkan daftar jawaban!<br>
                    Keterangan : ' . htmlspecialchars($Conn->error) . '
                </small>
            </div>
        ';
        exit;
    }

    $Qry->bind_param("i", $id_respondent);

    if (!$Qry->execute()) {
        echo '
            <div class="alert alert-danger text-center mb-3">
                <small>
                    <b>Opss!</b><br>
                    Terjadi kesalahan pada saat membuka daftar jawaban!<br>
                    Keterangan : ' . htmlspecialchars($Qry->error) . '
                </small>
            </div>
        ';
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();
    $rows = [];

    if ($Result) {
        while ($row = $Result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    $Qry->close();
?>
<?php
    function RenderAnswerLabel($questionType, $answerText, $alternativeAnswers){
        $questionType = strtolower(trim((string) $questionType));
        $answerText = trim((string) $answerText);

        if ($answerText === '') {
            return '<span class="text-muted">-</span>';
        }

        if ($questionType === 'boolean') {
            if ($answerText === '1') {
                return 'Ya';
            }
            if ($answerText === '0') {
                return 'Tidak';
            }
        }

        if ($questionType === 'coded' && !empty($alternativeAnswers)) {
            $decoded = json_decode($alternativeAnswers, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $value = (string)($item['value'] ?? '');
                    if ($value !== '' && $value === $answerText) {
                        $label = (string)($item['label'] ?? '');
                        if ($label !== '') {
                            return htmlspecialchars($label);
                        }
                    }
                }
            }
        }

        return htmlspecialchars($answerText);
    }
?>
<div class="row mb-3">
    <div class="col-12">
        <small class="fw-semibold">A. Informasi Responden</small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>No. RM</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $id_pasien; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>ID Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $id_kunjungan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Nama Responden</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $respondent_name; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Gender</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $respondent_sex; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Tanggal Lahir</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $respondent_brithdate_format; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Tanggal Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $tanggal_kunjungan_format; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Tujuan Kunjungan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $kunjungan_tujuan; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>No. Kontak</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $no_kontak; ?></small></div>
</div>

<hr class="my-4">

<div class="row mb-3">
    <div class="col-12">
        <small class="fw-semibold">B. Informasi Jawaban</small>
    </div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Status Jawaban</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><?php echo $answer_status; ?></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Kode Akses</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $invitation_token; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Metode Undangan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $method_invitation; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Datetime Undangan</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $datetime_invitation; ?></small></div>
</div>
<div class="row mb-2">
    <div class="col-5"><small>Datetime Jawaban</small></div>
    <div class="col-1"><small>:</small></div>
    <div class="col-6"><small class="text-grayish"><?php echo $datetime_answer; ?></small></div>
</div>
<hr>
<div class="row mt-4">
    <div class="col-12">
        <?php if (count($rows) === 0) { ?>
            <div class="alert alert-warning text-center mb-0">
                <small>Tidak ada pertanyaan aktif yang ditemukan.</small>
            </div>
        <?php } else { ?>
            <div class="list-group list-group-flush">
                <?php
                    $no = 1;
                    foreach ($rows as $row) {
                        $question_text = htmlspecialchars((string)($row['question_text'] ?? '-'));
                        $question_type = htmlspecialchars((string)($row['question_type'] ?? '-'));
                        $mandatory_badge = ((int)($row['mandatory'] ?? 0) === 1)
                            ? '<span class="badge bg-warning text-dark">Wajib</span>'
                            : '<span class="badge bg-secondary text-light">Opsional</span>';

                        $raw_answer = (string)($row['answer_text'] ?? '');
                        $answer_text = RenderAnswerLabel(
                            $row['question_type'] ?? '',
                            $raw_answer,
                            $row['alternative_answers'] ?? ''
                        );
                ?>
                    <div class="list-group-item px-0 py-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <div>
                                <div class="fw-semibold small mb-1">
                                    <?php echo $no . '. ' . $question_text; ?>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border"><?php echo $question_type; ?></span>
                                    <?php echo $mandatory_badge; ?>
                                </div>
                            </div>
                        </div>
                        <div class="bg-light rounded-3 px-3 py-2 border">
                            <small class="text-muted d-block mb-1">Jawaban</small>
                            <div class="fw-medium text-dark">
                                <?php echo $answer_text; ?>
                            </div>
                        </div>
                    </div>
                <?php
                        $no++;
                    }
                ?>
            </div>
        <?php } ?>
    </div>
</div>
