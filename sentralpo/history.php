<?php
// Include connection file
require_once 'connection.php';

// Ambil parameter start_date dan end_date dari URL
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Query dasar untuk mengambil data
$sql = "SELECT barcode, episode, nama_file, nama_crew, nama_penerima, tanggal_kirim, keterangan FROM file";

// Jika ada filter tanggal, tambahkan WHERE clause
if ($start_date && $end_date) {
    $sql .= " WHERE tanggal_kirim BETWEEN '$start_date' AND '$end_date'";
}

// Urutkan berdasarkan tanggal_kirim
$sql .= " ORDER BY tanggal_kirim DESC";

$result = $conn->query($sql);

// Array untuk menyimpan data
$data = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Menutup koneksi database
$conn->close();

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
