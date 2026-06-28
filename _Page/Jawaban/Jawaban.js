// ======================================================
// FUNCTION
// ======================================================

// Menampilkan Tabel Undangan
function ShowJawaban() {
    // Target And Filter
    let target = $('#tabel_jawaban');
    let data   = $('#ProsesFilter').serialize();

    target.addClass('blur-loading');

    $.ajax({
        type: 'POST',
        url: '_Page/Jawaban/TabelJawaban.php',
        data: data,
        dataType: 'json',
        success: function(res) {

            if(res.status === "success"){

                target.fadeOut(150, function () {
                    target.html(res.html).fadeIn(150);
                });

                $('input[name="check_all"]').prop('checked', false);

                // Update info page
                $('#page_info').html('Page ' + res.page + ' Of ' + res.total_page);

                // Handle tombol
                $('#prev_button').prop('disabled', res.page <= 1);
                $('#next_button').prop('disabled', res.page >= res.total_page);

            }else{
                target.html(res.html);
            }

            target.removeClass('blur-loading');
        }
    });
}


// ======================================================
// DOCUMENT READY
// ======================================================

$(document).ready(function() {
    // Menampilkan data responden pertama kali
    ShowJawaban();

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Jawaban/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(e){
        e.preventDefault();
        $('#page').val("1");
        ShowJawaban(0);
        $('#ModalFilter').modal('hide');
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowJawaban(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowJawaban(0);
    });

    // Reload
    $(document).on('click', '.reload_data', function() {
        // Reset Filter
        $('#ProsesFilter')[0].reset();

        // Tampilkan Ulang Data
        ShowJawaban();
    });

    //------------------------------------------------
    // DETAIL
    //------------------------------------------------
    
    $('#ModalDetail').on('shown.bs.modal', function (e) {
        $('#FormDetail').html('');

        // Tangkap id_respondent
        var id_respondent = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormDetail')
            .css({
                'filter'    : 'blur(4px)',
                'opacity'   : '0.5',
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
            type   : 'POST',
            url    : '_Page/Jawaban/FormDetail.php',
            data   : {id_respondent: id_respondent},
            success: function (data) {
                // Fade out kecil sebelum ganti content
                $('#FormDetail').css('opacity', '0');
                setTimeout(function () {
                    // Ganti isi
                    $('#FormDetail').html(data);

                    // Hilangkan blur
                    $('#FormDetail').css({
                        'filter': 'blur(0px)',
                        'opacity': '1'
                    });
                }, 200);
            },
            error: function () {
                $('#FormDetail').html(`
                    <div class="alert alert-danger mb-0">
                        Terjadi kesalahan saat memuat data
                    </div>
                `);
                $('#FormDetail').css({
                    'filter': 'blur(0px)',
                    'opacity': '1'
                });
            }
        });
    });

    // EDIT
    $('#ModalEdit').on('shown.bs.modal', function (e) {

        // Kosongkan Form
        $('#FormEdit').html('');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        // Tangkap id_respondent
        var id_respondent = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormEdit')
            .css({
                'filter'    : 'blur(4px)',
                'opacity'   : '0.5',
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
            type   : 'POST',
            url    : '_Page/Jawaban/FormEdit.php',
            data   : {id_respondent: id_respondent},
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
    $('#ProsesEdit').submit(function(e){
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
            type    : 'POST',
            url     : '_Page/Jawaban/ProsesEdit.php',
            data    : formData,
            dataType: 'JSON',
            success : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiEdit').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true,
                        didOpen          : (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalEdit').modal('hide');

                    // Reload Tabel
                    ShowJawaban();
                }else{

                    // Show Notification Error
                    $('#NotifikasiEdit').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiEdit').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
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

        // Tangkap id_respondent
        var id_respondent = $(e.relatedTarget).data('id');

        // Efek transisi loading tanpa CSS tambahan
        $('#FormHapus')
            .css({
                'filter'    : 'blur(4px)',
                'opacity'   : '0.5',
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
            type   : 'POST',
            url    : '_Page/Jawaban/FormHapus.php',
            data   : {id_respondent: id_respondent},
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
    $('#ProsesHapus').submit(function(e){
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
            type       : 'POST',
            url        : '_Page/Jawaban/ProsesHapus.php',
            data       : formData,
            processData: false,
            contentType: false,
            dataType   : 'JSON',
            success    : function(response){
                console.log(response);

                // Jika Berhasil
                if(response.status == 'success'){

                    // Reset Notifikasi
                    $('#NotifikasiHapus').html(``);

                    // Toast SweetAlert
                    Swal.fire({
                        toast            : true,
                        position         : 'top-end',
                        icon             : 'success',
                        title            : response.message,
                        showConfirmButton: false,
                        timer            : 3000,
                        timerProgressBar : true,
                        didOpen          : (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    // Hide Modal
                    $('#ModalHapus').modal('hide');

                    // Reload Tabel
                    ShowJawaban();
                }else{

                    // Show Notification Error
                    $('#NotifikasiHapus').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },

            error: function(xhr, status, error){
                // Consol
                console.log("XHR:", xhr);
                console.log("STATUS:", status);
                console.log("ERROR:", error);
                console.log("RESPONSE:", xhr.responseText);

                // Tampilkan Notifikasi
                $('#NotifikasiHapus').html(`<div class="alert alert-danger">Terjadi kesalahan server.</div>`);
            },

            complete: function(){
                // Kembalikan Tombol
                tombol.prop('disabled', false);
                tombol.html(tombol_asli);
            }
        });
    });

    // HAPUS MULTIPLE
    $('#ModalHapusMultiple').on('shown.bs.modal', function (e) {

        // Kosongkan Form 
        $('#FormHapusMultiple').html('Loading...');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusMultiple').html('');

        // Disable Button
       $('#ButtonHapusMultiple').prop('disabled', true);

        // Tangkap Data
        var ProsesUndanganMultiple = $('#ProsesUndanganMultiple').serialize();

        // Efek transisi loading tanpa CSS tambahan
        
        // View Data With Ajax
        $.ajax({
            type   : 'POST',
            url    : '_Page/Jawaban/FormHapusMultiple.php',
            data   : ProsesUndanganMultiple,
            success: function (data) {
                $('#FormHapusMultiple').html(data);
            }
        });
    });

    // Handle Proses Hapus (Multiple)
    $('#ProsesHapusMultiple').submit(function(e){
        e.preventDefault();

        // Tangkap Data
        var ProsesHapusMultiple = $('#ProsesHapusMultiple').serialize();

        // Loading Notifikasi
        $('#NotifikasiHapusMultiple').html('Loading...');
        
        // Tutup Modal Form
        $('#ModalHapusMultiple').modal('hide');

        // Tampilkan Modal Notifikasi
        $('#ModalNotifikasiHapusMultiple').modal('show');

        // Send To Ajax
        $.ajax({
            type       : 'POST',
            url        : '_Page/Jawaban/ProsesHapusMultiple.php',
            data       : ProsesHapusMultiple,
            success    : function(response){
                $('#NotifikasiHapusMultiple').html(response);
                ShowJawaban();
            }
        });
    });

    

});
