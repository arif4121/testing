<?php
include '../connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil input dari form
    $nama = $conn->real_escape_string($_POST['nama']);
    $passwd = $_POST['passwd'];
    

    // Melakukan hash password
    $hashed_password = password_hash($passwd, PASSWORD_DEFAULT);

    // Query untuk menyimpan data ke dalam tabel usl
    $sql = "INSERT INTO usl (nama_user, passwd) VALUES (?, ?)";

    // Menyiapkan statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nama, $hashed_password);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke halaman login
        header("Location: ../login.html");
        exit(); // Menghentikan eksekusi script setelah redirect
    } else {
        // Jika gagal, tampilkan pesan kesalahan
        echo "Registrasi gagal: " . $stmt->error;
    }

    // Menutup statement
    $stmt->close();
}

// Menutup koneksi
$conn->close();
?>
