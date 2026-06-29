<?php
    $periodeBy = $_POST['KeywordBy'] ?? '';
    if ($periodeBy === 'periode') {
        echo '<input type="date" name="keyword" id="keyword" class="form-control">';
    } else {
        echo '<input type="text" name="keyword" id="keyword" class="form-control">';
    }
?>
