<?php
    include "_Config/Connection.php";
    include "_Config/Setting.php";
    include "_Config/Helper.php";

    date_default_timezone_set("Asia/Jakarta");

    $env_version = "";
    if ($environment_status !== "Production") {
        $env_version = date('YmdHis');
    }

    $token = trim($_GET['token'] ?? '');
    $page_status = 'error';
    $page_heading = 'Survei Kepuasan';
    $page_message = 'Tautan tidak valid atau sudah tidak tersedia.';
    $page_badge = 'Tautan Tidak Valid';
    $page_icon = 'bi-exclamation-triangle';
    $page_color = 'warning';
    $respondent_name = '-';
    $respondent_sex = '-';
    $datetime_invitation = '';
    $method_invitation = '';
    $invitation_token = '';
    $id_respondent = 0;
    $already_filled = false;
    $total_answer = 0;
    $survey_link = '';
    $questions = [];

    if ($token !== '') {
        $Qry = $Conn->prepare("SELECT * FROM survey_log WHERE invitation_token = ? LIMIT 1");
        if ($Qry) {
            $Qry->bind_param("s", $token);
            if ($Qry->execute()) {
                $Result = $Qry->get_result();
                if ($Result && $Result->num_rows > 0) {
                    $Data = $Result->fetch_assoc();
                    $id_respondent      = (int) ($Data['id_respondent'] ?? 0);
                    $invitation_token   = (string) ($Data['invitation_token'] ?? '');
                    $datetime_invitation = (string) ($Data['datetime_invitation'] ?? '');
                    $method_invitation  = (string) ($Data['method_invitation'] ?? '');
                    $respondent_name    = (string) GetDetailData($Conn, 'respondent', 'id_respondent', $id_respondent, 'respondent_name');
                    $respondent_sex     = (string) GetDetailData($Conn, 'respondent', 'id_respondent', $id_respondent, 'respondent_sex');

                    $stmt_answer = $Conn->prepare("SELECT COUNT(*) AS total_answer FROM survey_answer WHERE id_respondent = ?");
                    if ($stmt_answer) {
                        $stmt_answer->bind_param("i", $id_respondent);
                        if ($stmt_answer->execute()) {
                            $result_answer = $stmt_answer->get_result();
                            if ($result_answer) {
                                $row_answer = $result_answer->fetch_assoc();
                                $total_answer = (int) ($row_answer['total_answer'] ?? 0);
                                $already_filled = $total_answer > 0;
                            }
                        }
                        $stmt_answer->close();
                    }

                    if (!$already_filled) {
                        $qry_question = $Conn->prepare("SELECT * FROM survey_question WHERE status = 1 ORDER BY question_order ASC");
                        if ($qry_question && $qry_question->execute()) {
                            $result_question = $qry_question->get_result();
                            while ($row = $result_question->fetch_assoc()) {
                                $questions[] = $row;
                            }
                            $qry_question->close();
                        }
                    }

                    $page_status = $already_filled ? 'filled' : 'ready';
                    $page_heading = $already_filled ? 'Sesi Sudah Diisi' : 'Kuesioner Siap Diisi';
                    $page_message = $already_filled
                        ? 'Berdasarkan token ini, sesi pertanyaan sudah pernah diisi. Terima kasih atas partisipasinya.'
                        : 'Dengan hormat., Dalam rangka meningkatkan mutu pelayanan di RS, kami mohon partisipasi bapak/ibu/sdr secara sukarela untuk mengisi kuesioner dan menjawab pertanyaan secara jujur dan benar.Atas kerjasama bapak/ibu/sdr kami ucapkan terimakasih.';
                    $page_badge = $already_filled ? 'Sudah Diisi' : 'Tersedia';
                    $page_icon = $already_filled ? 'bi-check-circle-fill' : 'bi-clipboard2-check-fill';
                    $page_color = $already_filled ? 'success' : 'primary';
                    $survey_link = $base_url . '/Questionnaire.php?token=' . urlencode($invitation_token);
                } else {
                    $page_message = 'Token tidak ditemukan atau sudah tidak berlaku.';
                    $page_badge = 'Token Tidak Ditemukan';
                }
            }
            $Qry->close();
        } else {
            $page_message = 'Gagal mempersiapkan data survey.';
            $page_badge = 'Error';
        }
    } else {
        $page_message = 'Token belum dikirimkan ke halaman ini.';
        $page_badge = 'Token Kosong';
    }

    $tanggal_undangan = '';
    if (!empty($datetime_invitation)) {
        $timestamp = strtotime($datetime_invitation);
        if ($timestamp !== false) {
            $tanggal_undangan = date('d/m/Y H:i', $timestamp);
        }
    }

    $judul_halaman = 'Questionnaire';
