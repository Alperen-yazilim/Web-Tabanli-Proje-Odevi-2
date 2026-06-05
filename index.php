<?php
// Header dosyasını çağırıyoruz (İçinde config, session kontrolü ve Bootstrap var)
require_once 'header.php';

// Sadece oturum açan hekime ait hastaları veritabanından çekiyoruz
$stmt = $db->prepare("SELECT * FROM has_Hasta WHERE kullanici_id = :kullanici_id ORDER BY has_ID DESC");
$stmt->execute(['kullanici_id' => $_SESSION['user_id']]);
$hastalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hasta Listem</h5>
        <a href="add_hasta.php" class="btn btn-primary btn-sm">+ Yeni Hasta Ekle</a>
    </div>
    <div class="card-body">
        <?php if (count($hastalar) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>T.C. No</th>
                            <th>Ad</th>
                            <th>Soyad</th>
                            <th>Doğum Tarihi</th>
                            <th>Cinsiyet</th>
                            <th>Kan Grubu</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hastalar as $hasta): ?>
                            <tr>
                                <td><?= htmlspecialchars($hasta['TC_no']); ?></td>
                                <td><?= htmlspecialchars($hasta['name']); ?></td>
                                <td><?= htmlspecialchars($hasta['surname']); ?></td>
                                <td><?= htmlspecialchars($hasta['birth_date']); ?></td>
                                <td><?= htmlspecialchars($hasta['sex']); ?></td>
                                <td><?= htmlspecialchars($hasta['bld_type']); ?></td>
                                <td>
                                    <a href="muayene.php?hasta_id=<?= $hasta['has_ID']; ?>" class="btn btn-info btn-sm text-white">Muayeneler</a>
                                    <a href="edit_hasta.php?id=<?= $hasta['has_ID']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                    <a href="delete_hasta.php?id=<?= $hasta['has_ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu hastayı silmek istediğinize emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Sisteme henüz hiç hasta eklemediniz.</div>
        <?php endif; ?>
    </div>
</div>

<?php
// Footer dosyasını çağırıyoruz
require_once 'footer.php';
?>