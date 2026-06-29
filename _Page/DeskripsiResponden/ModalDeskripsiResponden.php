<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <input type="hidden" name="batas" id="batas" value="10">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-funnel me-2"></i> Filter Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="periode_awal"><small>Periode Awal</small></label>
                        </div>
                        <div class="col-8">
                            <input type="date" name="periode_awal" id="periode_awal" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="periode_akhir"><small>Periode Akhir</small></label>
                        </div>
                        <div class="col-8">
                            <input type="date" name="periode_akhir" id="periode_akhir" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="OrderBy"><small>Dasar Urutan</small></label>
                        </div>
                        <div class="col-8">
                            <select name="OrderBy" id="OrderBy" class="form-control">
                                <option value="datetime_invitation">Tanggal Undangan</option>
                                <option value="id_pasien">No Rm</option>
                                <option value="respondent_name">Nama Responden</option>
                                <option value="method_invitation">Metode</option>
                                <option value="no_wa">No WA</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="ShortBy"><small>Tipe Urutan</small></label>
                        </div>
                        <div class="col-8">
                            <select name="ShortBy" id="ShortBy" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="keyword"><small>Kata Kunci</small></label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2">
                        <i class="bi bi-search me-2"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDownload" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="_Page/DeskripsiResponden/ProsesDownload.php" method="POST" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-download me-2"></i> Export Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="download_periode_awal"><small>Periode Awal</small></label>
                            <input type="date" class="form-control" name="periode_awal" id="download_periode_awal">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="download_periode_akhir"><small>Periode Akhir</small></label>
                            <input type="date" class="form-control" name="periode_akhir" id="download_periode_akhir">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2">
                        <i class="bi bi-file-earmark-excel me-2"></i> Download
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
