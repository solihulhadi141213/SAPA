function ShowDeskripsiResponden() {
    let target = $('#tabel_deskripsi_responden');
    let data = $('#ProsesFilter').serialize();

    target.addClass('blur-loading');
    $.ajax({
        type: 'POST',
        url: '_Page/DeskripsiResponden/TabelDeskripsiResponden.php',
        data: data,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                target.fadeOut(150, function () {
                    target.html(res.html).fadeIn(150);
                });
                $('#page_info').html('Page ' + res.page + ' Of ' + res.total_page);
                $('#prev_button').prop('disabled', res.page <= 1);
                $('#next_button').prop('disabled', res.page >= res.total_page);
            } else {
                target.html(res.html);
            }
            target.removeClass('blur-loading');
        }
    });
}

$(document).ready(function() {

    // Tampilkan Modal Periode
    $('#ModalPeriode').modal('show');

    // Ketika ProsesFilter Di Submit
    $('#ProsesFilter').on('submit', function (e) {
        e.preventDefault();

        // Ambil periode
        let periode_awal  = $('#periode_awal').val();
        let periode_akhir = $('#periode_akhir').val();

        // Loading
        $('#show_report').html('Loading...');

        $.ajax({
            type: 'POST',
            url: '_Page/DeskripsiResponden/TabelDeskripsiResponden.php',
            data: {
                periode_awal: periode_awal,
                periode_akhir: periode_akhir
            },
            success: function (response) {
                $('#show_report').html(response);
                $('#ModalPeriode').modal('hide');
            },
            error: function (xhr, status, error) {
                $('#show_report').html(
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>'
                );
                console.error(error);
            }
        });
    });
});
