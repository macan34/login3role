
<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE username = ?";

$stmt = $koneksi->prepare($sql);
$stmt->execute([$username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {

    // Membuat ID session baru
    session_regenerate_id(true);

    // Menyimpan informasi pengguna
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Mengarahkan berdasarkan role
    if ($user['role'] === 'admin') {
        header("Location: admin.php");
    } elseif ($user['role'] === 'petugas') {
        header("Location: petugas.php");
    } elseif ($user['role'] === 'siswa') {
        header("Location: siswa.php");
    } else {
        session_unset();
        session_destroy();
        header("Location: index.php?error=role");
    }

    exit;

} else {
    header("Location: index.php?error=1");
    exit;
}
?>
