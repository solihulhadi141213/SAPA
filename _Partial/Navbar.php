<nav class="navbar navbar-expand-lg navbar-light admin-navbar-wrap">
    <div class="container-fluid px-3 px-xxl-4">
        <div class="navbar-shell">
            <a class="navbar-brand d-flex align-items-center gap-2" href="">
                <span class="brand-logo">
                    <img src="assets/img/logo/<?php echo $app_icon; ?>?v=<?php echo $env_version; ?>" alt="Logo" class="img-fluid">
                </span>
                <span class="brand-text">
                    <strong><?= htmlspecialchars($app_name) ?></strong>
                    <small><?php echo $app_description; ?></small>
                </span>
            </a>
            <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-label="Toggle navigation">
                <i class="bi bi-list navbar-toggler-icon-custom" aria-hidden="true"></i>
            </button>
            <div class="offcanvas-lg offcanvas-end admin-offcanvas" tabindex="-1" id="adminNavbar" aria-labelledby="adminNavbarLabel">
                <div class="offcanvas-header d-lg-none">
                    <div class="d-flex align-items-center gap-2" id="adminNavbarLabel">
                        <span class="brand-logo brand-logo-sm">
                            <img src="assets/img/logo/<?php echo $app_icon; ?>?v=<?php echo $env_version; ?>" alt="Logo" class="img-fluid">
                        </span>
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($app_name) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($app_description) ?></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-custom shadow-none" data-bs-dismiss="offcanvas" data-mdb-dismiss="offcanvas" aria-label="Tutup">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="offcanvas-body p-3 p-lg-0">
                    <ul class="navbar-nav ms-lg-auto align-items-lg-center gap-lg-1 admin-nav-list">
                        <li class="nav-item">
                            <a class="nav-link 
                                <?php 
                                    if($Page==""||$Page=="Dashboard"){echo "active";} 
                                ?>
                            " href="index.php">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if($Page=="Aksesibilitas"){echo "active";} ?>" href="index.php?Page=Aksesibilitas">
                                <i class="bi bi-people me-1"></i>Aksesibilitas
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle 
                                <?php 
                                    if(
                                        $Page=="SesiSurvey"||
                                        $Page=="Pertanyaan"||
                                        $Page=="Jawaban"
                                    ){echo "active";} 
                                ?>
                            " href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-journal-text me-1"></i>Survei
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item <?php if($Page=="SesiSurvey"){echo "active";}  ?>" href="index.php?Page=SesiSurvey">Sesi Survei</a></li>
                                <li><a class="dropdown-item <?php if($Page=="Pertanyaan"){echo "active";}  ?>" href="index.php?Page=Pertanyaan">Daftar Pertanyaan</a></li>
                                <li><a class="dropdown-item <?php if($Page=="Jawaban"){echo "active";}  ?>" href="index.php?Page=Jawaban">Jawaban Responden</a></li>
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
                                <i class="bi bi-gear me-1"></i>Setting
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="#">Identitas RS</a></li>
                                <li><a class="dropdown-item" href="#">Logo & Favicon</a></li>
                                <li><a class="dropdown-item" href="#">Koneksi SIMRS</a></li>
                                <li><a class="dropdown-item" href="#">Email & WhatsApp Gateway</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> Hai, Sol
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="#">My Profile</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
