$(document).ready(function() {

    // ------------------------
    // FUNCTION
    // ------------------------

    // DETAIL PROFIL PENGGUNA
    function showDetailProfil(){
        $('#detail_profil').html("Loading...");
        
        // Buka Form Dengan AJAX
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/MyProfile/DetailProfile.php',
            success     : function(data){
                $('#detail_profil').html(data);
            }
        });
    }

    // ------------------------
    // EVENT
    // ------------------------

    // Menampilkan Detail Profile Pertama Kali
    showDetailProfil();

    // EDIT PROFIL
    // Modal Ubah Identitas Profil
    $('#ModalUbahIdentitasProfil').on('show.bs.modal', function () {
        // Reset notifikasi
        $('#NotifikasiUbahIdentitasProfil').html('');
        // Loading form
        $('#FormUbahIdentitasProfil').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary"></div>
                <div class="small mt-2 text-muted">Loading form...</div>
            </div>
        `);
        // Load form
        $.ajax({
            type: 'POST',
            url: '_Page/MyProfile/FormUbahIdentitasProfil.php',
            success: function(data){
                $('#FormUbahIdentitasProfil').html(data);
            },
            error: function(xhr, status, error){
                console.log(xhr.responseText);
                $('#FormUbahIdentitasProfil').html(`
                    <div class="alert alert-danger">
                        Gagal memuat form.
                    </div>
                `);
            }
        });
    });

    // Submit Ubah Profil
    $('#ProsesUbahIdentitasProfil').submit(function(e){
        e.preventDefault();
        // Reset notifikasi
        $('#NotifikasiUbahIdentitasProfil').html('');
        // Ambil tombol
        let tombol = $('#ButtonUbahIdentitasProfil');
        // Simpan text asli
        let tombol_asli = tombol.html();
        // Disable tombol
        tombol.prop('disabled', true);
        // Loading button
        tombol.html(`
            <span class="spinner-border spinner-border-sm"></span>
            Loading...
        `);
        // Ambil data form
        let formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: '_Page/MyProfile/ProsesUbahIdentitasProfil.php',
            data: formData,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if(response.status == 'success'){
                    // Alert success di modal
                    $('#NotifikasiUbahIdentitasProfil').html(`
                        <div class="alert alert-success">
                            ${response.message}
                        </div>
                    `);
                    // Toast SweetAlert
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    setTimeout(function(){
                        // Tutup modal
                        $('#ModalUbahIdentitasProfil').modal('hide');
                        // Reload detail profil
                        showDetailProfil();
                    }, 800);
                }else{
                    $('#NotifikasiUbahIdentitasProfil').html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `);
                    // SweetAlert error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error){
                console.log("XHR:");
                console.log(xhr);
                console.log("STATUS:");
                console.log(status);
                console.log("ERROR:");
                console.log(error);
                console.log("RESPONSE:");
                console.log(xhr.responseText);
                $('#NotifikasiUbahIdentitasProfil').html(`
                    <div class="alert alert-danger">
                        Terjadi kesalahan server.
                    </div>
                `);
                // SweetAlert error server
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server'
                });
            },
            complete: function(){
                // Enable tombol kembali
                tombol.prop('disabled', false);
                // Kembalikan text tombol
                tombol.html(tombol_asli);
            }
        });
    });

    // UBAH FOTO PROFIL
    // Modal Ubah Foto Profil
    $('#ModalUbahFotoProfil').on('show.bs.modal', function () {
        
        // Reset notifikasi
        $('#NotifikasiUbahFotoProfil').html('');
        
        // Loading form
        $('#FormUbahFotoProfil').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary"></div>
                <div class="small mt-2 text-muted">Loading form...</div>
            </div>
        `);
        
        // Load form
        $.ajax({
            type: 'POST',
            url: '_Page/MyProfile/FormUbahFotoProfil.php',
            success: function(data){
                $('#FormUbahFotoProfil').html(data);
            },
            error: function(xhr, status, error){
                console.log(xhr.responseText);
                $('#FormUbahFotoProfil').html(`
                    <div class="alert alert-danger">
                        Gagal memuat form.
                    </div>
                `);
            }
        });
    });

    // Submit Ubah Foto Profil
    $('#ProsesUbahFotoProfil').submit(function(e){

        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiUbahFotoProfil').html('');

        // Tombol submit
        let tombol = $('#ButtonUbahFotoProfil');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`
            <span class="spinner-border spinner-border-sm"></span>
            Uploading...
        `);

        // FormData WAJIB untuk upload file
        let formData = new FormData(this);

        $.ajax({

            type: 'POST',

            url: '_Page/MyProfile/ProsesUbahFotoProfil.php',

            data: formData,

            dataType: 'JSON',

            processData: false,

            contentType: false,

            cache: false,

            success: function(response){

                console.log(response);

                if(response.status == 'success'){

                    $('#NotifikasiUbahFotoProfil').html(`
                        <div class="alert alert-success">
                            ${response.message}
                        </div>
                    `);

                    // Toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    setTimeout(function(){

                        // Tutup modal
                        $('#ModalUbahFotoProfil').modal('hide');

                        // Reload detail profil
                        showDetailProfil();

                    }, 800);

                }else{

                    $('#NotifikasiUbahFotoProfil').html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `);

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            },

            error: function(xhr, status, error){

                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                $('#NotifikasiUbahFotoProfil').html(`
                    <div class="alert alert-danger">
                        Terjadi kesalahan server.
                    </div>
                `);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server'
                });
            },

            complete: function(){

                tombol.prop('disabled', false);

                tombol.html(tombol_asli);
            }
        });
    });

    // UBAH PASSWORD
    // Modal Ubah pASSWORD
    $('#ModalUbahPasswordProfil').on('show.bs.modal', function () {
        
        // Reset notifikasi
        $('#NotifikasiUbahPasswordProfil').html('');
        
        // Loading form
        $('#FormUbahPasswordProfil').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary"></div>
                <div class="small mt-2 text-muted">Loading form...</div>
            </div>
        `);
        
        // Load form
        $.ajax({
            type: 'POST',
            url: '_Page/MyProfile/FormUbahPasswordProfil.php',
            success: function(data){
                $('#FormUbahPasswordProfil').html(data);
            },
            error: function(xhr, status, error){
                console.log(xhr.responseText);
                $('#FormUbahPasswordProfil').html(`
                    <div class="alert alert-danger">
                        Gagal memuat form.
                    </div>
                `);
            }
        });
    });

    // Submit Ubah Password
    $('#ProsesUbahPasswordProfil').submit(function(e){

        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiUbahPasswordProfil').html('');

        // Tombol submit
        let tombol = $('#ButtonUbahPasswordProfil');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`
            <span class="spinner-border spinner-border-sm"></span>
            Loading...
        `);

        // Ambil data form
        let formData = $(this).serialize();

        $.ajax({

            type: 'POST',

            url: '_Page/MyProfile/ProsesUbahPasswordProfil.php',

            data: formData,

            dataType: 'JSON',

            success: function(response){

                console.log(response);

                if(response.status == 'success'){

                    // Notifikasi bootstrap
                    $('#NotifikasiUbahPasswordProfil').html(`
                        <div class="alert alert-success">
                            ${response.message}
                        </div>
                    `);

                    // Toast SweetAlert
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Reset form
                    $('#ProsesUbahPasswordProfil')[0].reset();

                    setTimeout(function(){

                        // Tutup modal
                        $('#ModalUbahPasswordProfil').modal('hide');

                    }, 800);

                }else{

                    $('#NotifikasiUbahPasswordProfil').html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `);

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            },

            error: function(xhr, status, error){

                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                $('#NotifikasiUbahPasswordProfil').html(`
                    <div class="alert alert-danger">
                        Terjadi kesalahan server.
                    </div>
                `);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server'
                });
            },

            complete: function(){

                tombol.prop('disabled', false);

                tombol.html(tombol_asli);
            }
        });
    });




});
