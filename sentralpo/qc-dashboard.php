<?php
session_start();

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
    <script src="js/qc.js"></script> <!-- Panggil JavaScript untuk logika frontend -->
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
                            <!-- <td><?= $row['status'] ?: 'Belum diproses' ?></td> -->
                            <td>
                                <input type="text" id="examiner_<?= $row['barcode'] ?>" placeholder="Masukkan nama pemeriksa" onkeyup="checkExaminerInput('<?= $row['barcode'] ?>')">
                            </td>
                            <td>
                                <?php if (!$row['status']): ?>
                                    <form method="post" onsubmit="return false;">
                                        <input type="hidden" name="barcode" value="<?= $row['barcode'] ?>">
                                        <button type="button" name="action" value="received" id="receive_<?= $row['barcode'] ?>" onclick="processFile('<?= $row['barcode'] ?>', 'received')" disabled>Receive</button>
                                        <button type="button" name="action" value="rejected" id="reject_<?= $row['barcode'] ?>" onclick="processFile('<?= $row['barcode'] ?>', 'rejected')" disabled>Reject</button>
                                    </form>
                                <?php else: ?>
                                    Sudah diproses
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="logic/qc_send.php">
                                    <input type="hidden" name="barcode" value="<?= $row['barcode'] ?>">
                                    <button type="submit" name="send_to" value="library" id="send_library_<?= $row['barcode'] ?>" disabled>Library</button>
                                    <button type="submit" name="send_to" value="mcr" id="send_mcr_<?= $row['barcode'] ?>" disabled>MCR</button>
                                    <button type="submit" name="send_to" value="mcr" id="subt<?= $row['barcode'] ?>" disabled>Subtitling</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11">Tidak ada data</td></tr>
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
            const examinerInput = document.getElementById('examiner_' + barcode).value;
            const receiveBtn = document.getElementById('receive_' + barcode);
            const rejectBtn = document.getElementById('reject_' + barcode);

            if (examinerInput.trim() !== '') {
                receiveBtn.disabled = false;
                rejectBtn.disabled = false;
            } else {
                receiveBtn.disabled = true;
                rejectBtn.disabled = true;
            }
        }

        // Fungsi untuk memproses file (receive atau reject)
        function processFile(barcode, action) {
            const examinerName = document.getElementById('examiner_' + barcode).value;
            console.log("Examiner: " + examinerName + ", Action: " + action + ", Barcode: " + barcode);

            $.ajax({
                url: 'logic/qc_process.php',
                type: 'POST',
                data: {
                    barcode: barcode,
                    action: action,
                    examiner: examinerName
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert('File ' + action + ' berhasil diproses.');
                        document.getElementById('send_library_' + barcode).disabled = false;
                        document.getElementById('send_mcr_' + barcode).disabled = false;
                        document.getElementById('receive_' + barcode).disabled = true;
                        document.getElementById('reject_' + barcode).disabled = true;
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        }
    </script>
</body>
</html>