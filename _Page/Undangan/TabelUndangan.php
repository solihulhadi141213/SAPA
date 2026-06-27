<?php
    header('Content-Type: application/json');

    // =========================================================
    // CONNECTION & SESSION
    // =========================================================
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    // =========================================================
    // VALIDASI SESSION
    // =========================================================
    if (empty($SessionIdAkses)) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <small>Sesi akses sudah berakhir. Silakan login ulang.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    // =========================================================
    // PARAMETER
    // =========================================================
    $page       = $_POST['page'] ?? 1;
    $batas      = $_POST['batas'] ?? 10;
    $OrderBy    = $_POST['OrderBy'] ?? 'id_respondent';
    $ShortBy    = $_POST['ShortBy'] ?? 'ASC';
    $keyword_by = $_POST['keyword_by'] ?? '';
    $keyword    = trim($_POST['keyword'] ?? '');

    // =========================================================
    // VALIDASI PAGE & LIMIT
    // =========================================================
    $page  = (int)$page;
    $batas = (int)$batas;

    if ($page <= 0) {
        $page = 1;
    }

    if ($batas <= 0) {
        $batas = 10;
    }

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // VALIDASI ORDER BY
    // =========================================================
    $allowedOrder = [
        'id_respondent'=>'r.id_respondent',
        'id_pasien'=>'r.id_pasien',
        'respondent_name'=>'r.respondent_name',
        'datetime_invitation'=>'sl.datetime_invitation',
        'method_invitation'=>'sl.method_invitation',
        'no_wa'=>'sl.no_wa',
        'email'=>'sl.email'
    ];

    if (!array_key_exists($OrderBy,$allowedOrder)) {$OrderBy='id_respondent';}
    $OrderBy=$allowedOrder[$OrderBy];

    // =========================================================
    // VALIDASI SORT
    // =========================================================
    $ShortBy = strtoupper($ShortBy);

    if (!in_array($ShortBy, ['ASC', 'DESC'])) {
        $ShortBy = 'ASC';
    }

    // =========================================================
    // VALIDASI FILTER
    // =========================================================
    $allowedKeywordBy = [
        'id_respondent',
        'id_pasien',
        'respondent_name',
        'datetime_invitation',
        'method_invitation',
        'no_wa',
        'email'
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where      = "";
    $bindTypes  = "";
    $bindValues = [];

    if (!empty($keyword)) {
        $keywordLike = "%" . $keyword . "%";
        if (!empty($keyword_by)) {

            $where .= " WHERE $keyword_by LIKE ? ";

            $bindTypes .= "s";
            $bindValues[] = $keywordLike;

        } else {

            $where .= "
                WHERE (
                    id_pasien LIKE ? OR 
                    respondent_name LIKE ?
                )
            ";

            $bindTypes .= "ss";

            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
        }
    }

    // =========================================================
    // TOTAL DATA
    // =========================================================
    $sql_count = "SELECT COUNT(*) AS total FROM respondent $where";
    $stmt_count = $Conn->prepare($sql_count);

    if (!$stmt_count) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <small>Gagal mempersiapkan query count.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    if (!empty($bindValues)) {
        $stmt_count->bind_param($bindTypes, ...$bindValues);
    }

    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $data_count   = $result_count->fetch_assoc();
    $total_data = (int)$data_count['total'];
    $stmt_count->close();

    // =========================================================
    // TOTAL PAGE
    // =========================================================
    $total_page = ($total_data > 0) ? ceil($total_data / $batas) : 1;

    if ($page > $total_page) {
        $page = $total_page;
    }

    $posisi = ($page - 1) * $batas;

    // =========================================================
    // QUERY DATA
    // =========================================================
    $sql="SELECT r.*,sl.id_survey_log,sl.invitation_token,sl.datetime_invitation,sl.method_invitation,sl.no_wa,sl.email
FROM respondent r
LEFT JOIN survey_log sl ON sl.id_respondent=r.id_respondent
$where ORDER BY $OrderBy $ShortBy LIMIT ?, ?";
    $stmt = $Conn->prepare($sql);
    if (!$stmt) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <small>Gagal mempersiapkan query data.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    // =========================================================
    // BIND PARAMETER
    // =========================================================
    $bindTypesData    = $bindTypes . "ii";
    $bindValuesData   = $bindValues;
    $bindValuesData[] = $posisi;
    $bindValuesData[] = $batas;
    $stmt->bind_param($bindTypesData, ...$bindValuesData);

    // =========================================================
    // EXECUTE
    // =========================================================
    if (!$stmt->execute()) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <small>Terjadi kesalahan saat mengambil data.</small>
                    </td>
                </tr>
            '
        ]);

        exit;
    }

    $query = $stmt->get_result();

    // =========================================================
    // BUILD HTML
    // =========================================================
    $html = '';
    $no   = 1 + $posisi;

    if ($query->num_rows == 0) {

        $html .= '
            <tr>
                <td colspan="9" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            $id_respondent        = (int)$data['id_respondent'];
            $id_pasien            = htmlspecialchars((string)$data['id_pasien']);
            $respondent_name      = htmlspecialchars($data['respondent_name']);

            // Buka Data Undangan
            $id_survey_log       = isset($data['id_survey_log']) ? htmlspecialchars((string)$data['id_survey_log']) : "";
            if (!empty($data['invitation_token'])) {
                $token = htmlspecialchars($data['invitation_token']);
                $invitation_token = substr($token, 0, 3) . str_repeat('*', max(0, strlen($token) - 3));
            } else {
                $invitation_token = "-";
            }
            $datetime_invitation = "-";
            $method_invitation   = !empty($data['method_invitation']) ? htmlspecialchars($data['method_invitation']) : "-";
            $no_wa               = !empty($data['no_wa']) ? htmlspecialchars($data['no_wa']) : "-";
            $email               = !empty($data['email']) ? htmlspecialchars($data['email']) : "-";

            if (!empty($data['datetime_invitation'])) {
                $datetime_invitation = date('d/m/Y H:i', strtotime($data['datetime_invitation']));
            }

            // Routing Warna Font
            if(empty($id_survey_log)){
                $font_color = "text-grayish";
            }else{
                $font_color = "text-dark";
            }

            // Routing Opsi Berdasarkan Keberadaan Data
            if(empty($id_survey_log)){
                $button_option = '
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                            Lihat Detail
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalKirimUndangan" data-id="'.$id_respondent.'">
                            Kirim Undangan
                        </a>
                    </li>
                ';
            }else{
                $button_option = '
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                            Lihat Detail
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_respondent.'">
                            Hapus Undangan
                        </a>
                    </li>
                ';
            }
           
            $html .= '
                <tr>
                    <td class="text-center">
                        <input class="form-check-input item_undangan" type="checkbox" name="id_respondent[]" value="'.$id_respondent.'">
                    </td>
                    <td>
                        <small>
                            <a href="javascript:void(0);" class="text text-primary" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                                '.$id_pasien.'
                            </a>
                        </small>
                    </td>
                    <td>
                        <small class="text '.$font_color.'">
                            '.$respondent_name.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text '.$font_color.'">
                            '.$datetime_invitation.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text '.$font_color.'">
                            '.$method_invitation.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text '.$font_color.'">
                            '.$no_wa.'
                        </small>
                    </td>
                    <td>
                        <small class="text '.$font_color.'">
                            '.$email.'
                        </small>
                    </td>
                    <td class="text-center">
                       <small class="text '.$font_color.'">
                            '.$invitation_token.'
                        </small>
                    </td>
                    <td class="text-center">

                        <button 
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            '.$button_option.'
                        </ul>

                    </td>

                </tr>
            ';
        }
    }

    $stmt->close();

    // =========================================================
    // RESPONSE
    // =========================================================
    echo json_encode([
        "status"      => "success",
        "html"        => $html,
        "page"        => $page,
        "total_page"  => $total_page,
        "total_data"  => $total_data
    ]);
?>
