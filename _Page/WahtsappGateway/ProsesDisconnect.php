<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set('Asia/Jakarta');

    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
        echo json_encode($response);
        exit;
    }

    if (empty($_POST['id_setting_wa'])) {
        $response['message'] = 'ID Whatsapp Gateway tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    $id_setting_wa = validateAndSanitizeInput($_POST['id_setting_wa']);

    $Qry = $Conn->prepare("
        SELECT * FROM setting_wa
        WHERE id_setting_wa = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response['message'] = 'Gagal mempersiapkan query database!';
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_setting_wa);

    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data Whatsapp Gateway!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response['message'] = 'Data Whatsapp Gateway tidak ditemukan!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Data          = $Result->fetch_assoc();
    $id_setting_wa = htmlspecialchars($Data['id_setting_wa']);
    $url_service   = htmlspecialchars($Data['url_service']);
    $api_key       = htmlspecialchars($Data['api_key']);
    $status        = (int) $Data['status'];
    $Qry->close();

    // Persiapan CURL
    $url = "$url_service/api/disconnection";

    // Mulai CURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'x-api-key: ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    // DECODE RESPONSE
    $data = json_decode($response, true);

    // Jika Gagal
    if ($data['success']!==true) {
        $response['message'] = $data['message'];
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $response = [
        'status'  => 'success',
        'message' => 'Perangkat Berhasil Dihapus.'
    ];
    echo json_encode($response);
    
?>
