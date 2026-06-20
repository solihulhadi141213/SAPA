<?php
    if(!empty($_SESSION['NotifikasiSwal'])){
        $NotifikasiSwal=$_SESSION['NotifikasiSwal'];
?>
    <!------- Notifikasi ------------>
    <?php if($NotifikasiSwal=="Login Berhasil"){ ?>
        <script>
            Swal.fire(
                'Selamat Datang!',
                'Login Berhasil!',
                'success'
            )
        </script>
    <?php } ?>
    
<?php 
    unset($_SESSION['NotifikasiSwal']);
    }
?>