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
                    <td colspan="8" class="text-center text-danger">
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
    $OrderBy    = $_POST['OrderBy'] ?? 'id_setting_general';
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
        'id_setting_general',
        'app_name',
        'company_name',
        'environment_status',
        'configuration_status',
    ];

    if (!in_array($OrderBy, $allowedOrder)) {
        $OrderBy = 'id_setting_general';
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
        'id_setting_general',
        'app_name',
        'company_name',
        'environment_status',
        'configuration_status',
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

            $where .= " WHERE $keyword_by LIKE ? ";

            $bindTypes .= "s";
            $bindValues[] = $keywordLike;

        } else {

            $where .= "
                WHERE (
                    app_name LIKE ? OR 
                    company_name LIKE ? OR
                    environment_status LIKE ? OR
                    configuration_status LIKE ?
                )
            ";

            $bindTypes .= "ssss";

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
    $sql_count = "SELECT COUNT(*) AS total FROM setting_general $where";

    $stmt_count = $Conn->prepare($sql_count);

    if (!$stmt_count) {

        echo json_encode([
            "status" => "error",
            "html"   => '
                <tr>
                    <td colspan="8" class="text-center text-danger">
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
        FROM setting_general
        
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
                    <td colspan="8" class="text-center text-danger">
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
                    <td colspan="8" class="text-center text-danger">
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
                <td colspan="8" class="text-center text-danger">
                    <small>Tidak ada data yang ditampilkan.</small>
                </td>
            </tr>
        ';

    } else {

        while ($data = $query->fetch_assoc()) {

            $id_setting_general   = (int)$data['id_setting_general'];
            $app_name             = htmlspecialchars($data['app_name']);
            $company_name         = htmlspecialchars($data['company_name']);
            $environment_status   = htmlspecialchars($data['environment_status']);
            $configuration_status = htmlspecialchars($data['configuration_status']);
            $app_icon             = htmlspecialchars($data['app_icon']);
            $base_url             = htmlspecialchars($data['base_url']);

            // Image
            $app_icon_path = "assets/img/logo/" . $app_icon;

            // Routing $environment_status
            if($environment_status=="Development"){
                $label_env_status = '<label class="badge bg-danger-subtle text-danger">Development</label>';
            }else{
                if($environment_status=="Staging"){
                    $label_env_status = '<label class="badge bg-warning-subtle text-warning">Staging</label>';
                }else{
                    if($environment_status=="Production"){
                        $label_env_status = '<label class="badge bg-success-subtle text-success">Production</label>';
                    }
                }
            }

            // Routing configuration_status
            if($configuration_status==1){
                $label_configuration_status = '<label class="badge bg-success text-light">Active</label>';
            }else{
                $label_configuration_status = '<label class="badge bg-danger text-light">Inactive</label>';
            }

            $html .= '
                <tr>

                    <td class="text-center">
                        <small class="text text-grayish">'.$no++.'</small>
                    </td>

                    <td class="text-center">
                        <img src="'.$app_icon_path.'" class="rounded-circle profile-img">
                    </td>

                    <td>
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_setting_general.'">
                            <small class="text text-primary">'.$app_name.'</small>
                        </a>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            '.$company_name.'
                        </small>
                    </td>

                    <td>
                        <small class="text text-grayish">
                            <a href="'.$base_url.'" class="text text-info text-decoration-underline">
                                '.$base_url.'
                            </a>
                        </small>
                    </td>

                    <td class="text-center">
                        '.$label_env_status.'
                    </td>

                    <td class="text-center">
                       '.$label_configuration_status.'
                    </td>

                    <td class="text-center">

                        <button 
                            class="btn btn-md btn-outline-secondary btn-floating"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots-vertical"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_setting_general.'">
                                    Detail
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_setting_general.'">
                                    Edit Pengaturan
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalEditAppIcon" data-id="'.$id_setting_general.'">
                                    Edit App Icon
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalEditCompanyLogo" data-id="'.$id_setting_general.'">
                                    Edit Company Logo
                                </a>
                            </li>
                           
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_setting_general.'">
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