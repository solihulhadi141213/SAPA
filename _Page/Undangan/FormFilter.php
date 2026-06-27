<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';
    
    // Jika pencarian berdasarkan datetime_invitation
    if ($KeywordBy === "datetime_invitation") {
        echo '<input type="date" name="keyword" id="keyword" class="form-control">';
    } else {
        if ($KeywordBy === "method_invitation") {
            echo '
                <select name="keyword" id="keyword" class="form-control">
                    <option value="">Pilih</option>
                    <option value="Whatsapp">Whatsapp</option>
                    <option value="Email">Email</option>
                    <option value="Manual">Manual</option>
                </select>
            ';
        } else {
            // Default input text
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>