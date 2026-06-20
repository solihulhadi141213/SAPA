<?php 
    $date_version=date('YmdHis');
    if(empty($_GET['Page'])){
        //Dafault Javascript Diarahkan Ke Dashboard
        echo '<script type="text/javascript" src="_Page/Dashboard/Dashboard.js?V='.$date_version.'"></script>';
    }else{
        $Page=$_GET['Page'];
        // Routing Javascript Berdasarkan Halaman
        $scripts = [
            "MyProfile"           => "_Page/MyProfile/MyProfile.js",
            "AksesFitur"          => "_Page/AksesFitur/AksesFitur.js",
            "AksesEntitas"        => "_Page/AksesEntitas/AksesEntitas.js",
            "Akses"               => "_Page/Akses/Akses.js",
            "Anggota"             => "_Page/Anggota/Anggota.js",
            "JenisSimpanan"       => "_Page/JenisSimpanan/JenisSimpanan.js",
            "JenisPinjaman"       => "_Page/JenisPinjaman/JenisPinjaman.js",
            "Supplier"            => "_Page/Supplier/Supplier.js",
            "KategoriHarga"       => "_Page/KategoriHarga/KategoriHarga.js",
            "Barang"              => "_Page/Barang/Barang.js",
            "BatchExpired"        => "_Page/BatchExpired/BatchExpired.js",
            "Diskon"              => "_Page/Diskon/Diskon.js",
            "StockOpename"        => "_Page/StockOpename/StockOpename.js",
            "SettingGeneral"      => "_Page/SettingGeneral/SettingGeneral.js",
            "SettingEmailGateway" => "_Page/SettingEmailGateway/SettingEmailGateway.js",
        ];

        // Cek apakah halaman ada dalam daftar dan sertakan file JS yang sesuai
        if (!empty($_GET['Page']) && isset($scripts[$_GET['Page']])) {
            echo '<script type="text/javascript" src="' . $scripts[$_GET['Page']] . '?V='.$date_version.'"></script>';
        }
    }
    echo '<script type="text/javascript" src="_Partial/Universal.js?V='.$date_version.'"></script>';
?>