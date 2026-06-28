<div class="pagetitle mb-4">
    <h1>
        <a href=""><i class="bi bi-file-text"></i> Jawaban</a></a>
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active"> Jawaban</li>
        </ol>
    </nav>
</div>

<section class="hero-panel mb-4">
    <div class="row align-items-center g-4 mb-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-md btn-outline-secondary btn-floating reload_data" title="Reload">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" class="btn btn-md btn-secondary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalFilter" title="Filter Data">
                <i class="bi bi-filter"></i>
            </button>
            <button type="button" class="btn btn-md btn-info btn-floating" data-bs-toggle="modal" data-bs-target="#ModalDownload" title="Download">
                <i class="bi bi-download"></i>
            </button>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">

            <!-- Bungkus Data Responden Pada Form -->
            <form action="javascript:void(0);" id="ProsesUndanganMultiple">
                <div class="table table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td class="text-center">
                                    <small><b>No</b></small>
                                </td>
                                <td><small><b>No.RM</b></small></td>
                                <td><small><b>Pasien/Responden</b></small></td>
                                <td><small><b>Undangan</b></small></td>
                                <td><small><b>Jawaban</b></small></td>
                                <td class="text-center"><small><b>Status</b></small></td>
                                <td class="text-center"><small><b>Opsi</b></small></td>
                            </tr>
                        </thead>
                        <tbody id="tabel_jawaban">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <small>No Data</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>

        </div>
    </div>

    <!-- Pagging -->
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