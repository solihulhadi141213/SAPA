<?php
    if(empty($_GET['Page'])){
        include "_Page/Dashboard/Dashboard.php";
    }else{
        $Page=$_GET['Page'];
        
        //Index Halaman
        $page_arry=[
            "MyProfile"       => "_Page/MyProfile/MyProfile.php",
            "Aksesibilitas"   => "_Page/Aksesibilitas/Aksesibilitas.php",
            "SettingGeneral"  => "_Page/SettingGeneral/SettingGeneral.php",
            "KoneksiSimrs"    => "_Page/KoneksiSimrs/KoneksiSimrs.php",
            "EmailGateway"    => "_Page/EmailGateway/EmailGateway.php",
            "WahtsappGateway" => "_Page/WahtsappGateway/WahtsappGateway.php",
            "Error"           => "_Page/Error/Error.php"
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