<?php 
    $date_version=date('YmdHis');
    if(empty($_GET['Page'])){
        //Dafault Javascript Diarahkan Ke Dashboard
        echo '<script type="text/javascript" src="_Page/Dashboard/Dashboard.js?V='.$date_version.'"></script>';
    }else{
        $Page=$_GET['Page'];
        // Routing Javascript Berdasarkan Halaman
        $scripts = [
            "MyProfile"       => "_Page/MyProfile/MyProfile.js",
            "Aksesibilitas"   => "_Page/Aksesibilitas/Aksesibilitas.js",
            "SettingGeneral"  => "_Page/SettingGeneral/SettingGeneral.js",
            "KoneksiSimrs"    => "_Page/KoneksiSimrs/KoneksiSimrs.js",
            "EmailGateway"    => "_Page/EmailGateway/EmailGateway.js",
            "WahtsappGateway" => "_Page/WahtsappGateway/WahtsappGateway.js",
            "Responden"       => "_Page/Responden/Responden.js",
            "Pertanyaan"      => "_Page/Pertanyaan/Pertanyaan.js",
            "Undangan"        => "_Page/Undangan/Undangan.js",
            "Jawaban"         => "_Page/Jawaban/Jawaban.js",
        ];

        // Cek apakah halaman ada dalam daftar dan sertakan file JS yang sesuai
        if (!empty($_GET['Page']) && isset($scripts[$_GET['Page']])) {
            echo '<script type="text/javascript" src="' . $scripts[$_GET['Page']] . '?V='.$date_version.'"></script>';
        }
    }
?>