// DOCUMEN READY FUNCTION
$(document).ready(function () {
    // ================================================================================
    // FUNCTION BLOCK
    // ================================================================================

    //Fungsi Menampilkan Data
    function ShowTable() {

        // Target And Filter
        let target = $('#tabel_pengaturan_umum');
        let data = $('#ProsesFilter').serialize();

        target.addClass('blur-loading');

        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/TabelSettingGeneral.php',
            data: data,
            dataType: 'json',
            success: function (res) {

                if (res.status === "success") {

                    target.fadeOut(150, function () {
                        target.html(res.html).fadeIn(150);
                    });

                    // Update info page
                    $('#page_info').html('Page ' + res.page + ' Of ' + res.total_page);

                    // Handle tombol
                    $('#prev_button').prop('disabled', res.page <= 1);
                    $('#next_button').prop('disabled', res.page >= res.total_page);

                } else {
                    target.html(res.html);
                }

                target.removeClass('blur-loading');
            }
        });
    }

    // ================================================================================
    // EVENT BLOCK
    // ================================================================================

    // Menampilkan Data Pertama Kali

    // Tampilkan data_view dan sembunyikan edit_view
    ShowTable();

    //------------------------------------------------
    // FILTER & PAGGING
    //------------------------------------------------

    // Modal Filter
    $('#ModalFilter').on('shown.bs.modal', function () {
        // Auto Focus
        $('#keyword').trigger('focus');

    });

    //Filter Data
    $('#ProsesFilter').submit(function () {

        // Set Halaman Menjadi 1
        $('#page').val("1");

        // Reload Tabel
        ShowTable();

        // Tutup Modal
        $('#ModalFilter').modal('hide');
    });

    //Reload
    $('#ReloadData').click(function () {

        // Reset Form
        $('#ProsesFilter')[0].reset();

        // Reload Tabel
        ShowTable();
    });

    //Form Keyword By
    $('#KeywordBy').change(function () {

        // Menangkap nilai keyword by
        let KeywordBy = $(this).val();

        // Kirim Ke Form Filter Dengan Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormFilter.php',
            data: { KeywordBy: KeywordBy },
            success: function (response) {
                $('#FormFilter').html(response);
            }
        });

    });

    //Pagging Next
    $(document).on('click', '#next_button', function () {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    //Pagging Previous
    $(document).on('click', '#prev_button', function () {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTable(0);
    });

    //=======================================
    // TAMBAH
    //=======================================

    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {
        // Auto Focus
        $('#app_name').trigger('focus');
    });


    // Handle Proses Tambah
    $('#ProsesTambah').submit(function (e) {
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiTambah').html('');

        // Tombol sUBMIT
        let tombol = $('#ButtonTambah');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form termasuk file
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/ProsesTambah.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                // Jika Berhasil
                if (response.status == 'success') {

                    // Hide Modal
                    $('#ModalTambah').modal('hide');

                    // Reset Notifikasi
                    $('#NotifikasiTambah').html(``);

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
                    $('#ProsesTambah')[0].reset();

                    // Reset Filter
                    $('#ProsesFilter')[0].reset();

                    // Reload Tabel
                    ShowTable();
                } else {

                    // Show Notification Error
                    $('#NotifikasiTambah').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function (xhr, status, error) {
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiTambah').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function () {
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    //------------------------------------------------
    // DETAIL
    //------------------------------------------------

    $('#ModalDetail').on('shown.bs.modal', function (e) {
        $('#FormDetail').html('');

        // Tangkap id_setting_general
        var id_setting_general = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormDetail')
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormDetail.php',
            data: { id_setting_general: id_setting_general },
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormDetail').html(data);
            },
            error: function () {
                $('#FormDetail').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
            }
        });
    });

    // EDIT
    $('#ModalEdit').on('shown.bs.modal', function (e) {

        // Kosongkan Form
        $('#FormEdit').html('');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        // Tangkap id_setting_general
        var id_setting_general = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormEdit')
            .css({
                'filter': 'blur(4px)',
                'opacity': '0.5',
                'transition': 'all 0.3s ease'
            })
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormEdit.php',
            data: { id_setting_general: id_setting_general },
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormEdit').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormEdit').html(data);

                    // Hilangkan blur
                    $('#FormEdit').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormEdit').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormEdit').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });

    // Handle Proses Edit
    $('#ProsesEdit').submit(function (e) {
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiEdit').html('');

        // Tombol sUBMIT
        let tombol = $('#ButtonEdit');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form
        let formData = $(this).serialize();

        // Send To Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/ProsesEdit.php',
            data: formData,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                // Jika Berhasil
                if (response.status == 'success') {

                    // Reset Notifikasi
                    $('#NotifikasiEdit').html(``);

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

                    // Hide Modal
                    $('#ModalEdit').modal('hide');

                    // Reload Tabel
                    ShowTable();
                } else {

                    // Show Notification Error
                    $('#NotifikasiEdit').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function (xhr, status, error) {
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiEdit').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function () {
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    // EDIT APP ICON
    $('#ModalEditAppIcon').on('shown.bs.modal', function (e) {

        // Kosongkan Form Foto
        $('#FormEditAppIcon').html('');

        // Kosongkan Notifikasi
        $('#NotifikasiEditAppIcon').html('');

        // Tangkap id_setting_general
        var id_setting_general = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormEditAppIcon')
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormEditAppIcon.php',
            data: { id_setting_general: id_setting_general },
            success: function (data) {
                $('#FormEditAppIcon').html(data);
            }
        });
    });

    // Handle Proses Edit App Icon
    $('#ProsesEditAppIcon').submit(function (e) {
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiEditAppIcon').html('');

        // Tombol sUBMIT
        let tombol = $('#ButtonEditAppIcon');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/ProsesEditAppIcon.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                // Jika Berhasil
                if (response.status == 'success') {

                    // Reset Notifikasi
                    $('#NotifikasiEditAppIcon').html(``);

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

                    // Hide Modal
                    $('#ModalEditAppIcon').modal('hide');

                    // Reload Tabel
                    ShowTable();
                } else {

                    // Show Notification Error
                    $('#NotifikasiEditAppIcon').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function (xhr, status, error) {
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiEditAppIcon').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function () {
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    // EDIT COMPANY LOGO
    $('#ModalEditCompanyLogo').on('shown.bs.modal', function (e) {

        // Kosongkan Form Foto
        $('#FormEditCompanyLogo').html('');

        // Kosongkan Notifikasi
        $('#NotifikasiEditCompanyLogo').html('');

        // Tangkap id_setting_general
        var id_setting_general = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormEditCompanyLogo')
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormEditCompanyLogo.php',
            data: { id_setting_general: id_setting_general },
            success: function (data) {
                $('#FormEditCompanyLogo').html(data);
            }
        });
    });

    // Handle Proses Edit Company Logo
    $('#ProsesEditCompanyLogo').submit(function (e) {
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiEditCompanyLogo').html('');

        // Tombol sUBMIT
        let tombol = $('#ButtonEditCompanyLogo');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/ProsesEditCompanyLogo.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                // Jika Berhasil
                if (response.status == 'success') {

                    // Reset Notifikasi
                    $('#NotifikasiEditCompanyLogo').html(``);

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

                    // Hide Modal
                    $('#ModalEditCompanyLogo').modal('hide');

                    // Reload Tabel
                    ShowTable();
                } else {

                    // Show Notification Error
                    $('#NotifikasiEditCompanyLogo').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function (xhr, status, error) {
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiEditAppIcon').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function () {
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    // HAPUS
    $('#ModalHapus').on('shown.bs.modal', function (e) {

        // Kosongkan Form 
        $('#FormHapus').html('');

        // Kosongkan Notifikasi
        $('#NotifikasiHapus').html('');

        // Tangkap id_setting_general
        var id_setting_general = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormHapus')
            .css({
                'filter': 'blur(4px)',
                'opacity': '0.5',
                'transition': 'all 0.3s ease'
            })
            .html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div>Loading...</div>
                </div>
            `);
        // View Data With Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/FormHapus.php',
            data: { id_setting_general: id_setting_general },
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormHapus').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormHapus').html(data);

                    // Hilangkan blur
                    $('#FormHapus').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormHapus').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormHapus').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });

    // Handle Proses Hapus
    $('#ProsesHapus').submit(function (e) {
        e.preventDefault();

        // Reset notifikasi
        $('#NotifikasiHapus').html('');

        // Tombol sUBMIT
        let tombol = $('#ButtonHapus');

        // Simpan html asli
        let tombol_asli = tombol.html();

        // Disable tombol
        tombol.prop('disabled', true);

        // Loading button
        tombol.html(`<span class="spinner-border spinner-border-sm"></span>Loading...`);

        // Ambil data form
        let formData = new FormData(this);

        // Send To Ajax
        $.ajax({
            type: 'POST',
            url: '_Page/SettingGeneral/ProsesHapus.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);

                // Jika Berhasil
                if (response.status == 'success') {

                    // Reset Notifikasi
                    $('#NotifikasiHapus').html(``);

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

                    // Hide Modal
                    $('#ModalHapus').modal('hide');

                    // Reload Tabel
                    ShowTable();
                } else {

                    // Show Notification Error
                    $('#NotifikasiHapus').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function (xhr, status, error) {
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiHapus').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function () {
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

});