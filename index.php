<?php
$pageTitle = 'Dashboard Admin';
$brandName = 'SAPA Admin';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= htmlspecialchars($brandName) ?></title>
    <meta name="theme-color" content="#A4DD00">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="assets/img/logo/favicon.svg?v=2">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css?v=2">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css?v=2">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/300.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/400.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/400-italic.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/500.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/600.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/600-italic.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/700.css">
    <link rel="stylesheet" href="node_modules/@fontsource/plus-jakarta-sans/800.css">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light admin-navbar-wrap">
        <div class="container-fluid px-3 px-xxl-4">
            <div class="navbar-shell">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                    <span class="brand-logo">
                        <img src="assets/img/logo/logo.svg" alt="Logo" class="img-fluid">
                    </span>
                    <span class="brand-text">
                        <strong><?= htmlspecialchars($brandName) ?></strong>
                        <small>Web Admin Template</small>
                    </span>
                </a>
                <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas-lg offcanvas-end admin-offcanvas" tabindex="-1" id="adminNavbar" aria-labelledby="adminNavbarLabel">
                    <div class="offcanvas-header d-lg-none">
                        <div class="d-flex align-items-center gap-2" id="adminNavbarLabel">
                            <span class="brand-logo brand-logo-sm">
                                <img src="assets/img/logo/logo.svg" alt="Logo" class="img-fluid">
                            </span>
                            <div>
                                <div class="fw-semibold">Menu SAPA</div>
                                <small class="text-muted">Sistem Aspirasi dan Kepuasan Pasien</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                    </div>
                    <div class="offcanvas-body p-3 p-lg-0">
                        <ul class="navbar-nav ms-lg-auto align-items-lg-center gap-lg-1 admin-nav-list">
                            <li class="nav-item">
                                <a class="nav-link active" href="#"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="bi bi-people me-1"></i>Manajemen User</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-journal-text me-1"></i>Survei
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="#">Sesi Survei</a></li>
                                    <li><a class="dropdown-item" href="#">Daftar Pertanyaan</a></li>
                                    <li><a class="dropdown-item" href="#">Jawaban Responden</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-people-fill me-1"></i>Responden
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="#">Daftar Responden</a></li>
                                    <li><a class="dropdown-item" href="#">Kirim Tautan</a></li>
                                    <li><a class="dropdown-item" href="#">Riwayat Undangan</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-gear me-1"></i>Pengaturan Aplikasi
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="#">Identitas RS</a></li>
                                    <li><a class="dropdown-item" href="#">Logo & Favicon</a></li>
                                    <li><a class="dropdown-item" href="#">Koneksi SIMRS</a></li>
                                    <li><a class="dropdown-item" href="#">Email & WhatsApp Gateway</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="admin-main">
        <div class="container-fluid px-3 px-xxl-4">
            <section class="hero-panel mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="badge rounded-pill text-bg-success-subtle border border-success-subtle text-success mb-3">Dashboard SAPA</span>
                        <h1 class="display-6 fw-bold mb-3">Pantau aspirasi dan kepuasan pasien RSU El-Syifa</h1>
                        <p class="lead mb-4">Kelola sesi survei, responden, jawaban, dan laporan secara terpusat untuk membantu peningkatan mutu layanan kesehatan.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success btn-lg"><i class="bi bi-plus-circle me-1"></i>Buat Sesi Survei</button>
                            <button class="btn btn-outline-success btn-lg"><i class="bi bi-send me-1"></i>Kirim Tautan Responden</button>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted mb-1">Indeks Kepuasan Hari Ini</p>
                                    <h3 class="mb-0">87,4%</h3>
                                </div>
                                <span class="icon-badge"><i class="bi bi-heart-pulse"></i></span>
                            </div>
                            <div class="mini-stats">
                                <div>
                                    <small class="text-muted">Responden Aktif</small>
                                    <div class="fw-semibold">1.284</div>
                                </div>
                                <div>
                                    <small class="text-muted">Jawaban Terkumpul</small>
                                    <div class="fw-semibold">392</div>
                                </div>
                                <div>
                                    <small class="text-muted">Sesi Berjalan</small>
                                    <div class="fw-semibold">8</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon bg-success-subtle text-success"><i class="bi bi-journal-check"></i></div>
                        <div>
                            <small class="text-muted">Sesi Survei Aktif</small>
                            <h4 class="mb-0">12</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon bg-warning-subtle text-warning"><i class="bi bi-chat-square-text"></i></div>
                        <div>
                            <small class="text-muted">Pertanyaan Aktif</small>
                            <h4 class="mb-0">48</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon bg-primary-subtle text-primary"><i class="bi bi-envelope-paper"></i></div>
                        <div>
                            <small class="text-muted">Undangan Terkirim</small>
                            <h4 class="mb-0">12.903</h4>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row g-4">
                <div class="col-xl-8">
                    <div class="content-card content-card-heavy">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Grafik Indeks Kepuasan Pasien</h5>
                                <small class="text-muted">Perbandingan hasil respon survei bulanan</small>
                            </div>
                            <span class="badge rounded-pill text-bg-success-subtle text-success">Real-time</span>
                        </div>
                        <div id="salesChart" class="chart-box"></div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="content-card content-card-heavy h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Aktivitas Terkini</h5>
                                <small class="text-muted">Ringkasan proses pada SAPA</small>
                            </div>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <span class="activity-dot"></span>
                                <div>
                                    <div class="fw-semibold">Sesi survei rawat jalan dibuka</div>
                                    <small class="text-muted">5 menit yang lalu</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <span class="activity-dot"></span>
                                <div>
                                    <div class="fw-semibold">47 responden menerima tautan WhatsApp</div>
                                    <small class="text-muted">12 menit yang lalu</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <span class="activity-dot"></span>
                                <div>
                                    <div class="fw-semibold">Laporan kepuasan periode Juni diperbarui</div>
                                    <small class="text-muted">30 menit yang lalu</small>
                                </div>
                            </div>
                            <div class="activity-item">
                                <span class="activity-dot"></span>
                                <div>
                                    <div class="fw-semibold">Sinkronisasi SIMRS berhasil</div>
                                    <small class="text-muted">1 jam yang lalu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="admin-footer">
        <div class="container-fluid px-3 px-xxl-4">
            <div class="footer-shell">
                <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($brandName) ?>. All rights reserved.</span>
                <span class="text-muted">Bootstrap 5.3.8 | Bootstrap Icons 1.13.1 | jQuery 4.0.0 | ApexCharts 5.15.1</span>
            </div>
        </div>
    </footer>

    <script src="node_modules/jquery/dist/jquery.min.js?v=2"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=2"></script>
    <script src="node_modules/apexcharts/dist/apexcharts.min.js?v=2"></script>
    <script src="assets/js/main.js?v=2"></script>
</body>
</html>
