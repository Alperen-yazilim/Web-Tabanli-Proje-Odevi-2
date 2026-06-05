<?php
// Oturum ve veritabanı bağlantısı
require_once 'config.php';

// Oturum açılmamışsa login'e gönder
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// URL'den (GET metodu ile) bir ID gelmiş mi kontrol ediyoruz
if (isset($_GET['id'])) {
    $hasta_id = $_GET['id'];
    $kullanici_id = $_SESSION['user_id'];

    // Sadece o an oturum açmış hekime (kullanici_id) ait olan hastayı siliyoruz (Güvenlik)
    $stmt = $db->prepare("DELETE FROM has_Hasta WHERE has_ID = :has_id AND kullanici_id = :kullanici_id");
    $stmt->execute([
        'has_id' => $hasta_id,
        'kullanici_id' => $kullanici_id
    ]);
}

// Silme işlemi bitince otomatik olarak listeye geri dön
header("Location: index.php");
exit;
?>