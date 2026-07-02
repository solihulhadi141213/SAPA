function ShowDaftarPertanyaan() {
    let target = $('#list_pertanyaan');

    target.html('<div class="row"><div class="col-12 text-center">Loading...</div></div>');
    target.addClass('blur-loading');
    $.ajax({
        type: 'POST',
        url: '_Page/DeskripsiPertanyaan/TabelPertanyaan.php',
        success: function(data) {
            target.html(data);
        }
    });
}

function ShowRincianResponden() {
    // Tangkap Data Filter
    let ProsesFilter   = $('#ProsesFilter').serialize();

    // Loading
    $('#tabel_rincian_jawaban').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

    $.ajax({
        type: 'POST',
        url: '_Page/DeskripsiPertanyaan/tabel_rincian_jawaban.php',
        data: ProsesFilter,
        beforeSend: function () {
            $('#tabel_rincian_jawaban').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
        },
        success: function (response) {
            $('#tabel_rincian_jawaban').html(response);
        },
        error: function () {
            $('#tabel_rincian_jawaban').html(
                '<tr><td colspan="8" class="text-center text-danger">Terjadi Kesalahan Pada Saat Memuat Data</td></tr>'
            );
        }
    });
}

$(document).ready(function() {

    // Tampilkan Modal Pertanyaan
    $('#ModalPertanyaan').modal('show');

    // Tampilkan Data
    ShowDaftarPertanyaan();
    
    // Ketika pilih_pertanyaan diklik
    $(document).on('click', '.pilih_pertanyaan', function (e) {
        e.preventDefault();

        // Ambil id dari elemen yang diklik
        let id_survey_question = $(this).data('id');

        // Hide Modal
        $('#ModalPertanyaan').modal('hide');

        // Loading
        $('#deskripsi_pertanyaan').html('Loading...');

        $.ajax({
            type: 'POST',
            url: '_Page/DeskripsiPertanyaan/DetailPertanyaan.php',
            data: {
                id_survey_question: id_survey_question
            },
            beforeSend: function () {
                $('#deskripsi_pertanyaan').html('Loading...');
            },
            success: function (response) {
                $('#deskripsi_pertanyaan').html(response);
            },
            error: function () {
                $('#deskripsi_pertanyaan').html(
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>'
                );
            }
        });
    });

    // Ketika rincian_jawaban diklik
    $(document).on('click', '.rincian_jawaban', function (e) {
        e.preventDefault();

        // Ambil id dari elemen yang diklik
        let id_survey_question = $(this).data('id');
        let value              = $(this).data('value');

        // Tempelkan Ke Filter
        $('#page').val('1');
        $('#id_survey_question').val(id_survey_question);
        $('#data_value').val(value);

        // Hide Modal
        $('#ModalRincianJawaban').modal('show');

        // Load Data
        ShowRincianResponden();
        
    });

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(e){
        e.preventDefault();
        $('#page').val("1");
        ShowRincianResponden(0);
    });

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowRincianResponden(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowRincianResponden(0);
    });


});
