<?php
// config.php dosyasını dahil ederek veritabanı bağlantısını ve session'ı başlatıyoruz
require_once 'config.php';

$mesaj = ''; // Kullanıcıya gösterilecek hata veya başarı mesajlarını tutacak değişken

// Form gönderildiğinde çalışacak blok
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri alıyoruz
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $spec = trim($_POST['spec']);

    // Boş alan kontrolü
    if (empty($username) || empty($password) || empty($name) || empty($surname)) {
        $mesaj = '<div class="alert alert-danger">Lütfen zorunlu alanları (Ad, Soyad, Kullanıcı Adı, Şifre) doldurunuz.</div>';
    } else {
        // Kullanıcı adının veritabanında daha önce alınıp alınmadığını kontrol ediyoruz
        $check_stmt = $db->prepare("SELECT id FROM kullanicilar WHERE username = :username");
        $check_stmt->execute(['username' => $username]);
        
        if ($check_stmt->rowCount() > 0) {
            $mesaj = '<div class="alert alert-warning">Bu kullanıcı adı zaten alınmış. Lütfen başka bir tane deneyin.</div>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Veritabanına yeni kullanıcıyı ekleme sorgusu
            $insert_stmt = $db->prepare("INSERT INTO kullanicilar (username, password_hash, name, surname, spec) VALUES (:username, :password_hash, :name, :surname, :spec)");
            
            $ekle = $insert_stmt->execute([
                'username' => $username,
                'password_hash' => $hashed_password,
                'name' => $name,
                'surname' => $surname,
                'spec' => $spec
            ]);

            if ($ekle) {
                $mesaj = '<div class="alert alert-success">Kayıt başarıyla oluşturuldu! Şimdi <a href="login.php" class="alert-link">giriş yapabilirsiniz</a>.</div>';
            } else {
                $mesaj = '<div class="alert alert-danger">Kayıt sırasında bir hata oluştu.</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sisteme Kayıt Ol - AHBS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Aile Hekimi Kayıt Ekranı</h4>
                </div>
                <div class="card-body">
                    
                    <?= $mesaj; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Adınız <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">Soyadınız <span class="text-danger">*</span></label>
                            <input type="text" name="surname" id="surname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="spec" class="form-label">Uzmanlık Alanınız</label>
                            <input type="text" name="spec" id="spec" class="form-control" placeholder="Örn: Aile Hekimi Uzmanı">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Kullanıcı Adı Belirleyin <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre Belirleyin <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Zaten bir hesabınız var mı? <a href="login.php" class="text-decoration-none">Giriş Yap</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>