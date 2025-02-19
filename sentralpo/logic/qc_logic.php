<?php
include 'connection.php'; // Panggil koneksi database

// Fungsi untuk mendapatkan data dari tabel file dan penampung
function getFileData() {
    global $conn;
    
    $sql = "SELECT file.barcode, file.episode, file.nama_file, file.tgl_tayang, file.nama_crew, file.tanggal_kirim, file.keterangan, penampung.status, penampung.examiner 
            FROM file 
            LEFT JOIN penampung ON file.barcode = penampung.barcode";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Fungsi untuk menampilkan baris tabel dengan warna merah jika file sudah dikirim
function displayFileData() {
    $files = getFileData();
    
    foreach ($files as $file) {
        $status = $file['status'] ?? 'Pending';
        $rowClass = ''; // Default tidak ada warna

        // Jika status file sudah 'received' atau 'rejected', tampilkan dengan warna merah
        if ($status === 'received' || $status === 'rejected') {
            $rowClass = 'style="background-color: #ffcccc;"'; // Warna merah muda
        }

        echo "<tr $rowClass>";
        echo "<td>{$file['barcode']}</td>";
        echo "<td>{$file['episode']}</td>";
        echo "<td>{$file['nama_file']}</td>";
        echo "<td>{$file['tgl_tayang']}</td>";
        echo "<td>{$file['nama_crew']}</td>";
        echo "<td>{$file['tanggal_kirim']}</td>";
        echo "<td>{$file['jam_kirim']}</td>";
        echo "<td>{$file['keterangan']}</td>";
        echo "<td>{$status}</td>";
        echo "<td>{$file['examiner']}</td>"; // Menampilkan nama pemeriksa
        echo "</tr>";
    }
}
?>
