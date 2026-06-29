<?php
    include "_Config/Connection.php";
    include "_Config/Setting.php";

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
        <title>Login | <?= $app_name ?></title>
        <meta name="theme-color" content="#A4DD00">
        <link rel="icon" type="image/svg+xml" href="assets/img/logo/<?= $app_icon ?>?v=<?php echo $env_version; ?>">
        <link rel="apple-touch-icon" href="assets/img/logo/<?= $app_icon ?>?v=<?php echo $env_version; ?>">
        
        <!-- Font -->
        <link href="assets/fonts/fonts.css?v=<?php echo $env_version; ?>" rel="stylesheet">

        <!-- bootstrap -->
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css?v=<?php echo $env_version; ?>">

        <!-- Bootstrap Icon -->
        <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css?v=<?php echo $env_version; ?>">

        <!-- fontsource -->
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/300.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/400.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/400-italic.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/500.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/600.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/600-italic.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/700.css?v=<?php echo $env_version; ?>">
        <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/800.css?v=<?php echo $env_version; ?>">

        <!-- Custome Style -->
        <link rel="stylesheet" href="assets/css/login-style.css?v=<?php echo $env_version; ?>">
        
    </head>
    <body>
        <main class="login-shell">
            <div class="login-card">
                <div class="row g-0">
                    <div class="col-lg-5 text-center">
                        <div class="login-brand d-flex flex-column justify-content-between">
                            <div>
                                <span class="brand-logo">
                                    <img src="assets/img/logo/<?= $company_logo ?>?v=<?php echo $env_version; ?>" alt="Logo <?= $app_name ?>">
                                </span>
                                <h1 class="h3 fw-bold mb-2">
                                    <a href="" class="text text-decoration-none text-light"><?= $app_name ?></a>
                                </h1>
                                <p class="mb-0 opacity-75"><?= $app_description ?></p>
                            </div>
                            <div class="mt-4">
                                <div class="small opacity-75">Login aman untuk mengakses dashboard aplikasi.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="login-form">
                            <div class="mb-4">
                                <h2 class="h4 fw-bold mb-2">Masuk ke akun Anda</h2>
                                <p class="text-muted-soft mb-0">Silakan isi email, password, dan captcha untuk melanjutkan.</p>
                            </div>
                            
                            <form action="javascript:void(0);" id="ProsesLogin" autocomplete="off">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="nama@domain.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Tampilkan password">
                                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="captcha" class="form-label">Captcha</label>
                                    <div class="captcha-box mb-2">
                                        <img id="image_captcha" src="_Config/Captcha.php" alt="Captcha" width="100%">
                                        <button type="button" class="btn btn-outline-success btn-captcha-reload" id="reloadCaptcha" aria-label="Reload captcha">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Ketik kode captcha di atas" required>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <a href="ResetPassword.php" class="link-success text-decoration-none">Lupa password?</a>
                                </div>
                                <div class="mb-2" id="NotifikasiLogin">
                                    <!-- Notifikasi Login Disini -->
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="ButtonLogin">
                                        <i class="bi bi-arrow-up-right"></i>Login
                                    </button>
                                    <a href="_Page/Login/LoginGoogle.php" class="btn google-btn btn-lg">
                                        <i class="bi bi-google me-2"></i>Login Dengan Google
                                    </a>
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
        <script src="node_modules/jquery/dist/jquery.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="assets/js/main.js?v=<?php echo $env_version; ?>"></script>

        <script>
            // Reload Captcha
            $('#reloadCaptcha').click(function(){
                $('#image_captcha').attr('src', '_Config/Captcha.php?' + Date.now());
            });

            // Toggle tampilkan password
            $('#togglePassword').on('click', function(){
                const input = $('#password');
                const icon = $('#togglePasswordIcon');
                const isPassword = input.attr('type') === 'password';

                input.attr('type', isPassword ? 'text' : 'password');
                icon.removeClass('bi-eye bi-eye-slash')
                    .addClass(isPassword ? 'bi-eye-slash' : 'bi-eye');

                $(this).attr('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });

            //Submit Login
            $('#ProsesLogin').submit(function(){
                var ProsesLogin = $('#ProsesLogin').serialize();
                var ButtonLogin = $('#ButtonLogin').html();
                $('#ButtonLogin').html('<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>');
                $.ajax({
                    type    : 'POST',
                    url     : '_Page/Login/ProsesLogin.php',
                    data    : ProsesLogin,
                    dataType: 'JSON',
                    success     : function(response){
                        let status = response.status;
                        let message = response.message;

                        if(status=='success'){
                            $('#ButtonLogin').html(ButtonLogin);
                            $('#NotifikasiLogin').html('');
                            window.location.href = "index.php";
                        }else{
                            $('#NotifikasiLogin').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                            $('#ButtonLogin').html(ButtonLogin);
                        }
                    }
                });
            });
        </script>
    </body>
</html>
