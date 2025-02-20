<?php
session_start();

include 'connection.php';

// Nonaktifkan cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['nama_user'])) {
    header("Location: login.html");
    exit();
}

$nama_user = $_SESSION['nama_user'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';

include 'logic/qc_logic.php'; // Panggil logika backend untuk pemrosesan
$data = getFileData(); // Ambil data dari database melalui fungsi backend
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC DASHBOARD</title>
    <link rel="stylesheet" href="css/qc.css">
</head>

<body>
    <button onclick="logout()" class="logout-btn">Logout</button>
    <div class="container">
        <h1>LIST PENERIMAAN</h1>
        <table>
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Episode</th>
                    <th>Nama Program</th>
                    <th>Tanggal tayang</th>
                    <th>Nama Crew</th>
                    <th>Tanggal Kirim</th>
                    <th>Keterangan</th>
                    <th>Nama Pemeriksa</th>
                    <th>Aksi</th>
                    <th>Kirim</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr id="row_<?= $row['barcode'] ?>">
                            <td><?= $row['barcode'] ?></td>
                            <td><?= $row['episode'] ?></td>
                            <td><?= $row['nama_file'] ?></td>
                            <td><?= $row['tgl_tayang'] ?></td>
                            <td><?= $row['nama_crew'] ?></td>
                            <td><?= $row['tanggal_kirim'] ?></td>
                            <td><?= $row['keterangan'] ?></td>
                            <td>
                                <input type="text" id="examiner_<?= $row['barcode'] ?>" placeholder="Masukkan nama pemeriksa"
                                    onkeyup="checkExaminerInput('<?= $row['barcode'] ?>')">
                            </td>
                            <td>
                                <?php if (!$row['status_qc']): ?>
                                    <form>  <input type="hidden" name="barcode" value="<?= $row['barcode'] ?>">
                                    <button type="button"  id="receive_<?= $row['barcode'] ?>"
                                    onclick="processFile('<?= $row['barcode'] ?>', 'received')" disabled>Receive</button>
                                    <button type="button"  id="reject_<?= $row['barcode'] ?>"
                                    onclick="processFile('<?= $row['barcode'] ?>', 'rejected')" disabled>Reject</button>
                                </form>
                                <?php else: ?>
                                    Sudah diproses
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="logic/qc_send.php">
                                    <input type="hidden" name="barcode" value="<?= $row['barcode'] ?>">
                                    <button type="submit" name="send_to" value="library"
                                        id="send_library_<?= $row['barcode'] ?>" disabled>Library</button>
                                    <button type="submit" name="send_to" value="mcr"
                                        id="send_mcr_<?= $row['barcode'] ?>" disabled>MCR</button>
                                    <button type="submit" name="send_to" value="subtitling" id="subt<?= $row['barcode'] ?>"
                                        disabled>Subtitling</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fungsi logout
        function logout() {
            window.location.href = 'logic/logout.php';
        }

        // Fungsi untuk memeriksa input nama pemeriksa
        function checkExaminerInput(barcode) {
            console.log("barcode:", barcode);
            const examinerInput = document.getElementById('examiner_' + barcode);
            const receiveBtn = document.getElementById('receive_' + barcode);
            const rejectBtn = document.getElementById('reject_' + barcode);

            console.log("examinerInput:", examinerInput);
            console.log("receiveBtn:", receiveBtn);
            console.log("rejectBtn:", rejectBtn);

            if (examinerInput && receiveBtn && rejectBtn) { // Periksa apakah semua elemen ditemukan
                if (examinerInput.value.trim() !== '') {
                    receiveBtn.disabled = false;
                    rejectBtn.disabled = false;
                } else {
                    receiveBtn.disabled = true;
                    rejectBtn.disabled = true;
                }
            } else { // Hanya ada satu blok else di sini
                console.error("Elemen tidak ditemukan untuk Bbrcode", barcode);
                if(!examinerInput){
                    console.error("examinerInput tidak ditemukan untuk Barcode", barcode);
                }
                if(!receiveBtn){
                    console.error("receiveBtn tidak ditemukan untuk barcode", barcode);
                }
                if(!rejectBtn){
                    console.error("rejectBtn tidak ditemukan untuk barcode", barcode);
                }
                receiveBtn.disabled = true;
                rejectBtn.disabled = true;
            }
}

        // Fungsi untuk memproses file (receive atau reject)
        function processFile(barcode, action) {
            const examinerName = document.getElementById('examiner_' + barcode).value;

            if (examinerName.trim() === "") {
                alert("Nama pemeriksa harus diisi!");
                return;
            }

            console.log("Examiner: " + examinerName + ", Action: " + action + ", barcode: " + barcode);

            const receiveBtn = document.getElementById('receive_' + barcode);
            const rejectBtn = document.getElementById('reject_' + barcode);
            receiveBtn.disabled = true;
            rejectBtn.disabled = true;

            $.ajax({
                // ... (konfigurasi AJAX Anda)
                success: function(response) {
                    console.log("Respon dari server:", response);
                    try {
                        const res = JSON.parse(response);
                        if (res.success) {
                            if (action === 'received') {
                                document.getElementById('send_library_' + barcode).disabled = false; // Aktifkan tombol *sebelum* alert
                                document.getElementById('send_mcr_' + barcode).disabled = false;
                                document.getElementById('subt' + barcode).disabled = false;
                                document.getElementById('row_' + barcode).style.backgroundColor = "yellow";
                                alert('File dengan barcode ' + barcode + ' berhasil di ' + action + ' berhasil diproses.'); // Tampilkan alert *setelah* tombol diaktifkan
                            } else if (action === 'rejected') {
                                document.getElementById('send_library_' + barcode).disabled = true;
                                document.getElementById('send_mcr_' + barcode).disabled = true;
                                document.getElementById('subt' + barcode).disabled = true;
                                document.getElementById('row_' + barcode).style.backgroundColor = "red";
                                alert('File dengan barcode ' + barcode + ' berhasil di ' + action + ' berhasil diproses.'); // Tampilkan alert *setelah* tombol dinonaktifkan
                            }

                            // ... (kode lain)
                        } else {
                            alert('Error: ' + res.message);
                        }
                    } catch (error) {
                        // ...
                    }
                    receiveBtn.disabled = false;
                    rejectBtn.disabled = false;
                },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            console.error("Respon Error:", xhr.responseText);
                            alert("Terjadi kesalahan saat memproses file: " + status + " - " + error + "\nRespon: " + xhr.responseText);
                            receiveBtn.disabled = false; // Pastikan tombol diaktifkan kembali jika terjadi error
                            rejectBtn.disabled = false;
                        }
                    });
                }
    </script>
</body>

</html>