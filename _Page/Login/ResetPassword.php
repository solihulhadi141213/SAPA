<?php
    // Include Connection, Setting dan Session
    include "../../_Config/Connection.php";
    include "../../_Config/Setting.php";
    include "../../_Config/Session.php";

    // Menentukan Environment
    $env_version = "";
    if($environment_status!=="Production"){
        $env_version = date('YmdHis');
    }
?>
<!doctype html>
<html lang="id">
    <head>
        <!-- Metatag -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset Password | <?= $app_name ?></title>
        <meta name="theme-color" content="#A4DD00">
        <link rel="icon" type="../../image/svg+xml" href="../../assets/img/logo/<?= $app_icon ?>?v=<?php echo $env_version; ?>">
        <link rel="apple-touch-icon" href="../../assets/img/logo/<?= $app_icon ?>?v=<?php echo $env_version; ?>">
        
        <!-- Font -->
        <link href="../../assets/fonts/fonts.css?v=<?php echo $env_version; ?>" rel="stylesheet">

        <!-- bootstrap -->
        <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css?v=<?php echo $env_version; ?>">

        <!-- Bootstrap Icon -->
        <link rel="stylesheet" href="../../node_modules/bootstrap-icons/font/bootstrap-icons.min.css?v=<?php echo $env_version; ?>">

        <!-- fontsource -->
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/300.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/400.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/400-italic.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/500.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/600.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/600-italic.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/700.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="../../node_modules/@fontsource/plus-jakarta-sans/800.css?v=<?php echo $env_version; ?>">

        <!-- Custome Style -->
        <link rel="stylesheet" href="../../assets/css/login-style.css?v=<?php echo $env_version; ?>">
        
    </head>
    <body>
        <main class="login-shell">
            <div class="login-card">
                <div class="row g-0">
                    <div class="col-lg-5 text-center">
                        <div class="login-brand d-flex flex-column justify-content-between">
                            <div>
                                <span class="brand-logo">
                                    <img src="../../assets/img/logo/<?= $company_logo ?>?v=<?php echo $env_version; ?>" alt="Logo <?= $app_name ?>">
                                </span>
                                <h1 class="h3 fw-bold mb-2">
                                    <a href="" class="text text-decoration-none text-light"><?= $app_name ?></a>
                                </h1>
                                <p class="mb-0 opacity-75"><?= $app_description ?></p>
                            </div>
                            <div class="mt-4">
                                <div class="small opacity-75">
                                    <a href="index.php" class="text text-white">Kembali Ke Halaman Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="login-form">
                            <div class="mb-4">
                                <h2 class="h4 fw-bold mb-2">Reset Password</h2>
                                <p class="text-muted-soft mb-0">Sistem akan mengirimkan tautan reset password pada email anda.</p>
                            </div>
                            
                            <form action="javascript:void(0);" id="ProsesResetPassword" autocomplete="off">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="nama@domain.com" required>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="captcha" class="form-label">Captcha</label>
                                    <div class="captcha-box mb-2">
                                        <img id="image_captcha" src="../../_Config/Captcha.php" alt="Captcha" width="100%">
                                        <button type="button" class="btn btn-outline-success btn-captcha-reload" id="reloadCaptcha" aria-label="Reload captcha">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Ketik kode captcha di atas" required>
                                </div>
                                <div class="mb-2" id="NotifikasiResetPassword">
                                    <!-- Notifikasi Login Disini -->
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="ButtonResetPassword">
                                        <i class="bi bi-arrow-up-right"></i> Reset Password
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="row">
                <div class="col-12">
                    <small>
                        <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($app_name ) ?>. All rights reserved.</span>
                        <span class="text-muted">
                            Created By <?= htmlspecialchars($app_author) ?>
                        </span>
                    </small>
                </div>
            </div>

        </main>
        <script src="../../node_modules/jquery/dist/jquery.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="../../assets/js/main.js?v=<?php echo $env_version; ?>"></script>

        <!-- Sweet Alert -->
        <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>


        <script>
            // Reload Captcha
            $('#reloadCaptcha').click(function(){
                $('#image_captcha').attr('src', '../../_Config/Captcha.php?' + Date.now());
            });

            //Submit Login
            $('#ProsesResetPassword').submit(function(){
                var ProsesResetPassword = $('#ProsesResetPassword').serialize();
                var ButtonResetPassword = $('#ButtonResetPassword').html();
                $('#ButtonResetPassword').html('<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>');
                $.ajax({
                    type    : 'POST',
                    url     : '../../_Page/Login/ProsesResetPassword.php',
                    data    : ProsesResetPassword,
                    dataType: 'JSON',
                    success     : function(response){
                        let status = response.status;
                        let message = response.message;

                        if(status=='success'){
                            $('#ButtonResetPassword').html(ButtonResetPassword);
                            $('#NotifikasiResetPassword').html('');

                            // Reset Cap[tcha
                            $('#image_captcha').attr('src', '../../_Config/Captcha.php?' + Date.now());

                            // Reset Form
                            
                            // Tampilkan Swal dan reset Form
                            Swal.fire({
                                toast            : true,
                                position         : 'top-end',
                                icon             : 'success',
                                title            : response.message,
                                showConfirmButton: false,
                                timer            : 3000,
                                timerProgressBar : true,
                                didOpen          : (toast) => {

                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });

                            // Reset Form
                            $('#ProsesResetPassword')[0].reset();

                            // Hilangkan notifikasi jika ada
                            $('#NotifikasiResetPassword').html('');
                        }else{
                            $('#NotifikasiResetPassword').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                            $('#ButtonResetPassword').html(ButtonResetPassword);
                        }
                    }
                });
            });
        </script>
    </body>
</html>
