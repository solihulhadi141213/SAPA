<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    $response = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
        echo json_encode($response);
        exit;
    }

    $id_google_credential = isset($_POST['id_google_credential'])
        ? validateAndSanitizeInput($_POST['id_google_credential'])
        : '';

    if ($id_google_credential === '') {
        $response['message'] = 'ID Google Credential tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    $id_google_credential = (int) $id_google_credential;

    $check = $Conn->prepare("SELECT id_google_credential FROM google_credential WHERE id_google_credential = ? LIMIT 1");

    if (!$check) {
        $response['message'] = 'Gagal mempersiapkan query database!';
        echo json_encode($response);
        exit;
    }

    $check->bind_param("i", $id_google_credential);

    if (!$check->execute()) {
        $response['message'] = 'Gagal membuka data!';
        echo json_encode($response);
        $check->close();
        exit;
    }

    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $response['message'] = 'Data tidak ditemukan!';
        echo json_encode($response);
        $check->close();
        exit;
    }

    $check->close();

    $Conn->begin_transaction();

    try {
        $stmt = $Conn->prepare("DELETE FROM google_credential WHERE id_google_credential = ?");

        if (!$stmt) {
            throw new Exception('Gagal menyiapkan query untuk menghapus data!');
        }

        $stmt->bind_param("i", $id_google_credential);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menghapus data Google Credential!');
        }

        $stmt->close();
        $Conn->commit();

        $response = [
            'status' => 'success',
            'message' => 'Google Credential berhasil dihapus.'
        ];
    } catch (Exception $e) {
        $Conn->rollback();

        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($response);
?>
