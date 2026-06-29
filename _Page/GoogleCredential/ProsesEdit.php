<?php
    // Header JSON Format
    header('Content-Type: application/json');

    // Connection, Helper & Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Set Time Zone
    date_default_timezone_set("Asia/Jakarta");

    // Default Response
    $response = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // Validation Session
    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!';
        echo json_encode($response);
        exit;
    }

    // Catch Data Form
    $id_google_credential = isset($_POST['id_google_credential']) ? validateAndSanitizeInput($_POST['id_google_credential']) : '';
    $credential_env       = isset($_POST['credential_env']) ? trim(htmlspecialchars($_POST['credential_env'])) : '';
    $client_id            = isset($_POST['client_id']) ? trim(htmlspecialchars($_POST['client_id'])) : '';
    $client_id            = trim($client_id);
    $client_secret        = isset($_POST['client_secret']) ? trim(htmlspecialchars($_POST['client_secret'])) : '';
    $client_secret        = trim($client_secret);
    $status               = isset($_POST['status']) ? (int) $_POST['status'] : 0;

    // Validation Mandatory
    if ($id_google_credential == '') {
        $response['message'] = 'ID Google Credential tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    if ($credential_env == '') {
        $response['message'] = 'Credential Environment tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    if ($client_id == '') {
        $response['message'] = 'Client ID tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    if ($client_secret == '') {
        $response['message'] = 'Client Sceret tidak boleh kosong!';
        echo json_encode($response);
        exit;
    }

    if (!in_array($status, [0, 1], true)) {
        $response['message'] = 'Status tidak valid!';
        echo json_encode($response);
        exit;
    }

    $Qry = $Conn->prepare("
        SELECT id_google_credential
        FROM google_credential
        WHERE id_google_credential = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response['message'] = 'Gagal mempersiapkan query database!';
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_google_credential);

    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response['message'] = 'Data tidak ditemukan!';
        echo json_encode($response);
        $Qry->close();
        exit;
    }

    $Qry->close();

    $Conn->begin_transaction();

    try {
        if ($status === 1) {
            $stmt_deactivate = $Conn->prepare("UPDATE google_credential SET status = 0 WHERE id_google_credential <> ?");

            if (!$stmt_deactivate) {
                throw new Exception('Gagal menyiapkan query untuk menonaktifkan data lain!');
            }

            $stmt_deactivate->bind_param("i", $id_google_credential);

            if (!$stmt_deactivate->execute()) {
                throw new Exception('Gagal menonaktifkan data lain!');
            }

            $stmt_deactivate->close();
        }

        $stmt_update = $Conn->prepare("
            UPDATE google_credential
            SET
                credential_env = ?,
                client_id = ?,
                client_secret = ?,
                status = ?
            WHERE id_google_credential = ?
        ");

        if (!$stmt_update) {
            throw new Exception('Gagal menyiapkan query untuk update data!');
        }

        $stmt_update->bind_param(
            "sssii",
            $credential_env,
            $client_id,
            $client_secret,
            $status,
            $id_google_credential
        );

        if (!$stmt_update->execute()) {
            throw new Exception('Gagal memperbarui data Google Credential!');
        }

        $stmt_update->close();
        $Conn->commit();

        $response = [
            'status' => 'success',
            'message' => 'Google Credential berhasil diperbarui.'
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
