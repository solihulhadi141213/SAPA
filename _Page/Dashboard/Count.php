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
        "chart_labels"       => [],
        "chart_series"       => []
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

    echo json_encode($response);
?>
