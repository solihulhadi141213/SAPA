<?php
    // Include Connection, Setting dan Session
    include "_Config/Connection.php";
    include "_Config/Setting.php";
    include "_Config/Session.php";

    // Validasi Sesi Akses
    if(empty($SessionIdAkses)){
        include "_Page/Login/Login.php";
        exit;
    }

    // Menentukan Environment
    $env_version = "";
    if($environment_status!=="Production"){
        $env_version = date('YmdHis');
    }
?>
<!doctype html>
<html lang="id">
    <?php
        // Init 'Page' Variabel
        $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

        // Routing Title By Page
        $list_halaman = [
            "MyProfile"      => "Profile Saya",
            "Aksesibilitas"  => "Aksesibilitas",
            "Profile"        => "Profile",
            "SettingGeneral" => "Pengaturan Umum",
            "KoneksiSimrs"   => "Koneksi Simrs",
            "EmailGateway"   => "Email Gateway",
        ];
        
        // Init Page Title
        $judul_halaman = isset($list_halaman[$Page]) ? $list_halaman[$Page] : "Dashboard";
    ?>
    <?php
        // Header
        include "_Partial/Head.php";
    ?>
    <body>
        <?php
            // Navbar
            include "_Partial/Navbar.php";
        ?>

        <main class="admin-main">
            <div class="container-fluid px-3 px-xxl-4">
                <?php
                    // Routing Page & Modal
                    include "_Partial/RoutingPage.php";
                    include "_Partial/RoutingModal.php";
                ?>
            </div>
        </main>
        <?php
            include "_Partial/Copyright.php";
        ?>
        
        <div class="floating-actions" aria-label="Aksi cepat">
            <button type="button" class="floating-btn" id="toggleDarkMode" aria-label="Aktifkan mode gelap" aria-pressed="false">
                <i class="bi bi-moon-stars"></i>
            </button>
            <button type="button" class="floating-btn" id="backToTop" aria-label="Kembali ke atas">
                <i class="bi bi-arrow-up"></i>
            </button>
        </div>

        <?php
            // Footer JS
            include "_Partial/FooterJs.php";

            // Routing JS
            include "_Partial/RoutingJs.php";

            // Routing Swal
            include "_Partial/RoutingSwal.php";
        ?>
    </body>
</html>
