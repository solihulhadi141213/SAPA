<?php
    // Koneksi dan Function
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // Validasi Session Akses
    if (empty($SessionIdAkses)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sesi Akses Sudah Berakhir, Silahkan Login Ulang!'
        ]);
        exit;
    }

    // ==========================
    // AMBIL & SANITASI INPUT
    // ==========================

    $url_simrs   = isset($_POST['url_simrs']) 
        ? trim(htmlspecialchars($_POST['url_simrs'])) 
        : '';

    $client_id              = isset($_POST['client_id']) 
        ? trim(htmlspecialchars($_POST['client_id'])) 
        : '';

    $client_key             = isset($_POST['client_key']) 
        ? trim(htmlspecialchars($_POST['client_key'])) 
        : '';

    $status = isset($_POST['status']) 
        ? (int) $_POST['status'] 
        : 0;

    // ==========================
    // VALIDASI INPUT
    // ==========================
    // 2. URL SIMRS
    if ($url_simrs == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'URL SIMRS tidak boleh kosong!'
        ]);
        exit;
    }

    if (!filter_var($url_simrs, FILTER_VALIDATE_URL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format URL SIMRS tidak valid!'
        ]);
        exit;
    }

    // 3. Client ID & Client Key
    if ($client_id == '' || $client_key == '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Client ID dan Client Key tidak boleh kosong!'
        ]);
        exit;
    }

    // Mulai transaksi untuk memastikan konsistensi data
    $Conn->begin_transaction();

    try {
        // ==========================
        // NONAKTIFKAN SEMUA KONEKSI LAIN JIKA STATUS = 1
        // ==========================
        if ($status == 1) {
            $sql_deactivate = "UPDATE setting_simrs SET status = 0";
            $stmt_deactivate = $Conn->prepare($sql_deactivate);
            
            if (!$stmt_deactivate) {
                throw new Exception('Gagal menyiapkan query untuk menonaktifkan koneksi lain!');
            }
            
            if (!$stmt_deactivate->execute()) {
                throw new Exception('Gagal menonaktifkan koneksi lain!');
            }
            
            $stmt_deactivate->close();
        }
        
        // ==========================
        // SIMPAN DATA BARU KE DATABASE
        // ==========================
        $sql_insert = "
            INSERT INTO setting_simrs 
            (
                url_simrs,
                client_id,
                client_key,
                status
            ) 
            VALUES (?, ?, ?, ?)
        ";
        
        $stmt_insert = $Conn->prepare($sql_insert);
        
        if (!$stmt_insert) {
            throw new Exception('Gagal menyiapkan query untuk menyimpan data!');
        }
        
        $stmt_insert->bind_param(
            "sssi",
            $url_simrs,
            $client_id,
            $client_key,
            $status
        );
        
        if (!$stmt_insert->execute()) {
            throw new Exception('Gagal menyimpan data ke database!');
        }
        
        // Commit transaksi jika semua berhasil
        $Conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Koneksi SIMRS berhasil disimpan.'
        ]);
        
        $stmt_insert->close();
        
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $Conn->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
?>