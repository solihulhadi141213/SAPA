<div class="pagetitle mb-4">
    <h1>
        <a href=""><i class="bi bi-question-circle"></i> Daftar Pertanyaan</a></a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"> Daftar Pertanyaan</li>
        </ol>
    </nav>
</div>

<section class="hero-panel mb-4">
    <div class="row align-items-center g-4">
        <div class="col-6">
            <a href="javascript:void(0);" class="px-2 py-1 rounded-2 bg-danger-subtle text-danger tampilan_sampah" data-sampah="false">
                <small>
                    <i class="bi bi-eye-slash"></i> Sampah (0)
                </small>
            </a>
        </div>
        <div class="col-6 text-end">
            <button type="button" class="btn btn-md btn-outline-secondary btn-floating reload_data" title="Reload">
                <i class="bi bi-repeat"></i>
            </button>
            <button type="button" class="btn btn-md btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalTambah" title="Tambah Pertanyaan">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    </div>
</section>

<div id="daftar_pertanyaan">
    <!-- Daftar Pertanyaan Akan Ditampilkan Disini -->
    <div class="alert alert-danger text-center">
        <small>
            Tidak ada daftar pertanyaan yang ditampilkan.
        </small>
    </div>
</div>
