<?php
session_start();
session_unset();
session_destroy();

// Arahkan kembali ke halaman login setelah keluar
header("Location: login.php");
exit;
?>