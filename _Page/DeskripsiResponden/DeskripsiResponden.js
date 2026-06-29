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
    ShowDeskripsiResponden();

    $('#ProsesFilter').submit(function(e) {
        e.preventDefault();
        $('#page').val('1');
        ShowDeskripsiResponden();
        $('#ModalFilter').modal('hide');
    });

    $(document).on('click', '#next_button', function() {
        let page_now = parseInt($('#page').val(), 10);
        $('#page').val(page_now + 1);
        ShowDeskripsiResponden();
    });

    $(document).on('click', '#prev_button', function() {
        let page_now = parseInt($('#page').val(), 10);
        $('#page').val(page_now - 1);
        ShowDeskripsiResponden();
    });

    $(document).on('click', '.reload_data', function() {
        $('#ProsesFilter')[0].reset();
        ShowDeskripsiResponden();
    });
});
