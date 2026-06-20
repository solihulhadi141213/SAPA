<section class="hero-panel mb-4">
    <div class="row align-items-center g-4">
        <div class="col-12">
            <span class="badge rounded-pill text-bg-success-subtle border border-success-subtle text-success mb-3">Dashboard</span>
            <h1 class="display-6 fw-bold mb-3"><?php echo $company_name; ?></h1>
            <p class="lead mb-4"><?php echo $company_address; ?></p>
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
            <div class="chart-box chart-placeholder">
                <div class="fw-semibold mb-2">Grafik dimatikan untuk performa lebih ringan</div>
                <small class="text-muted">Mode ini menampilkan halaman lebih cepat dan lebih stabil di Firefox.</small>
            </div>
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

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <b>Data Sesi Survey</b>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td><b>No</b></td>
                                <td><b>Nama Survey</b></td>
                                <td><b>Mulai</b></td>
                                <td><b>Selesai</b></td>
                                <td><b>Pertanyaan</b></td>
                                <td><b>Responsen</b></td>
                                <td><b>Opsi</b></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                Page 1 Of 100
            </div>
        </div>
    </div>
</div>