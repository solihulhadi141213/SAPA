//Fungsi Menampilkan Data
function ShowConnectionTable() {
    $('#tabel_whatsapp_gateway').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
    $.ajax({
        type    : 'POST',
        url     : '_Page/WahtsappGateway/TabelWahtsappGateway.php',
        success: function(data) {
            $('#tabel_whatsapp_gateway').html(data);
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

// Fungsi Menampilkan Detail
function ShowDetail(id_setting_wa){
    //Form Loading
    $('#FormDetail').html('Loading...');

    //Tampilkan Form Dengan Ajax
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/WahtsappGateway/FormDetail.php',
        data        : {id_setting_wa: id_setting_wa},
        success     : function(data){
            $('#FormDetail').html(data);
        }
    });
}


//Menampilkan Data Pertama Kali
$(document).ready(function() {
    ShowConnectionTable();

    /*  
    ---------------------------------------------------
    TAMBAH KONEKSI
    --------------------------------------------------- 
    */

    /* Ketika 'modal_tambah' di click */
    $(document).on('click', '.modal_tambah', function(){
        $('#ModalTambah').modal('show');
    });

    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {
        // Auto Focus
        $('#url_service').trigger('focus');
    });

    /* Ketika 'ProsesTambah' disubmit */
    $('#ProsesTambah').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesTambah=$('#ProsesTambah').serialize();

        /* Loading Notification */
        $('#NotifikasiTambah').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/WahtsappGateway/ProsesTambah.php',
            dataType: 'json',
            data    : ProsesTambah,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiTambah').html('');

                    //reset form
                    $('#ProsesTambah')[0].reset();

                    //Tutup modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Koneksi Whatsapp Gateway Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowConnectionTable();
                }else{
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DETAIL */
    $(document).on('click', '.modal_detail', function () {

        //tangkap data 'id_setting_wa' dan buat variabel
        var id_setting_wa   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        // Show Detail
        ShowDetail(id_setting_wa);
        
    });

    /* Ketika 'ProsesDetail' disubmit */
    $('#ProsesDetail').submit(function(){
       
        //tangkap data 'id_setting_wa' dan buat variabel
        var id_setting_wa   = $('#put_id_setting_wa').val();

        // Show Detail
        ShowDetail(id_setting_wa);
    });

    // Ketika "button_disconnect" di click
    $(document).on('click', '.button_disconnect', function () {

        //tangkap data 'id_setting_wa' dan buat variabel
        var id_setting_wa   = $(this).data('id');

        // Ambil Element Button
        let button_element = $('.button_disconnect').html();

        // Loading Button
        $('.button_disconnect').html('Menghapus Perangkat ...');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/WahtsappGateway/ProsesDisconnect.php',
            data    : {id_setting_wa : id_setting_wa},
            success: function(response) {
                
                let status = response.status;
                var message = response.message;
                if(status=='success'){
                    ShowDetail(id_setting_wa);
                }else{
                    $('.button_disconnect').html(button_element);
                    $('.notifikasi_disconnection').html('<div class="alert alert-danger text-center">'+message+'</div>');
                }
            }
        });
        
    });

    /* MODAL EDIT */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_setting_wa' dan buat variabel
        var id_setting_wa   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/WahtsappGateway/FormEdit.php',
            data        : {id_setting_wa: id_setting_wa},
            success     : function(data){
                $('#FormEdit').html(data);
            }
        });
    });

    /* Ketika 'ProsesEdit' disubmit */
    $('#ProsesEdit').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesEdit=$('#ProsesEdit').serialize();

        /* Loading Notification */
        $('#NotifikasiEdit').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/WahtsappGateway/ProsesEdit.php',
            dataType: 'json',
            data    : ProsesEdit,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiEdit').html('');

                    //Tutup modal
                    $('#ModalEdit').modal('hide');

                    //reload tabel
                    ShowConnectionTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Edit Koneksi Whatsapp Gateway Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DELETE */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_setting_wa' dan buat variabel
        var id_setting_wa   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/WahtsappGateway/FormDelete.php',
            data        : {id_setting_wa: id_setting_wa},
            success     : function(data){
                $('#FormDelete').html(data);
            }
        });
    });

    /* Ketika 'ProsesDelete' disubmit */
    $('#ProsesDelete').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesDelete=$('#ProsesDelete').serialize();

        /* Loading Notification */
        $('#NotifikasiDelete').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/WahtsappGateway/ProsesDelete.php',
            dataType: 'json',
            data    : ProsesDelete,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiDelete').html('');

                    //Tutup modal
                    $('#ModalDelete').modal('hide');

                    //reload tabel
                    ShowConnectionTable();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Koneksi Whatsapp Gateway Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

});