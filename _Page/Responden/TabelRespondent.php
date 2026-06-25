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
        'id_respondent',
        'id_pasien',
        'id_kunjungan',
        'respondent_name',
        'respondent_sex',
        'respondent_brithdate',
        'tanggal_kunjungan',
        'no_kontak',
        'kunjungan_tujuan'
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_respondent';
    }

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
        'id_pasien',
        'id_kunjungan',
        'respondent_name',
        'respondent_sex',
        'respondent_brithdate',
        'tanggal_kunjungan',
        'no_kontak',
        'kunjungan_tujuan'
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
                    id_kunjungan LIKE ? OR
                    respondent_name LIKE ? OR
                    respondent_sex LIKE ? OR
                    respondent_brithdate LIKE ? OR
                    tanggal_kunjungan LIKE ? OR
                    no_kontak LIKE ? OR
                    kunjungan_tujuan LIKE ?
                )
            ";

            $bindTypes .= "ssssssss";

            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
            $bindValues[] = $keywordLike;
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
    $sql = "SELECT * FROM respondent $where ORDER BY $OrderBy $ShortBy LIMIT ?, ?";
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
            $id_pasien            = (int)$data['id_pasien'];
            $id_kunjungan         = (int)$data['id_kunjungan'];
            $no_kontak         = htmlspecialchars($data['no_kontak']);
            $respondent_name      = htmlspecialchars($data['respondent_name']);
            $respondent_sex       = htmlspecialchars($data['respondent_sex']);
            $respondent_brithdate = htmlspecialchars($data['respondent_brithdate']);
            $tanggal_kunjungan    = htmlspecialchars($data['tanggal_kunjungan']);
            $kunjungan_tujuan     = htmlspecialchars($data['kunjungan_tujuan']);

            // Format tanggal lahir
            $respondent_brithdate_format = date('d/m/Y', strtotime($respondent_brithdate));

            // Format tanggal kunjungan
            $tanggal_kunjungan_format = date('d/m/Y', strtotime($tanggal_kunjungan));

            // Menghitung Usia
            $tgl_lahir     = new DateTime($respondent_brithdate);
            $tgl_kunjungan = new DateTime($tanggal_kunjungan);
            $usia = $tgl_lahir->diff($tgl_kunjungan);
            $tahun = $usia->y;
            $bulan = $usia->m;
            $hari  = $usia->d;

            if ($tahun > 0) {
                $umur_pasien = $tahun . ' Th';
            } elseif ($bulan > 0) {
                $umur_pasien = $bulan . ' Bl';
            } else {
                $umur_pasien = $hari . ' Hr';
            }

            // Routing Rajal/Ranap
            if($kunjungan_tujuan=="Rajal"){
                $label_tujuan = '<span class="badge bg-success-subtle text-success">RJL</span>';
            }else{
                $label_tujuan = '<span class="badge bg-primary-subtle text-primary">RNP</span>';
            }

            $html .= '
                <tr>

                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>
                    <td>
                        <small>
                            <a href="javascript:void(0);" class="text text-primary" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                                '.$id_pasien.'
                            </a>
                        </small>
                    </td>
                    <td>
                        <small class="text text-grayish">
                            '.$respondent_name.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text text-grayish">
                            '.$respondent_sex.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text text-grayish">
                            '.$umur_pasien.'
                        </small>
                    </td>
                    <td class="text-left">
                        <small class="text text-grayish">
                            '.$no_kontak.'
                        </small>
                    </td>
                    <td>
                        <small class="text text-grayish">
                            '.$tanggal_kunjungan_format.'
                        </small>
                    </td>
                    <td class="text-center">
                       '.$label_tujuan.'
                    </td>
                    <td class="text-center">

                        <button 
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_respondent.'">
                                    Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_respondent.'">
                                    Edit
                                </a>
                            </li>
                           
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_respondent.'">
                                    Hapus
                                </a>
                            </li>
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