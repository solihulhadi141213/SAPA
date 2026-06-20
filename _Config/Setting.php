<?php
    // Nilai default
    $id_setting_general   = 0;
    $app_name             = "SAPA V.1.0.0";
    $app_description      = "Sistem Aspirasi dan Kepuasan Pasien";
    $app_icon             = "favicon.svg";
    $app_author           = "Solihul Hadi";
    $metatag_keyword      = "HTML, CSS, Javascript, MySQL, Rumah Sakit, SIMRS";
    $metatag_description  = "Aplikasi untuk mengukur, mengelola, dan menganalisis tingkat kepuasan pasien terhadap layanan kesehatan di Rumah Sakit.";
    $company_name         = "RSU El-Syifa";
    $company_address      = "Jalan RE Martadinata Nomor 108, Ancaran Kabupaten Kuningan";
    $company_email        = "dhiforester@gmail.com";
    $company_phone        = "0232876240";
    $company_logo         = "logo.png";
    $base_url             = "http://localhost/SAPA";
    $environment_status   = "Development";
    $configuration_status = 1;

    // Query
    $sql = "SELECT * FROM setting_general WHERE configuration_status = 1 LIMIT 1";
    $stmt = $Conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $DataSettingGeneral   = $result->fetch_assoc();
            $id_setting_general   = $DataSettingGeneral['id_setting_general'] ?? 0;
            $app_name             = $DataSettingGeneral['app_name'] ?? "";
            $app_description      = $DataSettingGeneral['app_description'] ?? "";
            $app_icon             = $DataSettingGeneral['app_icon'] ?? "";
            $app_author           = $DataSettingGeneral['app_author'] ?? "";
            $metatag_keyword      = $DataSettingGeneral['metatag_keyword'] ?? "";
            $metatag_description  = $DataSettingGeneral['metatag_description'] ?? "";
            $company_name         = $DataSettingGeneral['company_name'] ?? "";
            $company_address      = $DataSettingGeneral['company_address'] ?? "";
            $company_email        = $DataSettingGeneral['company_email'] ?? "";
            $company_phone        = $DataSettingGeneral['company_phone'] ?? "";
            $company_logo         = $DataSettingGeneral['company_logo'] ?? "";
            $base_url             = $DataSettingGeneral['base_url'] ?? "";
            $environment_status   = $DataSettingGeneral['environment_status'] ?? "";
            $configuration_status = $DataSettingGeneral['configuration_status'] ?? "";
        }

        $stmt->close();
    }
?>