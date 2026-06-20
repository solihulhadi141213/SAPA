<?php
    if(empty($_GET['Page'])){
        include "_Page/Dashboard/Dashboard.php";
    }else{
        $Page=$_GET['Page'];
        
        //Index Halaman
        $page_arry=[
            "MyProfile"           => "_Page/MyProfile/MyProfile.php",
            "AksesFitur"          => "_Page/AksesFitur/AksesFitur.php",
            "AksesEntitas"        => "_Page/AksesEntitas/AksesEntitas.php",
            "Akses"               => "_Page/Akses/Akses.php",
            "Anggota"             => "_Page/Anggota/Anggota.php",
            "JenisSimpanan"       => "_Page/JenisSimpanan/JenisSimpanan.php",
            "JenisPinjaman"       => "_Page/JenisPinjaman/JenisPinjaman.php",
            "Supplier"            => "_Page/Supplier/Supplier.php",
            "KategoriHarga"       => "_Page/KategoriHarga/KategoriHarga.php",
            "Barang"              => "_Page/Barang/Barang.php",
            "BatchExpired"        => "_Page/BatchExpired/BatchExpired.php",
            "Diskon"              => "_Page/Diskon/Diskon.php",
            "StockOpename"        => "_Page/StockOpename/StockOpename.php",
            "SettingGeneral"      => "_Page/SettingGeneral/SettingGeneral.php",
            "SettingEmailGateway" => "_Page/SettingEmailGateway/SettingEmailGateway.php",
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