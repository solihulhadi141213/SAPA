<?php
    header('Content-Type: application/json');

    // Connection, Function dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // =====================================================
    // RESPONSE DEFAULT
    // =====================================================
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // =====================================================
    // VALIDASI SESSION
    // =====================================================
    if (empty($SessionIdAkses)) {
        $response['message'] = 'Sesi akses sudah berakhir. Silahkan login ulang.';
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // VALIDASI ID AKSES
    // =====================================================
    if (empty($_POST['id_akses'])) {
        $response['message'] = 'ID akses tidak boleh kosong.';
        echo json_encode($response);
        exit;
    }

    $id_akses = validateAndSanitizeInput($_POST['id_akses']);

    // =====================================================
    // VALIDASI TIDAK BOLEH HAPUS DIRI SENDIRI
    // =====================================================
    if ($id_akses == $SessionIdAkses) {
        $response['message'] = 'Anda tidak dapat menghapus akun yang sedang digunakan untuk login.';
        echo json_encode($response);
        exit;
    }

    // =====================================================
    // CEK DATA AKSES
    // =====================================================
    $Qry = $Conn->prepare("
        SELECT 
            id_akses,
            image_akses,
            nama_akses
        FROM akses
        WHERE id_akses = ?
        LIMIT 1
    ");

    if (!$Qry) {
        $response['message'] = 'Gagal mempersiapkan query database.';
        echo json_encode($response);
        exit;
    }

    $Qry->bind_param("i", $id_akses);

    if (!$Qry->execute()) {
        $response['message'] = 'Gagal membuka data akses.';
        echo json_encode($response);
        exit;
    }

    $Result = $Qry->get_result();

    if ($Result->num_rows == 0) {
        $response['message'] = 'Data akses tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    $Data = $Result->fetch_assoc();

    $image_akses = $Data['image_akses'];
    $nama_akses  = $Data['nama_akses'];

    $Qry->close();

    // =====================================================
    // TRANSACTION
    // =====================================================
    mysqli_begin_transaction($Conn);

    try {

        // =================================================
        // HAPUS DATA AKSES
        // =================================================
        $Delete = $Conn->prepare("
            DELETE FROM akses
            WHERE id_akses = ?
        ");

        if (!$Delete) {
            throw new Exception("Gagal mempersiapkan query hapus.");
        }

        $Delete->bind_param("i", $id_akses);

        if (!$Delete->execute()) {
            throw new Exception("Gagal menghapus data akses.");
        }

        $Delete->close();

        // =================================================
        // HAPUS FILE FOTO (SETELAH DELETE BERHASIL)
        // =================================================
        if (!empty($image_akses)) {

            $file_path = "../../_Assets/img/user/" . $image_akses;

            if (file_exists($file_path) && is_file($file_path)) {
                @unlink($file_path);
            }
        }

        // =================================================
        // COMMIT
        // =================================================
        mysqli_commit($Conn);

        $response = [
            'status'  => 'success',
            'message' => 'Data akses "' . $nama_akses . '" berhasil dihapus.'
        ];

    } catch (Exception $e) {

        mysqli_rollback($Conn);

        $response = [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($response);
?>