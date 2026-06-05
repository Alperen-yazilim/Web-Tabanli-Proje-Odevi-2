<?php
require_once 'header.php';

$mesaj = '';

// URL'den ID gelmediyse ana sayfaya at
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$hasta_id = $_GET['id'];
$kullanici_id = $_SESSION['user_id'];

// Form güncellenmek üzere gönderildiğinde çalışacak blok
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tc_no = trim($_POST['tc_no']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $birth_date = trim($_POST['birth_date']);
    $sex = trim($_POST['sex']);
    $bld_type = trim($_POST['bld_type']);

    try {
        // UPDATE sorgusu ile hastanın verilerini güncelliyoruz
        $update_stmt = $db->prepare("UPDATE has_Hasta SET TC_no = :tc_no, name = :name, surname = :surname, birth_date = :birth_date, sex = :sex, bld_type = :bld_type WHERE has_ID = :has_id AND kullanici_id = :kullanici_id");
        
        $guncelle = $update_stmt->execute([
            'tc_no' => $tc_no,
            'name' => $name,
            'surname' => $surname,
            'birth_date' => $birth_date,
            'sex' => $sex,
            'bld_type' => $bld_type,
            'has_id' => $hasta_id,
            'kullanici_id' => $kullanici_id
        ]);

        if ($guncelle) {
            $mesaj = '<div class="alert alert-success">Hasta bilgileri başarıyla güncellendi! <a href="index.php" class="alert-link">Listeye dön</a>.</div>';
        }
    } catch (PDOException $e) {
        $mesaj = '<div class="alert alert-danger">Güncelleme hatası: ' . $e->getMessage() . '</div>';
    }
}

// Hastanın mevcut verilerini formda göstermek için veritabanından çekiyoruz
$stmt = $db->prepare("SELECT * FROM has_Hasta WHERE has_ID = :has_id AND kullanici_id = :kullanici_id");
$stmt->execute(['has_id' => $hasta_id, 'kullanici_id' => $kullanici_id]);
$hasta = $stmt->fetch(PDO::FETCH_ASSOC);

// Eğer hasta bulunamazsa (veya başkasının hastasıysa) hata ver
if (!$hasta) {
    echo '<div class="alert alert-danger mt-5">Bu hastayı düzenleme yetkiniz yok veya hasta bulunamadı.</div>';
    require_once 'footer.php';
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Hasta Bilgilerini Düzenle</h5>
            </div>
            <div class="card-body">
                
                <?= $mesaj; ?>

                <form action="edit_hasta.php?id=<?= $hasta['has_ID']; ?>" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">T.C. Kimlik Numarası</label>
                            <input type="text" name="tc_no" class="form-control" value="<?= htmlspecialchars($hasta['TC_no']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Doğum Tarihi</label>
                            <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($hasta['birth_date']); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ad</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($hasta['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Soyad</label>
                            <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($hasta['surname']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Cinsiyet</label>
                            <select name="sex" class="form-select">
                                <option value="Erkek" <?= ($hasta['sex'] == 'Erkek') ? 'selected' : ''; ?>>Erkek</option>
                                <option value="Kadın" <?= ($hasta['sex'] == 'Kadın') ? 'selected' : ''; ?>>Kadın</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kan Grubu</label>
                            <select name="bld_type" class="form-select">
                                <option value="A+" <?= ($hasta['bld_type'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                <option value="0-" <?= ($hasta['bld_type'] == '0-') ? 'selected' : ''; ?>>0-</option>
                                <option value="AB+" <?= ($hasta['bld_type'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">Değişiklikleri Kaydet</button>
                    <a href="index.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>