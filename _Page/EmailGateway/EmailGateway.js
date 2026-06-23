//Fungsi Menampilkan Data
function ShowConnectionTable() {
    $.ajax({
        type    : 'POST',
        url     : '_Page/EmailGateway/TabelEmailGateway.php',
        success: function(data) {
            $('#tabel_email_gateway').html(data);
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
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
        $('#email_gateway').trigger('focus');
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
            url     : '_Page/EmailGateway/ProsesTambah.php',
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
                        'Tambah Koneksi SIMRS Berhasil!',
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

        //tangkap data 'id_setting_email_gateway' dan buat variabel
        var id_setting_email_gateway   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Form Loading
        $('#FormDetail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/EmailGateway/FormDetail.php',
            data        : {id_setting_email_gateway: id_setting_email_gateway},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    /* MODAL KIRIM EMAIL */
    $(document).on('click', '.modal_kirim_email', function () {

        //tangkap data 'id_setting_email_gateway' dan buat variabel
        var id_setting_email_gateway   = $(this).data('id');

        //tampilkan modal
        $('#ModalKirimEmail').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiKirimEmail').html('');

        //Form Loading
        $('#FormKirimEmail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/EmailGateway/FormKirimEmail.php',
            data        : {id_setting_email_gateway: id_setting_email_gateway},
            success     : function(data){
                $('#FormKirimEmail').html(data);
            }
        });
    });

    /* Ketika 'ModalKirimEmail' disubmit */
    $('#ProsesKirimEmail').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesKirimEmail=$('#ProsesKirimEmail').serialize();

        /* Loading Notification */
        $('#NotifikasiKirimEmail').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/EmailGateway/ProsesKirimEmail.php',
            data    : ProsesKirimEmail,
            success: function(response) {
                $('#NotifikasiKirimEmail').html(response);
            }
        });
    });

    /* MODAL EDIT */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_setting_email_gateway' dan buat variabel
        var id_setting_email_gateway   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/EmailGateway/FormEdit.php',
            data        : {id_setting_email_gateway: id_setting_email_gateway},
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
            url     : '_Page/EmailGateway/ProsesEdit.php',
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
                        'Edit Koneksi SIMRS Berhasil!',
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

        //tangkap data 'id_setting_email_gateway' dan buat variabel
        var id_setting_email_gateway   = $(this).data('id');

        //tampilkan modal
        $('#ModalDelete').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiDelete').html('');

        //Form Loading
        $('#FormDelete').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/EmailGateway/FormDelete.php',
            data        : {id_setting_email_gateway: id_setting_email_gateway},
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
            url     : '_Page/EmailGateway/ProsesDelete.php',
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
                        'Hapus Koneksi SIMRS Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiDelete').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

});