<?php
    header('Content-Type: application/json');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set('Asia/Jakarta');

    $response = array(
        'status' => 'error',
        'message' => 'Terjadi kesalahan'
    );

    // Validasi session
    if(empty($SessionIdAkses)){

        $response['message'] = 'Sesi login sudah berakhir';

        echo json_encode($response);
        exit;
    }

    // Tangkap input
    $nama_akses   = trim($_POST['nama_akses'] ?? '');
    $kontak_akses = trim($_POST['kontak_akses'] ?? '');
    $email        = trim($_POST['email_akses_profil'] ?? '');

    // Validasi nama
    if(empty($nama_akses)){

        $response['message'] = 'Nama pengguna tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Validasi kontak
    if(empty($kontak_akses)){

        $response['message'] = 'Nomor kontak tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Validasi format kontak
    if(!preg_match('/^[0-9]{6,20}$/', $kontak_akses)){

        $response['message'] = 'Nomor kontak hanya boleh 6-20 digit angka';

        echo json_encode($response);
        exit;
    }

    // Validasi email
    if(empty($email)){

        $response['message'] = 'Email tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Validasi format email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $response['message'] = 'Format email tidak valid';

        echo json_encode($response);
        exit;
    }

    // Cek duplikat kontak
    $StmtKontak = $Conn->prepare("
        SELECT id_akses 
        FROM akses
        WHERE kontak_akses = ?
        AND id_akses != ?
        LIMIT 1
    ");

    $StmtKontak->bind_param("si", $kontak_akses, $SessionIdAkses);

    $StmtKontak->execute();

    $ResultKontak = $StmtKontak->get_result();

    if($ResultKontak->num_rows > 0){

        $response['message'] = 'Nomor kontak sudah digunakan';

        echo json_encode($response);
        exit;
    }

    $StmtKontak->close();

    // Cek duplikat email
    $StmtEmail = $Conn->prepare("
        SELECT id_akses 
        FROM akses
        WHERE email_akses = ?
        AND id_akses != ?
        LIMIT 1
    ");

    $StmtEmail->bind_param("si", $email, $SessionIdAkses);

    $StmtEmail->execute();

    $ResultEmail = $StmtEmail->get_result();

    if($ResultEmail->num_rows > 0){

        $response['message'] = 'Email sudah digunakan';

        echo json_encode($response);
        exit;
    }

    $StmtEmail->close();

    // Update data
    $datetime_update = date('Y-m-d H:i:s');

    $StmtUpdate = $Conn->prepare("
        UPDATE akses
        SET 
            nama_akses = ?,
            kontak_akses = ?,
            email_akses = ?,
            datetime_update = ?
        WHERE id_akses = ?
    ");

    $StmtUpdate->bind_param(
        "ssssi",
        $nama_akses,
        $kontak_akses,
        $email,
        $datetime_update,
        $SessionIdAkses
    );

    if($StmtUpdate->execute()){

        $response['status'] = 'success';
        $response['message'] = 'Profil berhasil diperbarui';

    }else{

        $response['message'] = 'Gagal menyimpan data';
    }

    $StmtUpdate->close();

    echo json_encode($response);
?>