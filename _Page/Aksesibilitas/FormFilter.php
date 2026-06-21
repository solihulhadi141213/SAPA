<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan akses
    if ($KeywordBy === "akses") {
        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';

        // Prepared Statement
        $stmt = $Conn->prepare("SELECT DISTINCT akses FROM akses ORDER BY akses ASC");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($data = $result->fetch_assoc()) {
                $akses = htmlspecialchars($data['akses'], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $akses . '">' . $akses . '</option>';
            }
            $stmt->close();
        }
        echo '</select>';
    } else {
        // Default input text
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }
?>