<?php
    header('Content-Type: application/json');

    // Timezone
    date_default_timezone_set('Asia/Jakarta');

    // Koneksi & Session
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    // Default response
    $response = [
        'status'  => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // Validasi session
    if(empty($SessionIdAkses)){

        $response['message'] = 'Sesi login sudah berakhir';

        echo json_encode($response);
        exit;
    }

    // Tangkap input
    $password1 = trim($_POST['password1'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    // Validasi password kosong
    if(empty($password1)){

        $response['message'] = 'Password baru tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Validasi konfirmasi password
    if(empty($password2)){

        $response['message'] = 'Konfirmasi password tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Validasi password sama
    if($password1 !== $password2){

        $response['message'] = 'Konfirmasi password tidak sama';

        echo json_encode($response);
        exit;
    }

    // Validasi panjang password
    $jumlah_karakter = strlen($password1);

    if($jumlah_karakter < 6 || $jumlah_karakter > 20){

        $response['message'] = 'Password harus 6-20 karakter';

        echo json_encode($response);
        exit;
    }

    // Validasi karakter password
    if(!preg_match('/^[a-zA-Z0-9]+$/', $password1)){

        $response['message'] = 'Password hanya boleh huruf dan angka';

        echo json_encode($response);
        exit;
    }

    // HASH PASSWORD MODERN
    $password_hash = password_hash(
        $password1,
        PASSWORD_DEFAULT
    );

    // Update database
    $StmtUpdate = $Conn->prepare("
        UPDATE akses
        SET password = ?
        WHERE id_akses = ?
    ");

    $StmtUpdate->bind_param(
        "si",
        $password_hash,
        $SessionIdAkses
    );

    // Eksekusi update
    if($StmtUpdate->execute()){

        $response['status'] = 'success';

        $response['message'] = 'Password berhasil diperbarui';

    }else{

        $response['message'] = 'Gagal menyimpan password';
    }

    // Tutup statement
    $StmtUpdate->close();

    // Return JSON
    echo json_encode($response);
?>