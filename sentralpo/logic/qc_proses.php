<?php
session_start();
include '../connection.php'; // Hubungkan ke database

header('Content-Type: application/json'); // Respon dalam bentuk JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barcode']) && isset($_POST['action'])) {
    $barcode = $_POST['barcode'];
    $action = $_POST['action']; // received atau rejected
    $examiner = isset($_POST['examiner']) ? $_POST['examiner'] : 'Anonymous';

    // Cek apakah barcode tersebut ada di tabel file
    $checkSql = "SELECT * FROM file WHERE barcode = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("s", $barcode);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Jika barcode ditemukan, proses berdasarkan action
        $checkPenampungSql = "SELECT * FROM penampung WHERE barcode = ?";
        $stmtCheckPenampung = $conn->prepare($checkPenampungSql);
        $stmtCheckPenampung->bind_param("s", $barcode);
        $stmtCheckPenampung->execute();
        $resultCheckPenampung = $stmtCheckPenampung->get_result();

        if ($resultCheckPenampung->num_rows > 0) {
            // Update penampung jika sudah ada
            $stmt = $conn->prepare("UPDATE penampung SET status = ?, examiner = ?, processed_at = NOW() WHERE barcode = ?");
            $stmt->bind_param("sss", $action, $examiner, $barcode);
        } else {
            // Insert jika belum ada di penampung
            $stmt = $conn->prepare("INSERT INTO penampung (barcode, status, examiner, processed_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $barcode, $action, $examiner);
        }

        $stmt->execute();
        $stmt->close();

        // Kirim respons sukses
        $response = ['success' => true, 'message' => 'File processed'];
    } else {
        // Kirim respons jika file tidak ditemukan
        $response = ['success' => false, 'message' => 'File not found'];
    }
    $stmtCheck->close();
} else {
    // Kirim respons jika request tidak valid
    $response = ['success' => false, 'message' => 'Invalid request'];
}

// Log respons untuk debugging
error_log("Response: " . json_encode($response));

// Kirim respons sebagai JSON
echo json_encode($response);
?>
