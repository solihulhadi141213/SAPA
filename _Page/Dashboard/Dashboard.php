<section class="hero-panel mb-4 hero-bg-image">
    <div class="row align-items-center g-4">
        <div class="col-12">
            <span class="badge rounded-pill text-bg-success-subtle border border-success-subtle text-success mb-3">Dashboard</span>
            <h1 class="display-6 fw-bold mb-3"><?php echo $company_name; ?></h1>
            <p class="lead mb-4"><?php echo $company_address; ?></p>
            <small>
                <a href="https://spot-sable-36328494.figma.site">
                    Lihat Cara Kerja Sistem <i class="bi bi-chevron-compact-right"></i>
                </a>
            </small>
        </div>
    </div>
</section>
<div id="notifikasi_count">
    <!-- Apabila Ada Masalah Pada Saat Hitung Data Maka Akan Ditampilkan Disini -->
</div>
<section class="row g-4 mb-4">
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="metric-card">
            <div class="metric-icon bg-success-subtle text-success">
                <i class="bi bi-question-circle"></i>
            </div>
            <div>
                <small class="text-muted">Pertanyaan</small>
                <h4 class="mb-0" id="jumlah_pertanyaan">00.000</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="metric-card">
            <div class="metric-icon bg-warning-subtle text-warning">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <small class="text-muted">Responden</small>
                <h4 class="mb-0" id="jumlah_responden">00.000</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="metric-card">
            <div class="metric-icon bg-primary-subtle text-primary">
                <i class="bi bi-send"></i>
            </div>
            <div>
                <small class="text-muted">Undangan</small>
                <h4 class="mb-0" id="jumlah_undangan">00.000</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="metric-card">
            <div class="metric-icon bg-info-subtle text-info">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <small class="text-muted">Jawaban</small>
                <h4 class="mb-0" id="jumlah_jawaban">00.000</h4>
            </div>
        </div>
    </div>
</section>

<section class="row mb-3 g-4">
    <div class="col-12">
        <div class="content-card content-card-heavy">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Grafik Partisipasi Responden Bulanan</h5>
                    <small class="text-muted">Jumlah partisipasi responden terhadap survei bulanan</small>
                </div>
                <span class="badge rounded-pill text-bg-success-subtle text-success">Real-time</span>
            </div>
            <div class="chart-box" id="chart_partisipasi_responden">
                <div class="chart-placeholder h-100 d-flex align-items-center justify-content-center">
                    <!-- Menampilkan Chart Disini -->
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-md-4 mb-3">
        <div class="content-card content-card-heavy">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Capaian Partisipasi Responden</h5>
                    <small class="text-muted">Perbandingan Jumlah Total Responden Dengan Jumlah Jawaban</small>
                </div>
            </div>
            <div class="chart-box" id="chart_gap_partisipasi_responden">
                <div class="chart-placeholder h-100 d-flex align-items-center justify-content-center">
                    <!-- Menampilkan Chart Jumlah Jumlah Responden Vs Jawaban Disini -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="content-card content-card-heavy">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Gender</h5>
                    <small class="text-muted">Jumlah Responden Berdasarkan Gender</small>
                </div>
            </div>
            <div class="chart-box" id="chart_gender_responden">
                <div class="chart-placeholder h-100 d-flex align-items-center justify-content-center">
                    <!-- Menampilkan Chart Jumlah Responden Berdasarkan Gender Disini -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="content-card content-card-heavy">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Tujuan Kunjungan</h5>
                    <small class="text-muted">Jumlah Responden Berdasarkan Tujuan Kunjungan</small>
                </div>
            </div>
            <div class="chart-box" id="chart_encounter_responden">
                <div class="chart-placeholder h-100 d-flex align-items-center justify-content-center">
                    <!-- Menampilkan Chart Jumlah Responden Berdasarkan Tujuan Kunjungan Disini -->
                </div>
            </div>
        </div>
    </div>
</section>
