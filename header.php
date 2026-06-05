<?php
require_once 'config.php';

// Oturum açılmamışsa login sayfasına at! (Güvenlik)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHBS - Aile Hekimi Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">AHBS Sistemi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Hastalarım</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_hasta.php">Yeni Hasta Ekle</a>
                </li>
            </ul>
            <span class="navbar-text text-white me-3">
                Hoş geldin, Dr. <?= htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']); ?>
            </span>
            <a href="logout.php" class="btn btn-danger btn-sm">Çıkış Yap</a>
        </div>
    </div>
</nav>

<div class="container">