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
?>

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

<?php
// Footer dosyamızı çağırıyoruz
require_once 'footer.php';
?>