<div class="pagetitle mb-4">
    <h1>
        <a href=""><i class="bi bi-gear"></i> Pengaturan Umum</a></a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active"> Pengaturan Umum</li>
        </ol>
    </nav>
</div>

<div id="data_view">

    <section class="hero-panel mb-4">
        <div class="row align-items-center g-4 mb-3">
            <div class="col-12 text-end">
                <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter" title="Filter Data">
                    <i class="bi bi-filter"></i>
                </button>
                <button type="button" class="btn btn-md btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalTambah" title="Tambah Akun Akses">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-12">
                <div class="table table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td class="text-center"><small><b>No</b></small></td>
                                <td class="text-center"><small><b>Pavicon</b></small></td>
                                <td><small><b>App Name</b></small></td>
                                <td><small><b>Company Name</b></small></td>
                                <td><small><b>Base URL</b></small></td>
                                <td class="text-center"><small><b>Environment</b></small></td>
                                <td class="text-center"><small><b>Status</b></small></td>
                                <td class="text-center"><small><b>Opsi</b></small></td>
                            </tr>
                        </thead>
                        <tbody id="tabel_pengaturan_umum">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <small>No Data</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <small id="page_info">
                    Page 1 Of 100
                </small>
            </div>
            <div class="col-6 text-end">
                <button type="button" class="btn btn-md btn-outline-secondary btn-floating" id="prev_button">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-md btn-outline-secondary btn-floating" id="next_button">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

</div>

<div id="">

</div>