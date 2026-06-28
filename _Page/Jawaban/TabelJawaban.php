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
                    <td colspan="7" class="text-center text-danger">
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
        'datetime_answer'=>'sl.datetime_answer',
        'answer'=>'sl.answer'
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
        'id_respondent'      => 'r.id_respondent',
        'id_pasien'          => 'r.id_pasien',
        'respondent_name'    => 'r.respondent_name',
        'datetime_invitation'=> 'sl.datetime_invitation',
        'datetime_answer'    => 'sl.datetime_answer',
        'answer'             => 'sl.answer'
    ];

    if (!empty($keyword_by) && !array_key_exists($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where      = "";
    $bindTypes  = "";
    $bindValues = [];

    if ($keyword !== '') {
        if (!empty($keyword_by)) {
            $keywordColumn = $allowedKeywordBy[$keyword_by];

            if ($keyword_by === 'answer') {
                $where .= " WHERE $keywordColumn = ? ";
                $bindTypes .= "i";
                $bindValues[] = (int) $keyword;
            } else {
                $keywordLike = "%" . $keyword . "%";
                $where .= " WHERE $keywordColumn LIKE ? ";
                $bindTypes .= "s";
                $bindValues[] = $keywordLike;
            }

        } else {

            $keywordLike = "%" . $keyword . "%";
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
    $sql_count = "SELECT COUNT(*) AS total FROM respondent r LEFT JOIN survey_log sl ON sl.id_respondent=r.id_respondent $where";
    $stmt_count = $Conn->prepare($sql_count);

    if (!$stmt_count) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
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
    $sql="SELECT r.*,sl.id_survey_log,sl.datetime_invitation,sl.datetime_answer,sl.answer FROM respondent r LEFT JOIN survey_log sl ON sl.id_respondent=r.id_respondent $where ORDER BY $OrderBy $ShortBy LIMIT ?, ?";
    $stmt = $Conn->prepare($sql);
    if (!$stmt) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="7" class="text-center text-danger">
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
                    <td colspan="7" class="text-center text-danger">
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
                <td colspan="7" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {
        $no = 1;
        while ($data = $query->fetch_assoc()) {

            $id_respondent        = (int)$data['id_respondent'];
            $id_pasien            = htmlspecialchars((string)$data['id_pasien']);
            $respondent_name      = htmlspecialchars($data['respondent_name']);

            // Buka Data Undangan
            $id_survey_log       = isset($data['id_survey_log']) ? htmlspecialchars((string)$data['id_survey_log']) : "";
            $datetime_invitation = "-";
            $datetime_answer = "-";

            if (!empty($data['datetime_invitation'])) {
                $datetime_invitation = date('d/m/Y H:i', strtotime($data['datetime_invitation']));
            }

            if (!empty($data['datetime_answer'])) {
                if(empty($data['answer'])){
                    $datetime_answer = "-";
                }else{
                    $datetime_answer = date('d/m/Y H:i', strtotime($data['datetime_answer']));
                }
            }

            // Routing Warna Font
            if(empty($id_survey_log)){
                $font_color = "text-grayish";
            }else{
                $font_color = "text-dark";
            }

            // Routing Status
            if(empty($data['answer'])){
                $label_status = '
                    <span class="badge bg-danger text-light">Belum</span>
                ';
                $tombol_jawaban = '
                    <button type="button" disabled class="btn btn-sm btn-outline-info btn-floating">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                ';
            }else{
                $label_status = '
                    <span class="badge bg-success text-light">Selesai</span>
                ';
                $tombol_jawaban = '
                    <button type="button" class="btn btn-sm btn-info btn-floating" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                ';
            }
           
            $html .= '
                <tr>
                    <td class="text-center">
                        <small class="text '.$font_color.'">
                            '.$no.'
                        </small>
                    </td>
                    <td>
                        <small class="text '.$font_color.'">
                            '.$id_pasien.'
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
                            '.$datetime_answer.'
                        </small>
                    </td>
                    <td class="text-center">
                       '.$label_status.'
                    </td>
                    <td class="text-center">
                       '.$tombol_jawaban.'
                    </td>

                </tr>
            ';
            $no++;
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
