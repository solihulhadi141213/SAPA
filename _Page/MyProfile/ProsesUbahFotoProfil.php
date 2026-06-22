<?php
    header('Content-Type: application/json');

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/Session.php";

    date_default_timezone_set('Asia/Jakarta');

    $response = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan'
    ];

    // Validasi session
    if(empty($SessionIdAkses)){

        $response['message'] = 'Sesi login sudah berakhir';

        echo json_encode($response);
        exit;
    }

    // Validasi file
    if(empty($_FILES['image_akses']['name'])){

        $response['message'] = 'File foto tidak boleh kosong';

        echo json_encode($response);
        exit;
    }

    // Data file
    $file_name = $_FILES['image_akses']['name'];
    $file_size = $_FILES['image_akses']['size'];
    $file_tmp  = $_FILES['image_akses']['tmp_name'];
    $file_error = $_FILES['image_akses']['error'];

    // Validasi upload error
    if($file_error !== 0){

        $response['message'] = 'Gagal upload file';

        echo json_encode($response);
        exit;
    }

    // Validasi ukuran
    if($file_size > 2000000){

        $response['message'] = 'Ukuran file maksimal 2 MB';

        echo json_encode($response);
        exit;
    }

    // Ekstensi file
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed extension
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if(!in_array($ext, $allowed_ext)){

        $response['message'] = 'Format file tidak didukung';

        echo json_encode($response);
        exit;
    }

    // Validasi mime type
    $mime = mime_content_type($file_tmp);

    $allowed_mime = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    if(!in_array($mime, $allowed_mime)){

        $response['message'] = 'Mime type file tidak valid';

        echo json_encode($response);
        exit;
    }

    // Generate nama file baru
    $new_file_name = md5(
        uniqid().time()
    ).".".$ext;

    // Path upload
    $upload_path = "../../assets/img/user/".$new_file_name;

    // Ambil foto lama
    $StmtOld = $Conn->prepare("
        SELECT image_akses
        FROM akses
        WHERE id_akses = ?
    ");

    $StmtOld->bind_param("i", $SessionIdAkses);

    $StmtOld->execute();

    $ResultOld = $StmtOld->get_result();

    $DataOld = $ResultOld->fetch_assoc();

    $old_image = $DataOld['image_akses'] ?? '';

    $StmtOld->close();

    // Upload file
    if(!move_uploaded_file($file_tmp, $upload_path)){

        $response['message'] = 'Gagal memindahkan file upload';

        echo json_encode($response);
        exit;
    }

    // Update database
    $datetime_update = date('Y-m-d H:i:s');

    $StmtUpdate = $Conn->prepare("
        UPDATE akses
        SET 
            image_akses = ?,
            datetime_update = ?
        WHERE id_akses = ?
    ");

    $StmtUpdate->bind_param(
        "ssi",
        $new_file_name,
        $datetime_update,
        $SessionIdAkses
    );

    if($StmtUpdate->execute()){

        // Hapus foto lama
        if(!empty($old_image)){

            $old_path = "../../assets/img/user/".$old_image;

            if(file_exists($old_path)){

                @unlink($old_path);
            }
        }

        $response['status'] = 'success';

        $response['message'] = 'Foto profil berhasil diperbarui';

    }else{

        // Hapus file baru jika gagal update DB
        if(file_exists($upload_path)){

            @unlink($upload_path);
        }

        $response['message'] = 'Gagal update database';
    }

    $StmtUpdate->close();

    echo json_encode($response);
?>