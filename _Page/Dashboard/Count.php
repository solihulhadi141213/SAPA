<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Sesi akses sudah berakhir. Silakan login ulang."
        ]);
        exit;
    }

    $response = [
        "status"             => "success",
        "message"            => "Data dashboard berhasil dimuat.",
        "jumlah_pertanyaan"  => "0",
        "jumlah_responden"   => "0",
        "jumlah_undangan"    => "0",
        "jumlah_jawaban"     => "0",
        "chart_gap_labels"   => [],
        "chart_gap_series"   => [],
        "chart_gender_labels" => [],
        "chart_gender_series" => [],
        "chart_encounter_labels" => [],
        "chart_encounter_series" => []
    ];

    $queries = [
        "jumlah_pertanyaan" => "SELECT COUNT(*) AS total FROM survey_question",
        "jumlah_responden"  => "SELECT COUNT(*) AS total FROM respondent",
        "jumlah_undangan"   => "SELECT COUNT(*) AS total FROM survey_log",
        "jumlah_jawaban"    => "SELECT COUNT(*) AS total FROM survey_log WHERE answer = 1"
    ];

    foreach ($queries as $key => $sql) {
        $stmt = $Conn->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                "status"  => "error",
                "message" => "Gagal mempersiapkan query untuk {$key}."
            ]);
            exit;
        }

        if (!$stmt->execute()) {
            echo json_encode([
                "status"  => "error",
                "message" => "Gagal mengambil data dashboard."
            ]);
            $stmt->close();
            exit;
        }

        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : [];
        $response[$key] = number_format((int)($row['total'] ?? 0), 0, ',', '.');
        $stmt->close();
    }

    $chartLabels = [];
    $chartSeries = [];
    $bulanMap = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    for ($i = 11; $i >= 0; $i--) {
        $monthDate = strtotime("-{$i} months");
        $monthKey = date('Y-m', $monthDate);
        $monthLabel = $bulanMap[(int)date('n', $monthDate)] . ' ' . date('Y', $monthDate);

        $chartLabels[] = $monthLabel;
        $chartSeries[$monthKey] = 0;
    }

    $stmtChart = $Conn->prepare("
        SELECT DATE_FORMAT(datetime_answer, '%Y-%m') AS bulan, COUNT(*) AS total
        FROM survey_log
        WHERE answer = 1
          AND datetime_answer IS NOT NULL
          AND datetime_answer >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
        GROUP BY DATE_FORMAT(datetime_answer, '%Y-%m')
        ORDER BY bulan ASC
    ");

    if (!$stmtChart) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan data grafik."
        ]);
        exit;
    }

    if (!$stmtChart->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mengambil data grafik."
        ]);
        $stmtChart->close();
        exit;
    }

    $resultChart = $stmtChart->get_result();
    while ($row = $resultChart->fetch_assoc()) {
        $bulan = (string)($row['bulan'] ?? '');
        if (array_key_exists($bulan, $chartSeries)) {
            $chartSeries[$bulan] = (int)($row['total'] ?? 0);
        }
    }
    $stmtChart->close();

    $response['chart_labels'] = array_values($chartLabels);
    $response['chart_series'] = array_values($chartSeries);

    // =========================================================
    // GAP PARTISIPASI RESPONDEN
    // =========================================================
    $stmtGap = $Conn->prepare("
        SELECT
            COUNT(*) AS total_responden,
            SUM(CASE WHEN sl.answer = 1 THEN 1 ELSE 0 END) AS total_menjawab
        FROM respondent r
        LEFT JOIN survey_log sl ON sl.id_respondent = r.id_respondent
    ");
    if (!$stmtGap) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan data gap partisipasi."
        ]);
        exit;
    }
    if (!$stmtGap->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mengambil data gap partisipasi."
        ]);
        $stmtGap->close();
        exit;
    }
    $resultGap = $stmtGap->get_result();
    $rowGap = $resultGap ? $resultGap->fetch_assoc() : [];
    $stmtGap->close();

    $totalResponden = (int)($rowGap['total_responden'] ?? 0);
    $totalMenjawab = (int)($rowGap['total_menjawab'] ?? 0);
    $response['chart_gap_labels'] = ['Belum Menjawab', 'Sudah Menjawab'];
    $response['chart_gap_series'] = [
        max(0, $totalResponden - $totalMenjawab),
        $totalMenjawab
    ];

    // =========================================================
    // GENDER RESPONDEN
    // =========================================================
    $response['chart_gender_labels'] = ['Pria', 'Wanita'];
    $response['chart_gender_series'] = [0, 0];

    $stmtGender = $Conn->prepare("
        SELECT respondent_sex, COUNT(*) AS total
        FROM respondent
        GROUP BY respondent_sex
    ");
    if (!$stmtGender) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan data gender."
        ]);
        exit;
    }
    if (!$stmtGender->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mengambil data gender."
        ]);
        $stmtGender->close();
        exit;
    }
    $resultGender = $stmtGender->get_result();
    while ($row = $resultGender->fetch_assoc()) {
        $sex = (string)($row['respondent_sex'] ?? '');
        $total = (int)($row['total'] ?? 0);
        if ($sex === 'Male') {
            $response['chart_gender_series'][0] = $total;
        } elseif ($sex === 'Female') {
            $response['chart_gender_series'][1] = $total;
        }
    }
    $stmtGender->close();

    // =========================================================
    // ENCOUNTER RESPONDEN
    // =========================================================
    $response['chart_encounter_labels'] = ['Rajal', 'Ranap'];
    $response['chart_encounter_series'] = [0, 0];

    $stmtEncounter = $Conn->prepare("
        SELECT kunjungan_tujuan, COUNT(*) AS total
        FROM respondent
        GROUP BY kunjungan_tujuan
    ");
    if (!$stmtEncounter) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mempersiapkan data encounter."
        ]);
        exit;
    }
    if (!$stmtEncounter->execute()) {
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal mengambil data encounter."
        ]);
        $stmtEncounter->close();
        exit;
    }
    $resultEncounter = $stmtEncounter->get_result();
    while ($row = $resultEncounter->fetch_assoc()) {
        $tujuan = (string)($row['kunjungan_tujuan'] ?? '');
        $total = (int)($row['total'] ?? 0);
        if ($tujuan === 'Rajal') {
            $response['chart_encounter_series'][0] = $total;
        } elseif ($tujuan === 'Ranap') {
            $response['chart_encounter_series'][1] = $total;
        }
    }
    $stmtEncounter->close();

    echo json_encode($response);
?>
