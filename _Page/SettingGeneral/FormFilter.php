<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Tangkap dan amankan input
    $KeywordBy = $_POST['KeywordBy'] ?? '';

    // Label
    echo '<label for="keyword">Kata Kunci Pencarian</label>';
    
    // Jika pencarian berdasarkan environment_status
    if ($KeywordBy === "environment_status") {
        echo '<select name="keyword" id="keyword" class="form-control">';
        echo '  <option value="">Pilih</option>';

        // Prepared Statement
        $stmt = $Conn->prepare("SELECT DISTINCT environment_status FROM setting_general ORDER BY environment_status ASC");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($data = $result->fetch_assoc()) {
                $environment_status = htmlspecialchars($data['environment_status'], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $environment_status . '">' . $environment_status . '</option>';
            }
            $stmt->close();
        }
        echo '</select>';
    } else {
        // Jika pencarian berdasarkan configuration_status
        if ($KeywordBy === "configuration_status") {
            echo '<select name="keyword" id="keyword" class="form-control">';
            echo '  <option value="1">Active</option>';
            echo '  <option value="0">Inactive</option>';
            echo '</select>';
        } else {
            // Default input text
            echo '<input type="text" name="keyword" id="keyword" class="form-control">';
        }
    }
?>