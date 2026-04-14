<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
requireLogin();

$user = currentUser();

switch ($user['role']) {
    case 'admin':
        header("Location: ../dashboard/admin.php");
        break;
    case 'petugas':
        header("Location: ../dashboard/petugas.php");
        break;
    case 'pengguna':
        header("Location: ../dashboard/pengguna.php");
        break;
    default:
        session_destroy();
        header("Location: login.php?error=Role tidak dikenali");
        break;
}
exit;
?>


<?php
require_once __DIR__ . '/auth.php';
requireLogin();

echo "<pre>";
print_r($_SESSION);
echo "</pre>";
exit;
?>