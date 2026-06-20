<!-- Modal Logout -->
<div class="modal fade" id="ModalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <button type="button" 
                        class="btn-close shadow-none" 
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center pt-2 pb-3">

                <!-- Icon -->
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle"
                         style="width:80px;height:80px;">

                        <i class="bi bi-power text-danger"
                           style="font-size:2rem;"></i>
                    </div>
                </div>

                <!-- Text -->
                <h6 class="fw-bold mb-1">
                    Yakin ingin keluar?
                </h6>

                <p class="text-muted small mb-0">
                    Sesi login Anda akan diakhiri dan Anda perlu login kembali untuk mengakses aplikasi.
                </p>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 pt-0 justify-content-center">

                <a href="_Page/Logout/ProsesLogout.php"
                   class="btn btn-danger px-4">

                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>

                 <button type="button"
                        class="btn btn-light border px-4"
                        data-bs-dismiss="modal">

                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>

            </div>

        </div>
    </div>
</div>