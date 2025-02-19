<?php
session_start(); // Mulai session

// Nonaktifkan cache agar pengguna tidak bisa melihat halaman dari cache setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['nama_user'])) {
    header("Location: login.html");
    exit(); // Redirect ke login jika tidak ada session
}

// Ambil data dari session
$nama_user = $_SESSION['nama_user'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SERAH TERIMA</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

    <button onclick="logout()" class="logout-btn">Logout</button>

    <div class="container">
        <!-- Form Serah Terima -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">SERAH</h2>
            </div>
            <div class="card-content">
                <form action="logic/transaction.php" method="POST">
                    <div>
                        <label>Barcode</label>
                        <input type="text" name="Barcode" placeholder="Masukkan barcode" class="input" />
                    </div>
                    <div>
                        <label>Episode</label>
                        <input type="text" name="Episode" placeholder="Masukkan Episode" class="input" />
                    </div>
                    <div>
                        <label>Nama Program</label>
                        <input type="text" name="nama_File" placeholder="Masukkan nama program" class="input" />
                    </div>
                    <div>
                        <label>Tanggal Tayang</label>
                        <input type="date" name="nama_File" placeholder="masukan tanggal tayang" class="input" />
                    </div>
                    <div>
                        <label>Crew yang bertugas</label>
                        <input type="text" name="crew" placeholder="Masukan nama Crew" class="input" />
                    </div>
                    <div>
                        <label>Tujuan</label>
                        <div>
                            <label><input type="checkbox" name="tujuan[]" value="qc">QC</label>
                            <label><input type="checkbox" name="tujuan[]" value="MCR">MCR</label>
                            <label><input type="checkbox" name="tujuan[]" value="MCR">Translation</label>
                        </div>
                    </div>
                    <div>
                        <label for="currentDate">Tanggal</label>
                        <input type="date" id="currentDate" name="tanggal" class="input" />
                    </div>
                    <div>
                        <label for="currentTime">Jam</label>
                        <input type="time" id="currentTime" name="jam" class="input" />
                    </div>
                    <div>
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Masukkan keterangan jika ada" class="input"></textarea>
                    </div>
                    <div style="display:flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="button" class="cancel-btn" style="margin-right: 1rem;">Batal</button>
                        <button type="submit" class="submit-btn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Section -->
       
    </div>

    <script>
        // Fungsi logout
        function logout() {
            window.location.href = 'logic/logout.php';
        }

        // Mengisi tanggal dan jam otomatis
        document.addEventListener('DOMContentLoaded', function() {
            const currentDateInput = document.getElementById('currentDate');
            const currentTimeInput = document.getElementById('currentTime');

            // Mendapatkan tanggal dan waktu saat ini
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Tambahkan nol di depan jika perlu
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            // Set nilai ke input
            currentDateInput.value = `${year}-${month}-${day}`;
            currentTimeInput.value = `${hours}:${minutes}`;
        });

        function openTab(tabName) {
            var i, tabContent, tabButtons;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].className = tabButtons[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " active";
        }


        // Fungsi untuk format tanggal
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadHistory(); // Load data saat halaman pertama kali dibuka
        });
    </script>
</body>
</html>