<div class="modal fade" id="ModalFilter" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="javascript:void(0);" id="ProsesFilter">
                <input type="hidden" name="page" id="page" value="1">
                <div class="modal-header">
                    <h5 class="modal-title text-dark"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="batas">
                                <small>Limit</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="batas" id="batas" class="form-control">
                                <option value="5">5</option>
                                <option selected value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="OrderBy">
                                <small>Dasar Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="OrderBy" id="OrderBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="id_pasien">No Rm</option>
                                <option value="respondent_name">Nama Pasien</option>
                                <option value="datetime_invitation">Datetime Undangan</option>
                                <option value="datetime_answer">Datetime Jawaban</option>
                                <option value="answer">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="ShortBy">
                                <small>Tipe Urutan</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="ShortBy" id="ShortBy" class="form-control">
                                <option value="ASC">A To Z</option>
                                <option selected value="DESC">Z To A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="KeywordBy">
                                <small>Dasar Pencarian</small>
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="keyword_by" id="KeywordBy" class="form-control">
                                <option value="">Pilih</option>
                                <option value="id_pasien">No Rm</option>
                                <option value="respondent_name">Nama Pasien</option>
                                <option value="datetime_invitation">Datetime Undangan</option>
                                <option value="datetime_answer">Datetime Jawaban</option>
                                <option value="answer">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label for="keyword">
                                <small>Kata Kunci</small>
                            </label>
                        </div>
                        <div class="col-8" id="FormFilter">
                            <input type="text" name="keyword" id="keyword" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="bi bi-save"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDownload" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="_Page/Jawaban/ProsesDownload.php" method="POST" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-download me-2"></i> Download Jawaban
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="periode_awal">
                                <small>Periode Awal</small>
                            </label>
                            <input type="date" class="form-control" name="periode_awal" id="periode_awal">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="periode_akhir">
                                <small>Periode Awal</small>
                            </label>
                            <input type="date" class="form-control" name="periode_akhir" id="periode_akhir">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2">
                        <i class="bi bi-download me-2"></i> Download
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-semibold">
                    <i class="bi bi-info-circle me-2"></i> Detail Undangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12" id="FormDetail">
                        <!-- Form Detail Responden -->
                    </div>
                </div>
            </div>
            <div class="modal-footer py-3">
                <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesEdit">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-pencil me-2"></i> Edit Responden
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12" id="FormEdit">
                            <!-- Form Edit Responden -->
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12" id="NotifikasiEdit">
                            <!-- Notifikasi Edit Responden -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2" id="ButtonEdit">
                        <i class="bi bi-save me-2"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalHapus" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesHapus">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-trash me-2"></i> Hapus Undangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-2">
                        <div class="col-12" id="FormHapus">
                            <!-- Form Hapus -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="NotifikasiHapus">
                            <!-- Notifikasi Hapus -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2" id="ButtonHapus">
                        <i class="bi bi-check me-2"></i> Ya, Hapus
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hapus Data Multiple -->
<div class="modal fade" id="ModalHapusMultiple" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesHapusMultiple">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-trash me-2"></i> Hapus Undangan (Multiple)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-2">
                        <div class="col-12" id="FormHapusMultiple">
                            <!-- Form Hapus Responden -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" disabled class="btn btn-primary btn-rounded fw-medium px-4 py-2" id="ButtonHapusMultiple">
                        Lanjutkan <i class="bi bi-chevron-right"></i> 
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notifikasi Hapus Data Multiple -->
<div class="modal fade" id="ModalNotifikasiHapusMultiple" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-semibold">
                    <i class="bi bi-trash me-2"></i> Hapus Undangan (Multiple)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12" id="NotifikasiHapusMultiple">
                        <!-- Notifikasi Hapus Responden -->
                    </div>
                </div>
            </div>
            <div class="modal-footer py-3">
                <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>