?>
<!doctype html>
<html lang="id">
    <head>
        <?php include "_Partial/Head.php"; ?>
        <style>
            body{
                background:
                    radial-gradient(circle at top left, rgba(14,165,233,.18), transparent 28%),
                    radial-gradient(circle at top right, rgba(16,185,129,.14), transparent 22%),
                    linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
                min-height: 100vh;
            }
            .questionnaire-shell{
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 32px 0;
            }
            .questionnaire-card{
                border: 0;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(15, 23, 42, .12);
                background: rgba(255,255,255,.92);
                backdrop-filter: blur(10px);
            }
            .questionnaire-hero{
                background: linear-gradient(135deg, #0f766e 0%, #0ea5e9 100%);
                color: #fff;
                padding: 28px;
                height: 100%;
            }
            .questionnaire-hero .brand-mark{
                width: 72px;
                height: 72px;
                border-radius: 20px;
                background: rgba(255,255,255,.18);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                border: 1px solid rgba(255,255,255,.2);
            }
            .questionnaire-hero .brand-mark img{
                width: 46px;
                height: 46px;
                object-fit: contain;
            }
            .questionnaire-body{
                padding: 28px;
            }
            .status-pill{
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                border-radius: 999px;
                padding: .45rem .85rem;
                font-weight: 600;
                font-size: .875rem;
            }
            .soft-panel{
                background: #f8fbff;
                border: 1px solid rgba(15, 23, 42, .08);
                border-radius: 20px;
                padding: 18px;
            }
            .info-row{
                display: flex;
                justify-content: space-between;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px dashed rgba(15, 23, 42, .10);
            }
            .info-row:last-child{
                border-bottom: 0;
            }
            .info-label{
                color: #64748b;
                font-size: .9rem;
            }
            .info-value{
                font-weight: 600;
                text-align: right;
                color: #0f172a;
            }
            .copy-group .form-control{
                border-radius: 14px 0 0 14px;
            }
            .copy-group .btn{
                border-radius: 0 14px 14px 0;
            }
            .cta-btn{
                border-radius: 14px;
                padding: .8rem 1rem;
                font-weight: 600;
            }
            .muted-note{
                color: #64748b;
                font-size: .92rem;
            }
            .question-card{
                border: 1px solid rgba(15, 23, 42, .08);
                border-radius: 20px;
                padding: 18px;
                background: #fff;
            }
            .question-number{
                width: 38px;
                height: 38px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #e0f2fe;
                color: #0369a1;
                font-weight: 700;
                margin-right: 10px;
                flex: 0 0 auto;
            }
            .question-title{
                font-weight: 700;
                color: #0f172a;
            }
            .option-stack .form-check{
                padding: .8rem 1rem .8rem 2.25rem;
                border: 1px solid rgba(15, 23, 42, .08);
                border-radius: 14px;
                margin-bottom: .6rem;
                background: #fff;
            }
            .option-stack .form-check:last-child{
                margin-bottom: 0;
            }
            .sticky-submit{
                position: sticky;
                bottom: 16px;
                z-index: 5;
            }
        </style>
    </head>
    <body>
        <main class="questionnaire-shell">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <div class="card questionnaire-card">
                            <div class="row g-0">
                                <div class="col-lg-4">
                                    <div class="questionnaire-hero d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="brand-mark">
                                                <img src="assets/img/logo/<?= $company_logo ?>?v=<?php echo $env_version; ?>" alt="Logo <?= htmlspecialchars($company_name); ?>">
                                            </span>
                                            <div class="mb-3">
                                                <span class="status-pill bg-white text-<?= $page_color; ?>">
                                                    <i class="bi <?php echo $page_icon; ?>"></i>
                                                    <?php echo htmlspecialchars($page_badge); ?>
                                                </span>
                                            </div>
                                            <h1 class="display-6 fw-bold mb-3"><?php echo htmlspecialchars($company_name); ?></h1>
                                            <p class="mb-0 opacity-75"><?php echo htmlspecialchars($company_address); ?></p>
                                        </div>
                                        <div class="mt-4">
                                            <div class="small opacity-75 mb-2">Akses publik untuk pasien tanpa login.</div>
                                            <div class="small opacity-75">Gunakan tautan resmi dari rumah sakit untuk menjaga keamanan data.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="questionnaire-body">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                                            <div>
                                                <h2 class="h3 fw-bold mb-2"><?php echo htmlspecialchars($page_heading); ?></h2>
                                                <p class="muted-note mb-0"><?php echo htmlspecialchars($page_message); ?></p>
                                            </div>
                                            <span class="badge rounded-pill text-bg-<?php echo $page_color; ?>-subtle border border-<?php echo $page_color; ?>-subtle text-<?php echo $page_color; ?>">
                                                <?php echo htmlspecialchars($page_badge); ?>
                                            </span>
                                        </div>

                                        <?php if ($page_status === 'ready') { ?>
                                            <form action="javascript:void(0);" id="ProsesSubmitJawaban" autocomplete="off">
                                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($invitation_token); ?>">
                                                <input type="hidden" name="id_respondent" value="<?php echo (int) $id_respondent; ?>">

                                                <div class="soft-panel mb-4">
                                                    <div class="info-row">
                                                        <div class="info-label">Nama Responden</div>
                                                        <div class="info-value"><?php echo htmlspecialchars($respondent_name); ?></div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Jenis Kelamin</div>
                                                        <div class="info-value"><?php echo htmlspecialchars($respondent_sex); ?></div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Tanggal Undangan</div>
                                                        <div class="info-value"><?php echo !empty($tanggal_undangan) ? htmlspecialchars($tanggal_undangan) : '-'; ?></div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-label">Metode</div>
                                                        <div class="info-value"><?php echo htmlspecialchars($method_invitation ?: '-'); ?></div>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <?php if (count($questions) === 0) { ?>
                                                        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4">
                                                            Tidak ada pertanyaan aktif saat ini.
                                                        </div>
                                                    <?php } else { ?>
                                                        <?php foreach ($questions as $index => $question) {
                                                            $qid = (int) $question['id_survey_question'];
                                                            $qtype = (string) $question['question_type'];
                                                            $qtext = (string) $question['question_text'];
                                                            $mandatory = (int) $question['mandatory'];
                                                            $alts = [];
                                                            if ($qtype === 'coded' && !empty($question['alternative_answers'])) {
                                                                $decoded = json_decode($question['alternative_answers'], true);
                                                                if (is_array($decoded)) {
                                                                    $alts = $decoded;
                                                                }
                                                            }
                                                        ?>
                                                            <div class="question-card mb-3">
                                                                <div class="d-flex align-items-start mb-3">
                                                                    <div class="question-number"><?php echo $index + 1; ?></div>
                                                                    <div>
                                                                        <div class="question-title">
                                                                            <?php echo nl2br(htmlspecialchars($qtext)); ?>
                                                                            <?php if ($mandatory === 1) { ?>
                                                                                <span class="text-danger">*</span>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="small text-muted">Tipe: <?php echo htmlspecialchars($qtype); ?></div>
                                                                    </div>
                                                                </div>

                                                                <?php if ($qtype === 'text') { ?>
                                                                    <textarea class="form-control" name="answer[<?php echo $qid; ?>]" rows="3" <?php echo $mandatory === 1 ? 'required' : ''; ?>></textarea>
                                                                <?php } elseif ($qtype === 'number' || $qtype === 'decimal') { ?>
                                                                    <input type="number" class="form-control" name="answer[<?php echo $qid; ?>]" step="<?php echo $qtype === 'decimal' ? 'any' : '1'; ?>" <?php echo $mandatory === 1 ? 'required' : ''; ?>>
                                                                <?php } elseif ($qtype === 'boolean') { ?>
                                                                    <div class="option-stack">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="answer[<?php echo $qid; ?>]" value="1" id="q<?php echo $qid; ?>_yes" <?php echo $mandatory === 1 ? 'required' : ''; ?>>
                                                                            <label class="form-check-label" for="q<?php echo $qid; ?>_yes">Ya</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="answer[<?php echo $qid; ?>]" value="0" id="q<?php echo $qid; ?>_no" <?php echo $mandatory === 1 ? 'required' : ''; ?>>
                                                                            <label class="form-check-label" for="q<?php echo $qid; ?>_no">Tidak</label>
                                                                        </div>
                                                                    </div>
                                                                <?php } elseif ($qtype === 'coded') { ?>
                                                                    <div class="option-stack">
                                                                        <?php if (count($alts) > 0) {
                                                                            foreach ($alts as $optIndex => $alt) {
                                                                                $label = (string) ($alt['label'] ?? '');
                                                                                $value = (string) ($alt['value'] ?? '');
                                                                        ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio" name="answer[<?php echo $qid; ?>]" value="<?php echo htmlspecialchars($value); ?>" id="q<?php echo $qid . '_' . $optIndex; ?>" <?php echo $mandatory === 1 ? 'required' : ''; ?>>
                                                                                <label class="form-check-label" for="q<?php echo $qid . '_' . $optIndex; ?>">
                                                                                    <?php echo htmlspecialchars($label); ?>
                                                                                </label>
                                                                            </div>
                                                                        <?php }
                                                                        } else { ?>
                                                                            <div class="alert alert-light border mb-0">Alternatif jawaban belum tersedia.</div>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <input type="text" class="form-control" name="answer[<?php echo $qid; ?>]" <?php echo $mandatory === 1 ? 'required' : ''; ?>>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>

                                                <div class="sticky-submit">
                                                    <div class="soft-panel mb-3">
                                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                            <div class="muted-note">
                                                                Pastikan jawaban sudah benar sebelum dikirim.
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <button type="submit" class="btn btn-primary cta-btn" id="ButtonSubmitJawaban">
                                                                    <i class="bi bi-send me-2"></i>Kirim Jawaban
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="NotifikasiSubmitJawaban"></div>
                                            </form>
                                        <?php } elseif ($page_status === 'filled') { ?>
                                            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="fs-1 lh-1">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="h5 fw-bold mb-2">Sesi pertanyaan sudah diisi</h3>
                                                        <p class="mb-0">
                                                            Berdasarkan token ini, sesi pertanyaan sudah pernah diisi sebanyak
                                                            <b><?php echo (int)$total_answer; ?></b> jawaban.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="soft-panel mb-4">
                                                <div class="info-row">
                                                    <div class="info-label">Nama Responden</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($respondent_name); ?></div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">Tanggal Undangan</div>
                                                    <div class="info-value"><?php echo !empty($tanggal_undangan) ? htmlspecialchars($tanggal_undangan) : '-'; ?></div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">Metode</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($method_invitation ?: '-'); ?></div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">Token</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($invitation_token ?: '-'); ?></div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="fs-1 lh-1">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="h5 fw-bold mb-2">Token tidak valid</h3>
                                                        <p class="mb-0">
                                                            <?php echo htmlspecialchars($page_message); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="mt-4 text-muted small">
                                            <?php echo htmlspecialchars($company_name); ?> |
                                            <?php echo htmlspecialchars($company_phone); ?> |
                                            <?php echo htmlspecialchars($company_email); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include "_Partial/FooterJs.php"; ?>
        <script>
            $(document).on('submit', '#ProsesSubmitJawaban', function(e){
                e.preventDefault();

                const tombol = $('#ButtonSubmitJawaban');
                const html_awal = tombol.html();

                $('#NotifikasiSubmitJawaban').html('');
                tombol.prop('disabled', true);
                tombol.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');

                $.ajax({
                    type: 'POST',
                    url: '_Page/Questionnaire/ProsesSubmitJawaban.php',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(response){
                        if (response.status === 'success') {
                            $('#NotifikasiSubmitJawaban').html('<div class="alert alert-success border-0 shadow-sm rounded-4">'+response.message+'</div>');
                            setTimeout(function(){
                                window.location.reload();
                            }, 1500);
                        } else {
                            $('#NotifikasiSubmitJawaban').html('<div class="alert alert-danger border-0 shadow-sm rounded-4">'+response.message+'</div>');
                        }
                    },
                    error: function(xhr){
                        $('#NotifikasiSubmitJawaban').html('<div class="alert alert-danger border-0 shadow-sm rounded-4">Terjadi kesalahan server.</div>');
                    },
                    complete: function(){
                        tombol.prop('disabled', false);
                        tombol.html(html_awal);
                    }
                });
            });
        </script>
    </body>
</html>
