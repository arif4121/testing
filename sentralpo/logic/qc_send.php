<?php
session_start();
include '../connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcode']) && isset($_POST['send_to'])) {
    if (!isset($_SESSION['nama_user'])) {
        echo "Anda harus login untuk melakukan proses ini.";
        exit;
    }
    $barcode = $_POST['barcode'];
    $send_to = $_POST['send_to'];
    $updateStatus = '';
    // Validasi apakah barcode ada di tabel penampung
    $checkBarcodeSql = "SELECT barcode FROM penampung WHERE barcode = ?";
    $stmtCheck = $conn->prepare($checkBarcodeSql);
    $stmtCheck->bind_param("s", $barcode);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    if ($resultCheck->num_rows === 0) {
        echo "Barcode tidak ditemukan.";
        exit;
    }
    // Logika pengiriman file ke Library atau MCR
    if ($send_to === 'Library') {
        $updateStatus = 'sent_to_library';
    } elseif ($send_to === 'MCR') {
        $updateStatus = 'sent_to_mcr';
    }
    // Update status di tabel penampung
    if ($updateStatus) {
        $stmt = $conn->prepare("UPDATE penampung SET status = ?, sent_at = NOW() WHERE barcode = ?");
        $stmt->bind_param("ss", $updateStatus, $barcode);
        if (!$stmt->execute()) {
            echo "Terjadi kesalahan saat mengirim file.";
            exit;
        }
        $stmt->close();
    }
    header("Location: ../qc-dashboard.php"); // Redirect ke dashboard setelah proses
}
?>