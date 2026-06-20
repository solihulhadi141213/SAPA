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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_akses';
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
        'id_akses',
        'nama_akses',
        'kontak_akses',
        'email_akses',
        'akses',
        'datetime_update',
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_akses';
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
        'id_akses',
        'nama_akses',
        'kontak_akses',
        'email_akses',
        'akses',
        'datetime_update',
    ];

    if (!empty($keyword_by) && !in_array($keyword_by, $allowedKeywordBy)) {
        $keyword_by = '';
    }

    // =========================================================
    // FILTER QUERY
    // =========================================================
    $where = "";
    $bindTypes = "";
    $bindValues = [];

    if (!empty($keyword)) {

        $keywordLike = "%" . $keyword . "%";

        if (!empty($keyword_by)) {

            $where .= " WHERE akses.$keyword_by LIKE ? ";

            $bindTypes .= "s";
            $bindValues[] = $keywordLike;

        } else {

            $where .= "
                WHERE (
                    nama_akses LIKE ? OR 
                    kontak_akses LIKE ? OR
                    email_akses LIKE ? OR
                    akses LIKE ? OR
                    datetime_update LIKE ?
                )
            ";

            $bindTypes .= "sssss";

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
    $sql_count = "SELECT COUNT(*) AS total FROM akses $where";

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
    $sql = "
        SELECT *
        FROM akses
        
        $where
        ORDER BY $OrderBy $ShortBy
        LIMIT ?, ?
    ";

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
    $bindTypesData = $bindTypes . "ii";

    $bindValuesData = $bindValues;

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

        while ($data = $query->fetch_assoc()) {

            $id_akses           = (int)$data['id_akses'];
            $nama_akses         = htmlspecialchars($data['nama_akses']);
            $kontak_akses       = htmlspecialchars($data['kontak_akses']);
            $image_akses        = htmlspecialchars($data['image_akses']);
            $email              = htmlspecialchars($data['email_akses']);
            $akses              = htmlspecialchars($data['akses']);

            // Image
            if (empty($image_akses)) {
                $image_path = "assets/img/user/No-Image.png";
            } else {
                $image_path = "assets/img/user/" . $image_akses;
            }

            // Button fitur
            if (empty($jumlah_fitur)) {

                $button_fitur = '
                    <button 
                        type="button" 
                        class="btn btn-sm btn-outline-secondary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#ModalFitur" 
                        data-id="'.$id_akses.'">

                        0 Fitur
                    </button>
                ';

            } else {

                $button_fitur = '
                    <button 
                        type="button" 
                        class="btn btn-sm btn-secondary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#ModalFitur" 
                        data-id="'.$id_akses.'">

                        '.$jumlah_fitur.' Fitur
                    </button>
                ';
            }

            $html .= '
                <tr>

                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td>
                        <div class="table-user">

                            <img 
                                src="'.$image_path.'" 
                                class="rounded-circle profile-img">

                            <a 
                                href="javascript:void(0);" 
                                class="p p-3"
                                data-bs-toggle="modal" 
                                data-bs-target="#ModalDetail" 
                                data-id="'.$id_akses.'">

                                <small class="text text-primary">
                                    '.$nama_akses.'
                                </small>

                            </a>

                        </div>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$email.'
                        </small>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$kontak_akses.'
                        </small>
                    </td>

                    <td class="text-center">
                        <small class="text text-grayish">
                            '.$akses.'
                        </small>
                    </td>

                    <td class="text-center">
                        '.$button_fitur.'
                    </td>

                    <td class="text-center">

                        <button 
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalDetail"
                                    data-id="'.$id_akses.'">

                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </li>

                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEdit"
                                    data-id="'.$id_akses.'">

                                    <i class="bi bi-pencil"></i> Edit Profil
                                </a>
                            </li>

                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEditFoto"
                                    data-id="'.$id_akses.'">

                                    <i class="bi bi-image"></i> Edit Foto
                                </a>
                            </li>

                            <li>
                                <a 
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalEditPassword"
                                    data-id="'.$id_akses.'">

                                    <i class="bi bi-key"></i> Edit Password
                                </a>
                            </li>
                           
                            <li>
                                <a 
                                    class="dropdown-item text-danger"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalHapus"
                                    data-id="'.$id_akses.'">

                                    <i class="bi bi-trash"></i> Hapus
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