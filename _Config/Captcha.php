<?php
    session_start();

    // Generate captcha
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = substr(str_shuffle($chars), 0, 5);

    $_SESSION['captcha'] = $captcha;

    // Ukuran gambar
    $width  = 140;
    $height = 50;

    $image = imagecreatetruecolor($width, $height);

    // Background
    $bgColor = imagecolorallocate(
        $image,
        rand(200,255),
        rand(200,255),
        rand(200,255)
    );

    imagefill($image, 0, 0, $bgColor);

    // Noise titik
    for ($i = 0; $i < 100; $i++) {
        $noiseColor = imagecolorallocate(
            $image,
            rand(100,200),
            rand(100,200),
            rand(100,200)
        );

        imagesetpixel(
            $image,
            rand(0,$width),
            rand(0,$height),
            $noiseColor
        );
    }

    // Garis random
    for ($i = 0; $i < 5; $i++) {
        $lineColor = imagecolorallocate(
            $image,
            rand(100,200),
            rand(100,200),
            rand(100,200)
        );

        imageline(
            $image,
            rand(0,$width),
            rand(0,$height),
            rand(0,$width),
            rand(0,$height),
            $lineColor
        );
    }

    // Path font
    $fontPath = __DIR__ . '/../assets/fonts/ClassicalDiary.ttf';

    // Validasi font
    if (!file_exists($fontPath)) {
        die("Font tidak ditemukan: " . $fontPath);
    }

    // Tulis captcha
    for ($i = 0; $i < strlen($captcha); $i++) {

        $textColor = imagecolorallocate(
            $image,
            rand(0,100),
            rand(0,100),
            rand(0,100)
        );

        $angle = rand(-25, 25);

        imagettftext(
            $image,
            20,
            $angle,
            20 + ($i * 20),
            rand(30,40),
            $textColor,
            $fontPath,
            $captcha[$i]
        );
    }

    // Header HARUS sebelum output
    header("Content-Type: image/png");

    // Output image
    imagepng($image);

    imagedestroy($image);
?>