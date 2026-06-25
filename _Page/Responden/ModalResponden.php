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
                                <option value="respondent_sex">Gender</option>
                                <option value="respondent_brithdate">Tanggal Lahir</option>
                                <option value="no_kontak">No.Kontak</option>
                                <option value="tanggal_kunjungan">Tanggal Kunjungan</option>
                                <option value="kunjungan_tujuan">Tujuan</option>
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
                                <option value="respondent_sex">Gender</option>
                                <option value="respondent_brithdate">Tanggal Lahir</option>
                                <option value="no_kontak">No.Kontak</option>
                                <option value="tanggal_kunjungan">Tanggal Kunjungan</option>
                                <option value="kunjungan_tujuan">Tujuan</option>
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

<div class="modal fade" id="ModalKunjungan" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light">
                    <i class="bi bi-search"></i> Pilih Kunjungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">

                <!-- FILTER (STICKY) -->
                <div class="p-3 border-bottom bg-white sticky-top" style="z-index: 10;">
                    <form action="javascript:void(0);" id="ProsesFilterKunjungan">
                        <input type="hidden" name="page" id="page_kunjungan" value="1">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2"></div>
                            <div class="col-lg-4 col-md-4 col-sm-8 col-12 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" id="keyword_kunjungan" placeholder="No RM / Nama pasien">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-12 mb-2">
                                <button type="button" class="btn btn-primary w-100 modal_tambah_responden" data-id="">
                                    <i class="bi bi-plus"></i> Manual
                                </button>
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
                                    <th class="fw-bold">Nama Pasien</th>
                                    <th class="fw-bold">Tanggal</th>
                                    <th class="fw-bold">Tujuan</th>
                                    <th class="fw-bold">No.Kontak</th>
                                    <th class="fw-bold">Status</th>
                                    <th class="fw-bold">Opsi</th>
                                </tr>
                            </thead>
                            <tbody id="TabelKunjungan">
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
                    <button type="button" class="btn btn-sm btn-info" id="prev_button_kunjungan">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <span class="btn btn-sm btn-outline-info disabled" id="page_info_kunjungan">
                        0 / 0
                    </span>

                    <button type="button" class="btn btn-sm btn-info" id="next_button_kunjungan">
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

<div class="modal fade" id="ModalTambahResponden" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <form action="javascript:void(0);" id="ProsesTambahResponden">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-semibold">
                        <i class="bi bi-plus me-2"></i> Tambah Responden
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12" id="FormTambahResponden">
                            <!-- Form Tambah Responden -->
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12" id="NotifikasiTambahResponden">
                            <!-- Notifikasi Tambah Responden -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2" id="ButtonTambahResponden">
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


<div class="modal fade" id="ModalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-semibold">
                    <i class="bi bi-info-circle me-2"></i> Detail Responden
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
                        <i class="bi bi-trash me-2"></i> Hapus Responden
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-2">
                        <div class="col-12" id="FormHapus">
                            <!-- Form Hapus Responden -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="NotifikasiHapus">
                            <!-- Notifikasi Hapus Responden -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="submit" class="btn btn-primary btn-rounded fw-medium px-4 py-2" id="ButtonHapus">
                        <i class="bi bi-check me-2"></i> Hapus
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-rounded fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>