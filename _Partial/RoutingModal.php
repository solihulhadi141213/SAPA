<?php
    include "_Page/Logout/ModalLogout.php";
    if(!empty($_GET['Page'])){
        $Page=$_GET['Page'];
        
        // Daftar halaman dan modal yang terkait
        $modals = [
            "MyProfile"          => "_Page/MyProfile/ModalMyProfile.php",
            "Aksesibilitas"      => "_Page/Aksesibilitas/ModalAksesibilitas.php",
            "SettingGeneral"     => "_Page/SettingGeneral/ModalSettingGeneral.php",
            "KoneksiSimrs"       => "_Page/KoneksiSimrs/ModalKoneksiSimrs.php",
            "EmailGateway"       => "_Page/EmailGateway/ModalEmailGateway.php",
            "WahtsappGateway"    => "_Page/WahtsappGateway/ModalWahtsappGateway.php",
            "Responden"          => "_Page/Responden/ModalResponden.php",
            "Pertanyaan"         => "_Page/Pertanyaan/ModalPertanyaan.php",
            "Undangan"           => "_Page/Undangan/ModalUndangan.php",
            "Jawaban"            => "_Page/Jawaban/ModalJawaban.php",
            "DeskripsiResponden" => "_Page/DeskripsiResponden/ModalDeskripsiResponden.php",
            "GoogleCredential"   => "_Page/GoogleCredential/ModalGoogleCredential.php",
        ];

        // Cek apakah halaman memiliki modal terkait dan sertakan file modalnya
        if (!empty($_GET['Page']) && isset($modals[$_GET['Page']])) {
            include $modals[$_GET['Page']];
        }
    }
?>
