<?php
    include "_Page/Logout/ModalLogout.php";
    if(!empty($_GET['Page'])){
        $Page=$_GET['Page'];
        
        // Daftar halaman dan modal yang terkait
        $modals = [
            "MyProfile"      => "_Page/MyProfile/ModalMyProfile.php",
            "AksesFitur"     => "_Page/AksesFitur/ModalAksesFitur.php",
            "AksesEntitas"   => "_Page/AksesEntitas/ModalAksesEntitas.php",
            "Akses"          => "_Page/Akses/ModalAkses.php",
            "Anggota"        => "_Page/Anggota/ModalAnggota.php",
            "JenisSimpanan"  => "_Page/JenisSimpanan/ModalJenisSimpanan.php",
            "JenisPinjaman"  => "_Page/JenisPinjaman/ModalJenisPinjaman.php",
            "Supplier"       => "_Page/Supplier/ModalSupplier.php",
            "KategoriHarga"  => "_Page/KategoriHarga/ModalKategoriHarga.php",
            "Barang"         => "_Page/Barang/ModalBarang.php",
            "BatchExpired"   => "_Page/BatchExpired/ModalBatchExpired.php",
            "Diskon"         => "_Page/Diskon/ModalDiskon.php",
            "StockOpename"   => "_Page/StockOpename/ModalStockOpename.php",
            "SettingGeneral" => "_Page/SettingGeneral/ModalSettingGeneral.php",
        ];

        // Cek apakah halaman memiliki modal terkait dan sertakan file modalnya
        if (!empty($_GET['Page']) && isset($modals[$_GET['Page']])) {
            include $modals[$_GET['Page']];
        }
    }
?>