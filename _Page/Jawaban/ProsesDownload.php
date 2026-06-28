<?php
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    require_once "../../vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;

    date_default_timezone_set("Asia/Jakarta");

    if (empty($SessionIdAkses)) {
        echo '
            <div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;">
                <b>Opss!</b><br>Sesi akses sudah berakhir. Silakan login ulang.
            </div>
        ';
        exit;
    }

    $periode_awal = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');

    $filterAwal = null;
    $filterAkhir = null;

    if ($periode_awal !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_awal);
        if (!$dt || $dt->format('Y-m-d') !== $periode_awal) {
            echo '
                <div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;">
                    <b>Opss!</b><br>Periode awal tidak valid.
                </div>
            ';
            exit;
        }
        $filterAwal = $dt->format('Y-m-d 00:00:00');
    }

    if ($periode_akhir !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_akhir);
        if (!$dt || $dt->format('Y-m-d') !== $periode_akhir) {
            echo '
                <div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;">
                    <b>Opss!</b><br>Periode akhir tidak valid.
                </div>
            ';
            exit;
        }
        $filterAkhir = $dt->format('Y-m-d 23:59:59');
    }

    if ($filterAwal && $filterAkhir && strtotime($filterAwal) > strtotime($filterAkhir)) {
        echo '
            <div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;">
                <b>Opss!</b><br>Periode awal tidak boleh lebih besar dari periode akhir.
            </div>
        ';
        exit;
    }

    $questionSql = "SELECT id_survey_question, question_order, question_text, question_type, alternative_answers FROM survey_question WHERE status = 1 ORDER BY question_order ASC, id_survey_question ASC";
    $stmtQuestion = $Conn->prepare($questionSql);
    if (!$stmtQuestion) {
        echo 'Gagal mempersiapkan data pertanyaan.';
        exit;
    }
    if (!$stmtQuestion->execute()) {
        echo 'Gagal membuka data pertanyaan.';
        $stmtQuestion->close();
        exit;
    }
    $resultQuestion = $stmtQuestion->get_result();
    $questions = [];
    while ($row = $resultQuestion->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmtQuestion->close();

    if (count($questions) === 0) {
        echo '
            <div style="padding:16px;border:1px solid #ffe69c;background:#fff3cd;color:#664d03;border-radius:8px;text-align:center;">
                <b>Info</b><br>Tidak ada pertanyaan aktif untuk diekspor.
            </div>
        ';
        exit;
    }

    $where = "";
    $bindTypes = "";
    $bindValues = [];

    if ($filterAwal !== null) {
        $where .= " WHERE sl.datetime_invitation >= ? ";
        $bindTypes .= "s";
        $bindValues[] = $filterAwal;
    }
    if ($filterAkhir !== null) {
        $where .= ($where === "") ? " WHERE " : " AND ";
        $where .= " sl.datetime_invitation <= ? ";
        $bindTypes .= "s";
        $bindValues[] = $filterAkhir;
    }

    $sql = "
        SELECT
            r.id_respondent,
            r.id_pasien,
            r.respondent_name,
            sl.datetime_invitation,
            sl.datetime_answer,
            sl.answer AS survey_status,
            q.id_survey_question,
            q.question_order,
            q.question_type,
            q.alternative_answers,
            a.answer_text
        FROM respondent r
        LEFT JOIN survey_log sl ON sl.id_respondent = r.id_respondent
        LEFT JOIN survey_answer a ON a.id_respondent = r.id_respondent
        LEFT JOIN survey_question q ON q.id_survey_question = a.id_survey_question
        $where
        ORDER BY r.id_respondent ASC, q.question_order ASC, q.id_survey_question ASC
    ";

    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        echo 'Gagal mempersiapkan query data.';
        exit;
    }

    if (!empty($bindValues)) {
        $stmt->bind_param($bindTypes, ...$bindValues);
    }

    if (!$stmt->execute()) {
        echo 'Gagal membuka data untuk export.';
        $stmt->close();
        exit;
    }

    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    $respondents = [];
    foreach ($rows as $row) {
        $rid = (int)($row['id_respondent'] ?? 0);
        if ($rid <= 0) {
            continue;
        }

        if (!isset($respondents[$rid])) {
            $respondents[$rid] = [
                'id_respondent' => $rid,
                'id_pasien' => (string)($row['id_pasien'] ?? ''),
                'respondent_name' => (string)($row['respondent_name'] ?? ''),
                'datetime_invitation' => (string)($row['datetime_invitation'] ?? ''),
                'datetime_answer' => (string)($row['datetime_answer'] ?? ''),
                'answers' => []
            ];
        }

        $qid = (int)($row['id_survey_question'] ?? 0);
        if ($qid > 0) {
            $respondents[$rid]['answers'][$qid] = [
                'answer_text' => (string)($row['answer_text'] ?? ''),
                'question_type' => (string)($row['question_type'] ?? ''),
                'alternative_answers' => (string)($row['alternative_answers'] ?? '')
            ];
        }
    }

    function RenderExportAnswer($questionType, $answerText, $alternativeAnswers) {
        $questionType = strtolower(trim((string) $questionType));
        $answerText = trim((string) $answerText);

        if ($answerText === '') {
            return '';
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
                            return $label;
                        }
                    }
                }
            }
        }

        return $answerText;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Jawaban');

    $title = 'Data Responden dan Jawaban';
    if ($filterAwal || $filterAkhir) {
        $title .= ' - Periode Export';
    }

    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + count($questions)) . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rangeEndCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6 + count($questions));
    $headerRow = 3;
    $sheet->setCellValue('A' . $headerRow, 'No');
    $sheet->setCellValue('B' . $headerRow, 'No.RM');
    $sheet->setCellValue('C' . $headerRow, 'Nama Responden');
    $sheet->setCellValue('D' . $headerRow, 'Tanggal Undangan');
    $sheet->setCellValue('E' . $headerRow, 'Tanggal Jawaban');
    $sheet->setCellValue('F' . $headerRow, 'Status');

    $colIndex = 7;
    foreach ($questions as $question) {
        $questionNumber = (int)($question['question_order'] ?? 0);
        $questionText = trim((string)($question['question_text'] ?? ''));
        $cellValue = 'Item ' . $questionNumber;
        if ($questionText !== '') {
            $cellValue = 'Item ' . $questionNumber;
        }
        $sheet->setCellValueByColumnAndRow($colIndex, $headerRow, $cellValue);
        $sheet->getCommentByColumnAndRow($colIndex, $headerRow)->getText()->createTextRun('Pertanyaan: ' . $questionText);
        $colIndex++;
    }

    $headerRange = 'A' . $headerRow . ':' . $rangeEndCol . $headerRow;
    $sheet->getStyle($headerRange)->getFont()->setBold(true);
    $sheet->getStyle($headerRange)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('D9EAF7');
    $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getRowDimension($headerRow)->setRowHeight(24);

    $rowNum = 4;
    $no = 1;
    foreach ($respondents as $respondent) {
        $sheet->setCellValueExplicit("A{$rowNum}", $no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $sheet->setCellValueExplicit("B{$rowNum}", $respondent['id_pasien'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("C{$rowNum}", $respondent['respondent_name'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("D{$rowNum}", !empty($respondent['datetime_invitation']) ? date('d/m/Y H:i', strtotime($respondent['datetime_invitation'])) : '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("E{$rowNum}", !empty($respondent['datetime_answer']) ? date('d/m/Y H:i', strtotime($respondent['datetime_answer'])) : '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("F{$rowNum}", ((int)($respondent['survey_status'] ?? 0) === 1) ? 'Selesai' : 'Belum', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        $colIndex = 7;
        foreach ($questions as $question) {
            $qid = (int)($question['id_survey_question'] ?? 0);
            $answer = $respondent['answers'][$qid]['answer_text'] ?? '';
            $qType = $respondent['answers'][$qid]['question_type'] ?? ($question['question_type'] ?? '');
            $alts = $respondent['answers'][$qid]['alternative_answers'] ?? ($question['alternative_answers'] ?? '');
            $sheet->setCellValueExplicitByColumnAndRow(
                $colIndex,
                $rowNum,
                RenderExportAnswer($qType, $answer, $alts),
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $colIndex++;
        }

        $rowNum++;
        $no++;
    }

    $lastRow = $rowNum - 1;
    if ($lastRow >= 4) {
        $sheet->getStyle("A3:{$rangeEndCol}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("D4:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("F4:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A4:{$rangeEndCol}{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    $sheet->getStyle("A4:{$rangeEndCol}{$lastRow}")->getAlignment()->setWrapText(true);

    foreach (range(1, 6 + count($questions)) as $colNum) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colNum);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $filename = 'jawaban_' . date('Ymd_His') . '.xlsx';
    $tmpFile = tempnam(sys_get_temp_dir(), 'jawaban_');
    if ($tmpFile === false) {
        echo 'Gagal membuat file sementara.';
        exit;
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($tmpFile);

    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmpFile));

    readfile($tmpFile);
    @unlink($tmpFile);
    exit;
?>
