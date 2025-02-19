<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = $_POST['Barcode'];
    $episode = $_POST['Episode'];
    $nama_file = $_POST['nama_File'];
    $tgl_tayang = $_POST('tgl_tayang');
    $nama_crew = $_POST['crew'];
    $tujuan = isset($_POST['tujuan']) ? implode(',', $_POST['tujuan']) : '';
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $keterangan = $_POST['keterangan'];

    // Menyiapkan query dengan prepared statement
    $stmt = $conn->prepare("INSERT INTO File (barcode, episode, nama_file, tgl_tayang, nama_crew, nama_user, tanggal_kirim, jam_kirim, keterangan) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Mengikat parameter ke statement
    $stmt->bind_param("sssssssss", $barcode, $episode, $nama_file, $tgl_tayang, $nama_crew, $tujuan, $tanggal, $jam, $keterangan);

    // Mengeksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke dashboard.html
        header("Location: ../dashboard.php");
        exit(); // Pastikan untuk menghentikan eksekusi script setelah redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

}
?>
