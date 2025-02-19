<?php
session_start(); // Mulai session

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Mengatur ulang cookie session (opsional tapi berguna untuk keamanan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Arahkan ke halaman login setelah logout
header("Location: ../login.html");
exit();
?>
