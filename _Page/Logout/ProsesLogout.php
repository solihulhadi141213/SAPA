<?php
// Start session
session_start();

// Connection
include "../../_Config/Connection.php";

// Ambil cookie
$id_akses = $_COOKIE['id_akses'] ?? "";
$token    = $_COOKIE['login_token'] ?? "";

// Jika ada data login
if (!empty($id_akses) && !empty($token)) {

    // Hapus token dari database
    $stmt = $Conn->prepare("DELETE FROM akses_login WHERE id_akses = ? AND token = ?");
    $stmt->bind_param("is", $id_akses, $token);
    $stmt->execute();
    $stmt->close();
}

// Hapus cookie
setcookie("id_akses", "", [
    'expires'  => time() - 3600,
    'path'     => '/',
]);

setcookie("login_token", "", [
    'expires'  => time() - 3600,
    'path'     => '/',
]);

// Hancurkan session
session_unset();
session_destroy();

// Redirect ke login
header("Location: ../../index.php");
exit;