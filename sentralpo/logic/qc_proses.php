<?php
ob_start(); // Tambahkan ini di awal file
session_start();
include '../connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcode']) && isset($_POST['action'])) {
    $barcode = $_POST['barcode'];
    $action = $_POST['action'];
    $examiner = isset($_POST['examiner']) ? $_POST['examiner'] : 'Anonymous';

    $checkSql = "SELECT * FROM file WHERE barcode = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("s", $barcode);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $fileData = $resultCheck->fetch_assoc();
        $tgl_tayang = $fileData['tgl_tayang'];
        $nama_file = $fileData['nama_file'];

        // Tentukan status_qc berdasarkan $action
        $status_qc = null;
        if ($action === 'received') {
            $status_qc = 'received';
        } elseif ($action === 'rejected') {
            $status_qc = 'rejected';
        } else {
            error_log("Action tidak valid: " . $action);
            echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO penampung (barcode, nama_file, tgl_tayang, examiner, status_qc, status_kirim) 
        VALUES (?, ?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        status_qc = ?, examiner = ?, tgl_tayang = ?, nama_file = ?");

        if ($stmt) {
            $status_kirim = 'belum'; // nilai default

            // Bind semua parameter sekaligus
            $stmt->bind_param("ssssssssss", $barcode, $nama_file, $tgl_tayang, $examiner, $status_qc, $status_kirim, $status_qc, $examiner, $tgl_tayang, $nama_file);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'File processed'];
            } else {
                error_log("Error query: " . $stmt->error);
                $response = ['success' => false, 'message' => 'Error query: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            error_log("Error prepare statement: " . $conn->error);
            $response = ['success' => false, 'message' => 'Error prepare statement: ' . $conn->error];
        }

    } else {
        $response = ['success' => false, 'message' => 'File not found'];
    }

    $stmtCheck->close();
    echo json_encode($response);

} else {
    $response = ['success' => false, 'message' => 'Invalid request'];
    echo json_encode($response);
}
?>