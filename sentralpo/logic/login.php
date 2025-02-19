<?php
session_start();

include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama = $conn->real_escape_string($_POST['nama']);
    $passwd = $_POST['passwd'];

    // Query untuk mendapatkan user dan password dari database
    $sql = "SELECT nama_user, passwd FROM usl WHERE nama_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nama);

    // Eksekusi query
    if (!$stmt->execute()) {
        die("Error saat eksekusi query: " . $stmt->error);
    }

    $result = $stmt->get_result();

    // Cek apakah nama ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($passwd, $row['passwd'])) {
            // Jika password cocok
            $_SESSION['nama_user'] = $row['nama_user'];

            // Arahkan ke dashboard berdasarkan nama_user
            if ($row['nama_user'] == 'Library') {
                header("Location: ../dashboard.php");
            } elseif ($row['nama_user'] == 'QC') {
                header("Location: ../qc-dashboard.php");
            } elseif ($row['nama_user'] == 'MCR') {
                header("Location: ../mcr-dashboard.php");
            } elseif ($row['nama_user'] == 'Translation') {
                header("Location: ../Translation-dashboard.php");
            } else {
                echo "Pengguna tidak dikenali.";
            }
            exit(); 
        } else {
            // Jika password salah
            echo "Password yang dimasukkan salah. <br>";
            header("Location: ../login.html?error=1");
            exit();
        }
    } else {
        // Jika nama_user tidak ditemukan
        header("Location: ../login.html?error=2");
        exit();
    }

    $stmt->close();
}

// Menutup koneksi
$conn->close();
?>
