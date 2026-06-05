<?php
// config.php dosyasını dahil ediyoruz (session_start() bu dosyanın içinde zaten çalışıyor)
require_once 'config.php';

$mesaj = '';

// Eğer kullanıcı zaten giriş yapmışsa (session varsa), onu direkt ana sayfaya yönlendir
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $mesaj = '<div class="alert alert-danger">Kullanıcı adı ve şifre boş bırakılamaz.</div>';
    } else {
        // Veritabanından kullanıcıyı arıyoruz
        $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            
            // Başarılı giriş sonrası ana sayfaya (hasta listesine) yönlendir
            header("Location: index.php");
            exit;
        } else {
            $mesaj = '<div class="alert alert-danger">Kullanıcı adı veya şifre hatalı!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisteme Giriş - AHBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h4>Aile Hekimi Girişi</h4>
                </div>
                <div class="card-body">
                    
                    <?= $mesaj; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kullanıcı Adı</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Giriş Yap</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Hesabınız yok mu? <a href="register.php" class="text-decoration-none">Kayıt Ol</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>