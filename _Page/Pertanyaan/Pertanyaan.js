//------------------------------------------------
// FUNCTION
//------------------------------------------------
//Fungsi Menampilkan Data
function ShowDaftarPertanyaan() {
    let sampah = $('.tampilan_sampah').data('sampah');
    $.ajax({
        type    : 'POST',
        url     : '_Page/Pertanyaan/DaftarPertanyaan.php',
        data    : {sampah: sampah},
        success: function(data) {
            $('#daftar_pertanyaan').html(data);
            
            // 🔁 Re-inisialisasi tooltip setelah data dimuat
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function UpdateTampilanSampahButton(isTrashMode) {
    const tombol = $('.tampilan_sampah');

    if (isTrashMode === true || isTrashMode === 'true') {
        tombol
            .removeClass('bg-danger-subtle text-danger')
            .addClass('bg-success-subtle success-dark');

        tombol.find('small').html('<i class="bi bi-eye"></i> Sampah (aktif)');
    } else {
        tombol
            .removeClass('bg-success-subtle text-success')
            .addClass('bg-danger-subtle text-danger');

        tombol.find('small').html('<i class="bi bi-eye-slash"></i> Sampah (0)');
    }
}


//------------------------------------------------
// HANDDLE
//------------------------------------------------

//Menampilkan Data Pertama Kali
$(document).ready(function() {

    ShowDaftarPertanyaan();

    UpdateTampilanSampahButton($('.tampilan_sampah').data('sampah'));

    // Sembunyikan Form alternatif untuk tambah dan edit
    $('#form_alternative_answers').hide();
    $('#form_alternative_answers_edit').hide();

    // Jika question_type adalah coded
    $(document).on('change', '#question_type', function(){
        if($(this).val() == 'coded'){
            $('#form_alternative_answers').slideDown(200);
        }else{
            $('#form_alternative_answers').slideUp(200);
        }
    });

    $(document).on('change', '#question_type_edit', function(){
        if($(this).val() == 'coded'){
            $('#form_alternative_answers_edit').slideDown(200);
        }else{
            $('#form_alternative_answers_edit').slideUp(200);
        }
    });

    // Tambah Alternatif
    $(document).on('click', '.tambah_alternatif', function(){
        var html = `
            <div class="row mb-2 item_alternatif">
                <div class="col-12">
                    <div class="input-group">

                        <input type="text"
                            name="alternatif_label[]"
                            class="form-control"
                            placeholder="Label">

                        <input type="text"
                            name="alternatif_value[]"
                            class="form-control"
                            placeholder="Value">

                        <button
                            type="button"
                            class="btn btn-danger hapus_alternatif">
                            <i class="bi bi-x"></i>
                        </button>

                    </div>
                </div>
            </div>
        `;

        $('#list_alternatif').append(html);

    });

    // Tambah Alternatif Edit
    $(document).on('click', '.tambah_alternatif_edit', function(){
        var html = `
            <div class="row mb-2 item_alternatif">
                <div class="col-12">
                    <div class="input-group">

                        <input type="text"
                            name="alternatif_label[]"
                            class="form-control"
                            placeholder="Label">

                        <input type="text"
                            name="alternatif_value[]"
                            class="form-control"
                            placeholder="Value">

                        <button
                            type="button"
                            class="btn btn-danger hapus_alternatif_edit">
                            <i class="bi bi-x"></i>
                        </button>

                    </div>
                </div>
            </div>
        `;

        $('#list_alternatif_edit').append(html);
    });

    // Hapus Alternatif
    $(document).on('click', '.hapus_alternatif', function(){

        // jangan sampai semua alternatif habis
        if($('.item_alternatif').length == 1){

            $(this)
                .closest('.item_alternatif')
                .find('input')
                .val('');

            return;
        }

        $(this)
            .closest('.item_alternatif')
            .remove();

    });

    // Hapus Alternatif Edit
    $(document).on('click', '.hapus_alternatif_edit', function(){
        if($('#list_alternatif_edit .item_alternatif').length == 1){
            $(this)
                .closest('.item_alternatif')
                .find('input')
                .val('');

            return;
        }

        $(this)
            .closest('.item_alternatif')
            .remove();
    });

    // Modal Tambah
    $('#ModalTambah').on('shown.bs.modal', function () {
        // Auto Focus
        $('#question_type').trigger('focus');
    });

    // Toggle tampilan sampah
    $(document).on('click', '.tampilan_sampah', function () {
        const tombol = $(this);
        const current = tombol.data('sampah');
        const next = !(current === true || current === 'true');

        tombol.attr('data-sampah', next ? 'true' : 'false');
        tombol.data('sampah', next);

        UpdateTampilanSampahButton(next);
        ShowDaftarPertanyaan();
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
            url     : '_Page/Pertanyaan/ProsesTambah.php',
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

                    $('#form_alternative_answers').hide();

                    $('#list_alternatif').html(`
                        <div class="row mb-2 item_alternatif">
                            <div class="col-12">
                                <div class="input-group">

                                    <input type="text"
                                        name="alternatif_label[]"
                                        class="form-control"
                                        placeholder="Label">

                                    <input type="text"
                                        name="alternatif_value[]"
                                        class="form-control"
                                        placeholder="Value">

                                    <button
                                        type="button"
                                        class="btn btn-danger hapus_alternatif">
                                        <i class="bi bi-x"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    `);

                    //Tutup modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Pertanyaan Berhasil!',
                        'success'
                    )

                    //reload tabel
                    ShowDaftarPertanyaan();
                }else{
                    $('#NotifikasiTambah').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* UBAH POSISI DATA */
    $(document).on('click', '.ubah_posisi', function () {

        //tangkap data 'id_survey_question' dan buat variabel
        var id_survey_question   = $(this).data('id');
        var posisi   = $(this).data('posisi');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pertanyaan/ProsesUbahPosisi.php',
            dataType: 'json',
            data    : {id_survey_question: id_survey_question, posisi: posisi},
            success : function(response){
                let status = response.status;
                let message = response.message;

                // Jika Berhasil
                if(status=='success'){
                    //reload tabel
                    ShowDaftarPertanyaan();
                }else{
                    // Jika gagal tampilkan dalam bentuk toast 
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {

                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                }
            }
        });
    });

    /* MODAL DETAIL */
    $(document).on('click', '.modal_detail', function () {

        //tangkap data 'id_survey_question' dan buat variabel
        var id_survey_question   = $(this).data('id');

        //tampilkan modal
        $('#ModalDetail').modal('show');

        //Form Loading
        $('#FormDetail').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pertanyaan/FormDetail.php',
            data        : {id_survey_question: id_survey_question},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    /* MODAL EDIT */
    $(document).on('click', '.modal_edit', function () {

        //tangkap data 'id_survey_question' dan buat variabel
        var id_survey_question   = $(this).data('id');

        //tampilkan modal
        $('#ModalEdit').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiEdit').html('');

        //Form Loading
        $('#FormEdit').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pertanyaan/FormEdit.php',
            data        : {id_survey_question: id_survey_question},
            success     : function(data){
                $('#FormEdit').html(data);
                $('#question_type_edit').trigger('change');
            }
        });
    });

    /* Ketika 'ProsesEdit' disubmit */
    $(document).on('submit', '#ProsesEdit', function(){
       
        /* Menangkap data dari form  */
        var ProsesEdit=$('#ProsesEdit').serialize();

        /* Loading Notification */
        $('#NotifikasiEdit').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pertanyaan/ProsesEdit.php',
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
                    ShowDaftarPertanyaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Edit Daftar Pertanyaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiEdit').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DELETE */
    $(document).on('click', '.modal_delete_soft', function () {

        //tangkap data 'id_survey_question' dan buat variabel
        var id_survey_question   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapusSoft').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapusSoft').html('');

        //Form Loading
        $('#FormHapusSoft').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pertanyaan/FormHapusSoft.php',
            data        : {id_survey_question: id_survey_question},
            success     : function(data){
                $('#FormHapusSoft').html(data);
            }
        });
    });

    /* Ketika 'ProsesHapusSoft' disubmit */
    $('#ProsesHapusSoft').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesHapusSoft=$('#ProsesHapusSoft').serialize();

        /* Loading Notification */
        $('#NotifikasiHapusSoft').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pertanyaan/ProsesHapusSoft.php',
            dataType: 'json',
            data    : ProsesHapusSoft,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiHapusSoft').html('');

                    //Tutup modal
                    $('#ModalHapusSoft').modal('hide');

                    //reload tabel
                    ShowDaftarPertanyaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Daftar Pertanyaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiHapusSoft').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

    /* MODAL DELETE */
    $(document).on('click', '.modal_delete', function () {

        //tangkap data 'id_survey_question' dan buat variabel
        var id_survey_question   = $(this).data('id');

        //tampilkan modal
        $('#ModalHapus').modal('show');

        // Kosongkan Notifikasi
        $('#NotifikasiHapus').html('');

        //Form Loading
        $('#FormHapus').html('Loading...');

        //Tampilkan Form Dengan Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Pertanyaan/FormHapus.php',
            data        : {id_survey_question: id_survey_question},
            success     : function(data){
                $('#FormHapus').html(data);
            }
        });
    });

    /* Ketika 'ProsesHapus' disubmit */
    $('#ProsesHapus').submit(function(){
       
        /* Menangkap data dari form  */
        var ProsesHapus=$('#ProsesHapus').serialize();

        /* Loading Notification */
        $('#NotifikasiHapus').html('loading..');

        /* Kirim data dengan AJAX  */
        $.ajax({
            type    : 'POST',
            url     : '_Page/Pertanyaan/ProsesHapus.php',
            dataType: 'json',
            data    : ProsesHapus,
            success: function(response) {
                var status  = response.status;
                var message = response.message;

                // Apabila berhasil
                if(status=='success'){
                    //Bersihkan notifikasi
                    $('#NotifikasiHapus').html('');

                    //Tutup modal
                    $('#ModalHapus').modal('hide');

                    //reload tabel
                    ShowDaftarPertanyaan();

                    // Menampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Daftar Pertanyaan Berhasil!',
                        'success'
                    )
                }else{
                    $('#NotifikasiHapus').html('<div class="alert alert-danger"><small>'+message+'</small></div>');
                }
                
            }
        });
    });

});
