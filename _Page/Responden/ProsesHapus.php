<?php
    header('Content-Type: application/json');

    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    date_default_timezone_set("Asia/Jakarta");

    function ResponseJson($status, $message){
        echo json_encode([
            "status"  => $status,
            "message" => $message
        ]);
        exit;
    }

    if (empty($SessionIdAkses)) {
        ResponseJson("error", "Sesi akses sudah berakhir. Silakan login ulang.");
    }

    $id_respondent = trim($_POST['id_respondent'] ?? '');

    if ($id_respondent === '') {
        ResponseJson("error", "ID responden tidak boleh kosong.");
    }

    if (!filter_var($id_respondent, FILTER_VALIDATE_INT) || (int)$id_respondent <= 0) {
        ResponseJson("error", "ID responden tidak valid.");
    }

    $id_respondent = (int)$id_respondent;

    $stmt_old = $Conn->prepare("SELECT id_respondent FROM respondent WHERE id_respondent = ? LIMIT 1");
    if (!$stmt_old) {
        ResponseJson("error", "Gagal mempersiapkan validasi data.");
    }

    $stmt_old->bind_param("i", $id_respondent);

    if (!$stmt_old->execute()) {
        $stmt_old->close();
        ResponseJson("error", "Gagal membuka data responden.");
    }

    $result_old = $stmt_old->get_result();

    if ($result_old->num_rows == 0) {
        $stmt_old->close();
        ResponseJson("error", "Data responden tidak ditemukan.");
    }

    $stmt_old->close();

    $stmt_delete = $Conn->prepare("DELETE FROM respondent WHERE id_respondent = ? LIMIT 1");
    if (!$stmt_delete) {
        ResponseJson("error", "Gagal mempersiapkan query hapus.");
    }

    $stmt_delete->bind_param("i", $id_respondent);

    if (!$stmt_delete->execute()) {
        $stmt_delete->close();
        ResponseJson("error", "Gagal menghapus data responden.");
    }

    $stmt_delete->close();

    ResponseJson("success", "Data responden berhasil dihapus.");
?>
