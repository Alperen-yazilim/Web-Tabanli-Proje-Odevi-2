Bana 
<?php
// Header dosyamızı çağırıyoruz (Oturum kontrolü ve Bootstrap otomatik geliyor)
require_once 'header.php';

$mesaj = '';

// Form gönderildiğinde çalışacak blok
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri alıyoruz
    $tc_no = trim($_POST['tc_no']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $birth_date = trim($_POST['birth_date']);
    $sex = trim($_POST['sex']);
    $bld_type = trim($_POST['bld_type']);
    
    $kullanici_id = $_SESSION['user_id']; 

    if (empty($tc_no) || empty($name) || empty($surname)) {
        $mesaj = '<div class="alert alert-danger">Lütfen T.C. Kimlik No, Ad ve Soyad alanlarını doldurun.</div>';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO has_Hasta (TC_no, name, surname, birth_date, sex, bld_type, kullanici_id) VALUES (:tc_no, :name, :surname, :birth_date, :sex, :bld_type, :kullanici_id)");
            
            $ekle = $stmt->execute([
                'tc_no' => $tc_no,
                'name' => $name,
                'surname' => $surname,
                'birth_date' => $birth_date,
                'sex' => $sex,
                'bld_type' => $bld_type,
                'kullanici_id' => $kullanici_id
            ]);

            if ($ekle) {
                $mesaj = '<div class="alert alert-success">Hasta başarıyla eklendi! <a href="index.php" class="alert-link">Listeye dönmek için tıklayın</a>.</div>';
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $mesaj = '<div class="alert alert-warning">Bu T.C. Kimlik Numarası sistemde zaten kayıtlı!</div>';
            } else {
                $mesaj = '<div class="alert alert-danger">Bir hata oluştu: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>             koduna uygun html yaz 
AI cevabı:
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Yeni Hasta Kaydı Oluştur</h5>
            </div>
            <div class="card-body">
                
                <?= $mesaj; ?>

                <form action="add_hasta.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">T.C. Kimlik Numarası <span class="text-danger">*</span></label>
                            <input type="text" name="tc_no" class="form-control" maxlength="11" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Doğum Tarihi</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ad <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="surname" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Cinsiyet</label>
                            <select name="sex" class="form-select">
                                <option value="">Seçiniz...</option>
                                <option value="Erkek">Erkek</option>
                                <option value="Kadın">Kadın</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kan Grubu</label>
                            <select name="bld_type" class="form-select">
                                <option value="">Seçiniz...</option>
                                <option value="A+">A+</option><option value="A-">A-</option>
                                <option value="B+">B+</option><option value="B-">B-</option>
                                <option value="AB+">AB+</option><option value="AB-">AB-</option>
                                <option value="0+">0+</option><option value="0-">0-</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Kaydet</button>
                    <a href="index.php" class="btn btn-secondary">İptal Et</a>
                </form>
            </div>
        </div>
    </div>
</div>

Bana   <?php
require_once 'header.php';

// URL'den hasta ID'si gelmemişse ana sayfaya at
if (!isset($_GET['hasta_id'])) {
    header("Location: index.php");
    exit;
}

$hasta_id = $_GET['hasta_id'];
$kullanici_id = $_SESSION['user_id'];
$mesaj = '';

// 1. GÜVENLİK KONTROLÜ: Bu hasta gerçekten o anki hekime mi ait?
$stmt = $db->prepare("SELECT * FROM has_Hasta WHERE has_ID = :hasta_id AND kullanici_id = :kullanici_id");
$stmt->execute(['hasta_id' => $hasta_id, 'kullanici_id' => $kullanici_id]);
$hasta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hasta) {
    echo '<div class="alert alert-danger mt-5">Bu hastaya işlem yapma yetkiniz yok.</div>';
    require_once 'footer.php';
    exit;
}

// 2. YENİ MUAYENE EKLEME (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diag = trim($_POST['diag']);
    $notes = trim($_POST['notes']);
    $date = trim($_POST['date']);

    if (empty($diag) || empty($date)) {
        $mesaj = '<div class="alert alert-danger">Tarih ve Teşhis/Tanı alanları zorunludur.</div>';
    } else {
        $insert_stmt = $db->prepare("INSERT INTO mua_Muayene (diag, notes, date, has_ID) VALUES (:diag, :notes, :date, :has_id)");
        $ekle = $insert_stmt->execute([
            'diag' => $diag,
            'notes' => $notes,
            'date' => $date,
            'has_id' => $hasta_id
        ]);

        if ($ekle) {
            $mesaj = '<div class="alert alert-success">Muayene kaydı başarıyla eklendi!</div>';
        } else {
            $mesaj = '<div class="alert alert-danger">Kayıt sırasında hata oluştu.</div>';
        }
    }
}

// 3. GEÇMİŞ MUAYENELERİ ÇEKME
$mua_stmt = $db->prepare("SELECT * FROM mua_Muayene WHERE has_ID = :has_id ORDER BY date DESC");
$mua_stmt->execute(['has_id' => $hasta_id]);
$muayeneler = $mua_stmt->fetchAll(PDO::FETCH_ASSOC);
?>          koduna uygun html yaz

AI cevabı:

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Hasta Bilgileri</h5>
            </div>
            <div class="card-body">
                <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($hasta['name'] . ' ' . $hasta['surname']); ?></p>
                <p><strong>T.C. No:</strong> <?= htmlspecialchars($hasta['TC_no']); ?></p>
                <p class="mb-0"><strong>Kan Grubu:</strong> <?= htmlspecialchars($hasta['bld_type']); ?></p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Yeni Muayene Ekle</h5>
            </div>
            <div class="card-body">
                <?= $mesaj; ?>
                <form action="muayene.php?hasta_id=<?= $hasta['has_ID']; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Muayene Tarihi <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teşhis / Tanı <span class="text-danger">*</span></label>
                        <input type="text" name="diag" class="form-control" placeholder="Örn: Akut Faranjit" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Muayene Notları & Bulgular</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Hastanın şikayetleri, bulgular..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Muayeneyi Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Geçmiş Muayene Kayıtları</h5>
            </div>
            <div class="card-body">
                <?php if (count($muayeneler) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Teşhis</th>
                                    <th>Notlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($muayeneler as $mua): ?>
                                    <tr>
                                        <td><?= date('d.m.Y', strtotime($mua['date'])); ?></td>
                                        <td><strong><?= htmlspecialchars($mua['diag']); ?></strong></td>
                                        <td><?= nl2br(htmlspecialchars($mua['notes'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">Bu hastaya ait geçmiş muayene kaydı bulunmamaktadır.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>






