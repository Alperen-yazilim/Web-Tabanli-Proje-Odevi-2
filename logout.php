<?php
session_start();
session_unset();
session_destroy(); // Oturumu tamamen sonlandırır
header("Location: login.php"); // Giriş sayfasına yönlendirir
exit;
?>