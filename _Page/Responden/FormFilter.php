<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';
    
    // Jika pencarian berdasarkan respondent_sex
    if ($KeywordBy === "respondent_sex") {
        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';
        echo '  <option value="Male">Male</option>';
        echo '  <option value="Female">Female</option>';
        echo '</select>';
    } else {
        // Jika pencarian berdasarkan kunjungan_tujuan
        if ($KeywordBy === "kunjungan_tujuan") {
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="">Pilih</option>';
            echo '  <option value="Rajal">Rajal</option>';
            echo '  <option value="Ranap">Ranap</option>';
            echo '</select>';
        } else {
            // Jika pencarian berdasarkan kunjungan_tujuan
            if ($KeywordBy === "tanggal_kunjungan"||$KeywordBy === "respondent_brithdate") {
               echo '<input type="date" name="keyword" id="keyword" class="form-control">';
            } else {
                // Default input text
                echo '<input type="text" name="keyword" id="keyword" class="form-control">';
            }
        }
    }
?>