<div class="modal fade" id="ModalPertanyaan" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-4 shadow-lg">
           <div class="modal-header">
                <h5 class="modal-title text-dark fw-semibold">
                    <i class="bi bi-list me-2"></i> Daftar Pertanyaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="list_pertanyaan">
                <!-- Daftar Pertanyaan Akan Muncul Disini -->
                <div class="row">
                    <div class="col-12 text-center">
                        No Data
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

<!-- MODAL RINCIAN JAWABAN -->
<div class="modal fade" id="ModalRincianJawaban" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-light fw-semibold">
                    <i class="bi bi-download me-2"></i> Rincian Jawaban
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- FILTER (STICKY) -->
                <div class="p-3 border-bottom bg-white sticky-top" style="z-index: 10;">
                    <form action="javascript:void(0);" id="ProsesFilter">
                        <input type="hidden" name="page" id="page" value="1">
                        <input type="hidden" name="id_survey_question" id="id_survey_question" value="">
                        <input type="hidden" name="data_value" id="data_value" value="">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 mb-2"></div>
                            <div class="col-lg-4 col-md-4 col-sm-8 col-12 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" id="keyword" placeholder="No RM / Nama Responden">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- TABEL (SCROLLABLE) -->
                <div class="p-3" style="height: calc(100vh - 260px); overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="fw-bold">No</th>
                                    <th class="fw-bold">No.RM</th>
                                    <th class="fw-bold">Nama Responden</th>
                                    <th class="fw-bold">Gender</th>
                                    <th class="fw-bold">Email</th>
                                    <th class="fw-bold">No.Kontak</th>
                                    <th class="fw-bold">Tanggal Pengisian</th>
                                    <th class="fw-bold">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody id="tabel_rincian_jawaban">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <small>Tidak Ada Data Yang Ditampilkan</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">

                <!-- PAGINATION -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-info" id="prev_button">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <span class="btn btn-sm btn-outline-info disabled" id="page_info">
                        0 / 0
                    </span>

                    <button type="button" class="btn btn-sm btn-info" id="next_button">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- CLOSE -->
                <button type="button" class="btn btn-md btn-outline-secondary btn-rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="Mo" tabindex="-1">
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
