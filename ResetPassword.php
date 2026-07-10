<?php
    // Include Connection, Setting dan Session
    include "_Config/Connection.php";
    include "_Config/Setting.php";
    include "_Config/Helper.php";

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
                                <div class="small opacity-75">
                                    <a href="index.php" class="text text-white">Kembali Ke Halaman Utama</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="login-form">

                            <?php
                                if(empty($_GET['token'])){
                                    echo '
                                        <div class="alert alert-danger text-center">Token Tidak Boleh Kosong!</div>
                                    ';
                                    exit;
                                }

                                $token = validateAndSanitizeInput($_GET['token']);

                                // Validasi Token
                                $id_akses = GetDetailData($Conn, 'akses_reset', 'token', $token, 'id_akses');

                                if(empty($id_akses)){
                                    echo '
                                        <div class="alert alert-danger text-center">Token Tidak Valid!</div>
                                    ';
                                    exit;
                                }

                                // Validasi Expired Token
                                $datetime_expired = GetDetailData($Conn, 'akses_reset', 'token', $token, 'datetime_expired');
                                $now              = new DateTime('now', new DateTimeZone('UTC'));
                                $expired          = new DateTime($datetime_expired, new DateTimeZone('UTC'));

                                if ($expired < $now) {
                                    echo '
                                        <div class="alert alert-danger text-center">Tautan Sudah Tidak Berlaku. Silahkan hubungi petugas untuk mendapatkan tautan baru.</div>
                                    ';
                                    exit;
                                }
                            ?>
                            <div class="mb-4">
                                <h2 class="h4 fw-bold mb-2">Reset Password</h2>
                                <p class="text-muted-soft mb-0">
                                    Silahkan masukan password baru anda. Password harus terdiri dari 8-20 karakter angka atau huruf.
                                </p>
                            </div>
                            
                            <form action="javascript:void(0);" id="ProsesUbahPassword" autocomplete="off">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                <div class="mb-3">
                                    <label for="password_1" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="password_1" name="password_1" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password_2" class="form-label">Ulangi password</label>
                                    <input type="password" class="form-control" id="password_2" name="password_2" required>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="tampilkan_password">
                                    <label class="form-check-label" for="tampilkan_password">
                                        <small>Tampilkan Password</small>
                                    </label>
                                </div>
                               
                                <div class="mb-2" id="NotifikasiUbahPassword">
                                    <!-- Notifikasi Ubah pasword Disini -->
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="ButtonUbahPassword">
                                        <i class="bi bi-save"></i> Simpan Password
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
        <script src="node_modules/jquery/dist/jquery.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?php echo $env_version; ?>"></script>
        <script src="assets/js/main.js?v=<?php echo $env_version; ?>"></script>

        <!-- Sweet Alert -->
        <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>


        <script>
            // Tampilkan Password
            $('#tampilkan_password').on('change', function () {
                $('#password_1, #password_2').attr(
                    'type',
                    this.checked ? 'text' : 'password'
                );
            });

            //Submit Ubah Password
            $('#ProsesUbahPassword').submit(function(){
                var ProsesUbahPassword = $('#ProsesUbahPassword').serialize();
                var ButtonUbahPassword = $('#ButtonUbahPassword').html();
                $('#ButtonUbahPassword').html('<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>');
                $.ajax({
                    type    : 'POST',
                    url     : '_Page/Login/ProsesUbahPassword.php',
                    data    : ProsesUbahPassword,
                    dataType: 'JSON',
                    success     : function(response){
                        let status  = response.status;
                        let message = response.message;

                        if(status=='success'){
                            $('#ButtonUbahPassword').html(ButtonUbahPassword);
                            $('#NotifikasiUbahPassword').html('');
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Berhasil Diubah',
                                text: message,
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = "index.php";
                            });
                        }else{
                            $('#NotifikasiUbahPassword').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                            $('#ButtonUbahPassword').html(ButtonUbahPassword);
                        }
                    }
                });
            });
        </script>
    </body>
</html>
