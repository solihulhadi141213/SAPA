<?php
    
    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Validasi Session
    if (empty($SessionIdAkses)) {
        echo '
           <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang</small>
                </td>
           </tr>
           <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Validasi id_survey_question 
    if(empty($_POST['id_survey_question'])){
        echo '
           <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">ID Pertanyaan Tidak Boleh Kosong</small>
                </td>
           </tr>
           <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Validasi data_value 
    if(empty($_POST['data_value'])){
        echo '
           <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">Value Jawaban Tidak Ditemukan</small>
                </td>
           </tr>
           <script>
                $("#page_info").html("0 / 0");
                $("#prev_button").prop("disabled", true);
                $("#next_button").prop("disabled", true);
            </script>
        ';
        exit;
    }

    // Variabel And Sanitazer
    $id_survey_question = validateAndSanitizeInput($_POST['id_survey_question']);
    $value              = validateAndSanitizeInput($_POST['data_value']);

    // Parameter Halaman
    $keyword    = $_POST['keyword'] ?? "";
    $order_by   = "id_survey_answer";
    $page       = $_POST['page'] ?? 1;
    $limit      = 10;
    $short_by   = "DESC";

    // VALIDASI PAGE & LIMIT
    $page  = (int)$page;
    $limit = (int)$limit;

    if ($page <= 0) {
        $page = 1;
    }

    if ($limit <= 0) {
        $limit = 10;
    }

    $posisi = ($page - 1) * $limit;

    // Open Data With Prepared Statmnet
    $where = "WHERE sa.id_survey_question=? AND sa.answer_text=?";
    $param_type="ss";
    $param=[];
    $param[]=&$id_survey_question;
    $param[]=&$value;
    if(!empty($keyword)){
        $where.=" AND (r.id_pasien LIKE ? OR r.respondent_name LIKE ? )";
        $search="%".$keyword."%";
        $param_type.="ss";
        $param[]=&$search;
        $param[]=&$search;
    }
    $sql="
        SELECT
            sa.*,
            r.id_pasien,
            r.respondent_name,
            r.respondent_sex,
            r.no_kontak,
            sl.email,
            sl.no_wa,
            sl.datetime_answer

        FROM survey_answer sa

        INNER JOIN respondent r
        ON sa.id_respondent=r.id_respondent

        LEFT JOIN survey_log sl
        ON sa.id_respondent=sl.id_respondent

        $where

        ORDER BY sa.id_survey_answer DESC

        LIMIT ?, ?

    ";

    $param_type.="ii";
    $param[]=&$posisi;
    $param[]=&$limit;
    $stmt=$Conn->prepare($sql);
    $stmt->bind_param(
        $param_type,
        ...$param
    );

$stmt->execute();

$Result=$stmt->get_result();

    $sql_count = "SELECT COUNT(*) AS total FROM survey_answer WHERE id_survey_question = '$id_survey_question' AND answer_text = '$value'";
    $stmt_count = $Conn->prepare($sql_count);
    if (!$stmt_count) {
        echo '
            <tr>
                <td colspan="8" class="text-center">
                    <small class="text-danger">
                        Gagal mempersiapkan query count.
                    </small>
                </td>
            </tr>
        ';
        exit;
    }
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $data_count   = $result_count->fetch_assoc();
    $total_data = (int)$data_count['total'];
    $stmt_count->close();

    // TOTAL PAGE
    $total_page = ($total_data > 0) ? ceil($total_data / $limit) : 1;

    // SHOW DATA
    $no=$posisi+1;
    while($Data = $Result->fetch_assoc()){
        $id_pasien       = $Data['id_pasien'];
        $respondent_name = $Data['respondent_name'];
        $respondent_sex  = $Data['respondent_sex'];
        $email           = $Data['email'];
        $no_wa           = $Data['no_wa'];
        $datetime_answer = $Data['datetime_answer'];
        $answer_text     = $Data['answer_text'];

        
        echo '
            <tr>
                <td class="text-center"><small>'.$no.'</small></td>
                <td class="text-left"><small>'.$id_pasien.'</small></td>
                <td class="text-left"><small>'.$respondent_name.'</small></td>
                <td class="text-left"><small>'.$respondent_sex.'</small></td>
                <td class="text-left"><small>'.$email.'</small></td>
                <td class="text-left"><small>'.$no_wa.'</small></td>
                <td class="text-left"><small>'.$datetime_answer.'</small></td>
                <td class="text-left"><small>'.$answer_text.'</small></td>
            </tr>
        ';
        $no++;
    }

    echo "
        <script>
            $('#page_info').html('$page / $total_page');
            $('#prev_button').prop('disabled',".($page<=1?'true':'false').");
            $('#next_button').prop('disabled',".($page>=$total_page?'true':'false').");
        </script>
    ";
    
?>
 