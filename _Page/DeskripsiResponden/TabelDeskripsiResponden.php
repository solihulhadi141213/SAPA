<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    if (empty($SessionIdAkses)) {
        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <small>Sesi akses sudah berakhir. Silakan login ulang.</small>
                    </td>
                </tr>
            '
        ]);
        exit;
    }

    $page  = (int)($_POST['page'] ?? 1);
    $batas = (int)($_POST['batas'] ?? 10);
    $OrderBy = $_POST['OrderBy'] ?? 'datetime_invitation';
    $ShortBy = strtoupper($_POST['ShortBy'] ?? 'DESC');
    $periode_awal = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');
    $keyword = trim($_POST['keyword'] ?? '');

    if ($page <= 0) $page = 1;
    if ($batas <= 0) $batas = 10;

    $allowedOrder = [
        'id_pasien' => 'r.id_pasien',
        'respondent_name' => 'r.respondent_name',
        'datetime_invitation' => 'sl.datetime_invitation',
        'method_invitation' => 'sl.method_invitation',
        'no_wa' => 'sl.no_wa',
        'email' => 'sl.email'
    ];
    if (!isset($allowedOrder[$OrderBy])) $OrderBy = 'datetime_invitation';
    $OrderBy = $allowedOrder[$OrderBy];

    if (!in_array($ShortBy, ['ASC', 'DESC'], true)) $ShortBy = 'DESC';

    $filterAwal = null;
    $filterAkhir = null;

    if ($periode_awal !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_awal);
        if (!$dt || $dt->format('Y-m-d') !== $periode_awal) {
            echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Periode awal tidak valid.</small></td></tr>']);
            exit;
        }
        $filterAwal = $dt->format('Y-m-d 00:00:00');
    }

    if ($periode_akhir !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $periode_akhir);
        if (!$dt || $dt->format('Y-m-d') !== $periode_akhir) {
            echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Periode akhir tidak valid.</small></td></tr>']);
            exit;
        }
        $filterAkhir = $dt->format('Y-m-d 23:59:59');
    }

    if ($filterAwal && $filterAkhir && strtotime($filterAwal) > strtotime($filterAkhir)) {
        echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Periode awal tidak boleh lebih besar dari periode akhir.</small></td></tr>']);
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
    if ($keyword !== '') {
        $keywordLike = "%{$keyword}%";
        $where .= ($where === "") ? " WHERE " : " AND ";
        $where .= " (r.id_pasien LIKE ? OR r.respondent_name LIKE ? OR sl.method_invitation LIKE ? OR sl.no_wa LIKE ? OR sl.email LIKE ?) ";
        $bindTypes .= "sssss";
        array_push($bindValues, $keywordLike, $keywordLike, $keywordLike, $keywordLike, $keywordLike);
    }

    $sql_count = "SELECT COUNT(*) AS total FROM respondent r LEFT JOIN survey_log sl ON sl.id_respondent=r.id_respondent $where";
    $stmt_count = $Conn->prepare($sql_count);
    if (!$stmt_count) {
        echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Gagal mempersiapkan query count.</small></td></tr>']);
        exit;
    }
    if (!empty($bindValues)) {
        $stmt_count->bind_param($bindTypes, ...$bindValues);
    }
    $stmt_count->execute();
    $total_data = (int)($stmt_count->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt_count->close();

    $total_page = ($total_data > 0) ? ceil($total_data / $batas) : 1;
    if ($page > $total_page) $page = $total_page;
    $posisi = ($page - 1) * $batas;

    $sql = "
        SELECT r.id_respondent, r.id_pasien, r.respondent_name, sl.datetime_invitation, sl.method_invitation, sl.no_wa, sl.email
        FROM respondent r
        LEFT JOIN survey_log sl ON sl.id_respondent = r.id_respondent
        $where
        ORDER BY $OrderBy $ShortBy
        LIMIT ?, ?
    ";
    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Gagal mempersiapkan query data.</small></td></tr>']);
        exit;
    }

    $bindTypesData = $bindTypes . "ii";
    $bindValuesData = $bindValues;
    $bindValuesData[] = $posisi;
    $bindValuesData[] = $batas;
    $stmt->bind_param($bindTypesData, ...$bindValuesData);
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "html" => '<tr><td colspan="8" class="text-center text-danger"><small>Terjadi kesalahan saat mengambil data.</small></td></tr>']);
        exit;
    }

    $query = $stmt->get_result();
    $html = '';
    $no = 1 + $posisi;
    if ($query->num_rows === 0) {
        $html .= '<tr><td colspan="8" class="text-center text-danger"><small>Tidak ada data yang ditampilkan.</small></td></tr>';
    } else {
        while ($data = $query->fetch_assoc()) {
            $datetime_invitation = !empty($data['datetime_invitation']) ? date('d/m/Y H:i', strtotime($data['datetime_invitation'])) : '-';
            $method_invitation = !empty($data['method_invitation']) ? htmlspecialchars($data['method_invitation']) : '-';
            $no_wa = !empty($data['no_wa']) ? htmlspecialchars($data['no_wa']) : '-';
            $email = !empty($data['email']) ? htmlspecialchars($data['email']) : '-';
            $status = !empty($data['datetime_invitation']) ? '<span class="badge bg-success">Terkirim</span>' : '<span class="badge bg-secondary">Belum</span>';
            $html .= '
                <tr>
                    <td class="text-center"><small>'.$no.'</small></td>
                    <td><small>'.htmlspecialchars((string)$data['id_pasien']).'</small></td>
                    <td><small>'.htmlspecialchars((string)$data['respondent_name']).'</small></td>
                    <td><small>'.$datetime_invitation.'</small></td>
                    <td><small>'.$method_invitation.'</small></td>
                    <td><small>'.$no_wa.'</small></td>
                    <td><small>'.$email.'</small></td>
                    <td class="text-center">'.$status.'</td>
                </tr>
            ';
            $no++;
        }
    }

    $stmt->close();

    echo json_encode([
        "status" => "success",
        "html" => $html,
        "page" => $page,
        "total_page" => $total_page,
        "total_data" => $total_data
    ]);
?>
