<?php
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";
    require_once "../../vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;

    date_default_timezone_set("Asia/Jakarta");

    if (empty($SessionIdAkses)) {
        echo '<div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;"><b>Opss!</b><br>Sesi akses sudah berakhir. Silakan login ulang.</div>';
        exit;
    }

    $periode_awal = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');

    $filterAwal = null;
    $filterAkhir = null;

    if ($periode_awal !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_awal);
        if (!$dt || $dt->format('Y-m-d') !== $periode_awal) {
            echo '<div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;"><b>Opss!</b><br>Periode awal tidak valid.</div>';
            exit;
        }
        $filterAwal = $dt->format('Y-m-d 00:00:00');
    }

    if ($periode_akhir !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_akhir);
        if (!$dt || $dt->format('Y-m-d') !== $periode_akhir) {
            echo '<div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;"><b>Opss!</b><br>Periode akhir tidak valid.</div>';
            exit;
        }
        $filterAkhir = $dt->format('Y-m-d 23:59:59');
    }

    if ($filterAwal && $filterAkhir && strtotime($filterAwal) > strtotime($filterAkhir)) {
        echo '<div style="padding:16px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;text-align:center;"><b>Opss!</b><br>Periode awal tidak boleh lebih besar dari periode akhir.</div>';
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
        SELECT r.id_pasien, r.respondent_name, sl.datetime_invitation, sl.method_invitation, sl.no_wa, sl.email
        FROM respondent r
        LEFT JOIN survey_log sl ON sl.id_respondent = r.id_respondent
        $where
        ORDER BY sl.datetime_invitation DESC, r.id_pasien ASC
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
        exit;
    }

    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Deskripsi Responden');

    $title = 'Laporan Deskripsi Responden';
    if ($filterAwal || $filterAkhir) {
        $title .= ' - Periode Export';
    }
    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $headers = ['No', 'No.RM', 'Nama Responden', 'Tanggal Undangan', 'Metode', 'No. WA', 'Email', 'Status'];
    $headerRow = 3;
    foreach ($headers as $i => $header) {
        $sheet->setCellValueByColumnAndRow($i + 1, $headerRow, $header);
    }
    $headerRange = 'A3:H3';
    $sheet->getStyle($headerRange)->getFont()->setBold(true);
    $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAF7');
    $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rowNum = 4;
    $no = 1;
    foreach ($rows as $row) {
        $sheet->setCellValueExplicit("A{$rowNum}", $no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $sheet->setCellValueExplicit("B{$rowNum}", (string)($row['id_pasien'] ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("C{$rowNum}", (string)($row['respondent_name'] ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("D{$rowNum}", !empty($row['datetime_invitation']) ? date('d/m/Y H:i', strtotime($row['datetime_invitation'])) : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("E{$rowNum}", !empty($row['method_invitation']) ? (string)$row['method_invitation'] : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("F{$rowNum}", !empty($row['no_wa']) ? (string)$row['no_wa'] : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("G{$rowNum}", !empty($row['email']) ? (string)$row['email'] : '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("H{$rowNum}", !empty($row['datetime_invitation']) ? 'Terkirim' : 'Belum', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $rowNum++;
        $no++;
    }

    $lastRow = max(3, $rowNum - 1);
    $sheet->getStyle("A3:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("D4:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("H4:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A4:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    $sheet->getStyle("A4:H{$lastRow}")->getAlignment()->setWrapText(true);

    foreach (range('A', 'H') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $filename = 'deskripsi_responden_' . date('Ymd_His') . '.xlsx';
    $tmpFile = tempnam(sys_get_temp_dir(), 'deskripsi_responden_');
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
