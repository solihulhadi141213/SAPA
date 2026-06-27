<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";
    include "../../_Config/Setting.php";

    date_default_timezone_set("Asia/Jakarta");

    function ResponseJson($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    function NormalisasiNoKontak($no_kontak){
        $no_kontak = preg_replace('/\D+/', '', (string) $no_kontak);

        if ($no_kontak === '') {
            return '';
        }

        if (strpos($no_kontak, '62') === 0) {
            return $no_kontak;
        }

        if (strpos($no_kontak, '0') === 0) {
            return '62' . substr($no_kontak, 1);
        }

        if (strpos($no_kontak, '8') === 0) {
            return '62' . $no_kontak;
        }

        return '62' . $no_kontak;
    }

    if (empty($SessionIdAkses)) {
        ResponseJson("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    $id_respondent     = trim($_POST['id_respondent'] ?? '');
    $respondent_name   = trim($_POST['respondent_name'] ?? '');
    $invitation_token  = trim($_POST['invitation_token'] ?? '');
    $no_kontak         = trim($_POST['no_kontak'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $method_invitation = trim($_POST['method_invitation'] ?? '');
    $isi_pesan         = trim($_POST['isi_pesan'] ?? '');
    $datetime_invitation = date('Y-m-d H:i:s');

    if ($id_respondent === '') {
        ResponseJson("error", "ID Responden tidak boleh kosong.");
    }

    if (!ctype_digit($id_respondent)) {
        ResponseJson("error", "ID Responden tidak valid.");
    }

    if ($respondent_name === '') {
        ResponseJson("error", "Nama Responden tidak boleh kosong.");
    }

    if ($invitation_token === '') {
        ResponseJson("error", "Token Undangan tidak boleh kosong.");
    }

    if ($method_invitation === '') {
        ResponseJson("error", "Metode Undangan tidak boleh kosong.");
    }

    if (!in_array($method_invitation, ['Whatsapp', 'Email', 'Manual'], true)) {
        ResponseJson("error", "Metode Undangan tidak valid.");
    }

    if ($method_invitation === "Whatsapp" && $no_kontak === '') {
        ResponseJson("error", "Nomor Kontak harus diisi sebelum mengirimkan melalui Whatsapp.");
    }

    if ($method_invitation === "Email" && $email === '') {
        ResponseJson("error", "Alamat Email harus diisi sebelum mengirimkan melalui Email.");
    }

    if ($method_invitation === "Whatsapp") {
        $no_kontak = NormalisasiNoKontak($no_kontak);

        if ($no_kontak === '' || strpos($no_kontak, '62') !== 0) {
            ResponseJson("error", "Nomor Kontak tidak valid. Gunakan format nomor Indonesia.");
        }
    }

    $id_respondent = (int) $id_respondent;

    $stmt_duplicate = $Conn->prepare("
        SELECT id_survey_log
        FROM survey_log
        WHERE id_respondent = ?
        LIMIT 1
    ");

    if (!$stmt_duplicate) {
        ResponseJson("error", "Gagal mempersiapkan validasi duplikasi.");
    }

    $stmt_duplicate->bind_param("i", $id_respondent);
    if (!$stmt_duplicate->execute()) {
        $stmt_duplicate->close();
        ResponseJson("error", "Gagal memeriksa data undangan.");
    }

    $result_duplicate = $stmt_duplicate->get_result();
    if ($result_duplicate->num_rows > 0) {
        $stmt_duplicate->close();
        ResponseJson("error", "Data responden sudah memiliki data undangan survey.");
    }
    $stmt_duplicate->close();

    if ($method_invitation === "Whatsapp") {
        $status_setting_wa = 1;
        $id_setting_wa = GetDetailData($Conn, 'setting_wa', 'status', $status_setting_wa, 'id_setting_wa');

        if (empty($id_setting_wa)) {
            ResponseJson("error", "Koneksi dengan API WhatsApp gateway belum diatur.");
        }

        $Qry = $Conn->prepare("SELECT * FROM setting_wa WHERE id_setting_wa = ? LIMIT 1");
        if (!$Qry) {
            ResponseJson("error", "Gagal mempersiapkan query setting WA.");
        }

        $Qry->bind_param("i", $id_setting_wa);
        if (!$Qry->execute()) {
            $Qry->close();
            ResponseJson("error", "Gagal membuka data setting WA.");
        }

        $Result = $Qry->get_result();
        if ($Result->num_rows === 0) {
            $Qry->close();
            ResponseJson("error", "Data setting WA tidak ditemukan.");
        }

        $Data = $Result->fetch_assoc();
        $url_service = trim((string)($Data['url_service'] ?? ''));
        $api_key = trim((string)($Data['api_key'] ?? ''));
        $Qry->close();

        if ($url_service === '' || $api_key === '') {
            ResponseJson("error", "Konfigurasi WA gateway belum lengkap.");
        }

        $message = str_replace(
            ['{{respondent_name}}', '{{link_survey}}'],
            [
                $respondent_name,
                $base_url . '/Questionnaire.php?token=' . urlencode($invitation_token)
            ],
            $isi_pesan
        );

        $payload = json_encode([
            'to'      => $no_kontak,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            ResponseJson("error", "Gagal menyiapkan payload WhatsApp.");
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => rtrim($url_service, '/') . '/api/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . $api_key,
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        if ($response === false) {
            $curl_error = curl_error($curl);
            curl_close($curl);
            ResponseJson("error", "Gagal mengirim WhatsApp: " . $curl_error);
        }

        curl_close($curl);

        $arry_response = json_decode($response, true);
        if (!is_array($arry_response)) {
            ResponseJson("error", "Respon WhatsApp gateway tidak valid.");
        }

        $status = (bool)($arry_response['success'] ?? false);
        $message_gateway = (string)($arry_response['message'] ?? 'Gagal mengirim WhatsApp.');

        if ($status !== true) {
            ResponseJson("error", $message_gateway);
        }
    }

    if ($method_invitation === "Email") {
        
    // Kirim Undangan Melalui Email
        $status_email_gateway = 1;
        $Qry = $Conn->prepare("SELECT * FROM setting_email_gateway WHERE status = ? LIMIT 1");
        if (!$Qry) {
            ResponseJson("error", 'Terjadi kesalahan pada saat mempersiapkan query database!  Keterangan : ' . htmlspecialchars($Conn->error) . '');
        }
        $Qry->bind_param("i", $status_email_gateway);
        if (!$Qry->execute()) {
            $Qry->close();
            ResponseJson("error", 'Terjadi kesalahan pada saat mempersiapkan query database!  Keterangan : ' . htmlspecialchars($Conn->error) . '');
        }
        $Result = $Qry->get_result();
        if ($Result->num_rows == 0) {
            $Qry->close();
            ResponseJson("error", 'Pengaturan Koneksi Email Gateway Belum Dibuat!');
        }

        // Atur Pesan
        $message = str_replace(
            ['{{respondent_name}}', '{{link_survey}}'],
            [
                $respondent_name,
                $base_url . '/Questionnaire.php?token=' . urlencode($invitation_token)
            ],
            $isi_pesan
        );

        // Parameter Pengaturan Email Gateway
        $Data = $Result->fetch_assoc();
        $Qry->close();
        $id_setting_email_gateway = htmlspecialchars($Data['id_setting_email_gateway']);
        $email_gateway            = htmlspecialchars($Data['email_gateway']);
        $password_gateway         = htmlspecialchars($Data['password_gateway']);
        $url_provider             = htmlspecialchars($Data['url_provider']);
        $port_gateway             = htmlspecialchars($Data['port_gateway']);
        $nama_pengirim            = htmlspecialchars($Data['nama_pengirim']);
        $url_service              = htmlspecialchars($Data['url_service']);

        // Susun Payload
        $payload = [
            "subjek"               => 'Survey Kepuasan Pasien',
            "email_asal"           => $email_gateway,
            "password_email_asal"  => $password_gateway,
            "url_provider"         => $url_provider,
            "nama_pengirim"        => $nama_pengirim,
            "email_tujuan"         => $email,
            "nama_tujuan"          => $respondent_name,
            "pesan"                => $message,
            "port"                 => $port_gateway
        ];

        // CURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url_service,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Error CURL
        if(!empty($error)){
            ResponseJson("error", 'Terjadi kesalahan pada saat mengirim email. Keterangan : '.$error.'');
        }
    }

    $stmt_insert = $Conn->prepare("
        INSERT INTO survey_log (
            id_respondent,
            invitation_token,
            datetime_invitation,
            method_invitation,
            no_wa,
            email
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )
    ");

    if (!$stmt_insert) {
        ResponseJson("error", "Gagal mempersiapkan query penyimpanan.");
    }

    $stmt_insert->bind_param(
        "isssss",
        $id_respondent,
        $invitation_token,
        $datetime_invitation,
        $method_invitation,
        $no_kontak,
        $email
    );

    if (!$stmt_insert->execute()) {
        $stmt_insert->close();
        ResponseJson("error", "Gagal menyimpan data undangan.");
    }

    $stmt_insert->close();

    ResponseJson("success", "Data undangan berhasil dikirim.");
?>
