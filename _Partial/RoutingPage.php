<?php
    if(empty($_GET['Page'])){
        include "_Page/Dashboard/Dashboard.php";
    }else{
        $Page=$_GET['Page'];
        
        //Index Halaman
        $page_arry=[
            "MyProfile"           => "_Page/MyProfile/MyProfile.php",
            "Aksesibilitas"       => "_Page/Aksesibilitas/Aksesibilitas.php",
            "SettingGeneral"      => "_Page/SettingGeneral/SettingGeneral.php",
            "KoneksiSimrs"        => "_Page/KoneksiSimrs/KoneksiSimrs.php",
            "EmailGateway"        => "_Page/EmailGateway/EmailGateway.php",
            "WahtsappGateway"     => "_Page/WahtsappGateway/WahtsappGateway.php",
            "Responden"           => "_Page/Responden/Responden.php",
            "Pertanyaan"          => "_Page/Pertanyaan/Pertanyaan.php",
            "Undangan"            => "_Page/Undangan/Undangan.php",
            "Jawaban"             => "_Page/Jawaban/Jawaban.php",
            "DeskripsiResponden"  => "_Page/DeskripsiResponden/DeskripsiResponden.php",
            "DeskripsiPertanyaan" => "_Page/DeskripsiPertanyaan/DeskripsiPertanyaan.php",
            "GoogleCredential"    => "_Page/GoogleCredential/GoogleCredential.php",
            "Error"               => "_Page/Error/Error.php"
        ];

        //Tangkap 'Page'
        $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

        //Kondisi Pada masing-masing Page
        if (array_key_exists($Page, $page_arry)) { 
            include $page_arry[$Page]; 
        } else { 
            include "_Page/Error/Error.php";
        }
    }
?>
