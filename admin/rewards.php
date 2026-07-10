<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reward'])) {
    $stmt = $db->prepare("
        INSERT INTO rewards (user_name, user_phone, reward_type, title_ne, title_en, description_ne, description_en, expires_at, awarded_by)
        VALUES (:user_name, :user_phone, :reward_type, :title_ne, :title_en, :description_ne, :description_en, :expires_at, :awarded_by)
    ");
    $stmt->execute([
        ':user_name' => sanitize($_POST['user_name']),
        ':user_phone' => sanitize($_POST['user_phone']),
        ':reward_type' => $_POST['reward_type'],
        ':title_ne' => sanitize($_POST['title_ne']),
        ':title_en' => sanitize($_POST['title_en'] ?? ''),
        ':description_ne' => sanitize($_POST['description_ne'] ?? ''),
        ':description_en' => sanitize($_POST['description_en'] ?? ''),
        ':expires_at' => $_POST['expires_at'] ?: null,
        ':awarded_by' => $_SESSION['admin_id'],
    ]);
    echo '<div class="alert alert-success">पुरस्कार सफलतापूर्वक सिर्जना गरियो</div>';
}

$rewards = $db->query("SELECT r.*, a.display_name AS awarded_by_name FROM rewards r LEFT JOIN admin_users a ON r.awarded_by = a.id ORDER BY r.created_at DESC LIMIT 50")->fetchAll();
?>

<h1>पुरस्कार व्यवस्थापन</h1>

<div class="form-card" style="max-width:600px;margin-bottom:32px">
    <h3>नयाँ पुरस्कार दिनुहोस्</h3>
    <form method="POST">
        <div class="form-grid" style="grid-template-columns:1fr 1fr">
            <div class="field"><label>प्रयोगकर्ता नाम *</label><input name="user_name" required></div>
            <div class="field"><label>फोन नम्बर *</label><input name="user_phone" required></div>
            <div class="field"><label>पुरस्कार प्रकार *</label>
                <select name="reward_type" required>
                    <option value="feature">विशेष सुविधा</option>
                    <option value="discount">छुट</option>
                    <option value="badge">ब्याज / मानपदवी</option>
                    <option value="service">निःशुल्क सेवा</option>
                    <option value="other">अन्य</option>
                </select>
            </div>
            <div class="field"><label>म्याद सकिने मिति</label><input name="expires_at" type="date"></div>
            <div class="field"><label>शीर्षक (नेपाली) *</label><input name="title_ne" required placeholder="e.g. निःशुल्क कुण्डली विश्लेषण"></div>
            <div class="field"><label>शीर्षक (अङ्ग्रेजी)</label><input name="title_en" placeholder="e.g. Free Kundali Analysis"></div>
            <div class="field full"><label>विवरण (नेपाली)</label><textarea name="description_ne" rows="2"></textarea></div>
            <div class="field full"><label>विवरण (अङ्ग्रेजी)</label><textarea name="description_en" rows="2"></textarea></div>
        </div>
        <button type="submit" name="create_reward" class="btn btn-primary">पुरस्कार सिर्जना गर्नुहोस्</button>
    </form>
</div>

<h3>हालको पुरस्कारहरू</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>प्रयोगकर्ता</th>
            <th>फोन</th>
            <th>प्रकार</th>
            <th>शीर्षक</th>
            <th>प्रयोग भयो?</th>
            <th>प्रदान गर्ने</th>
            <th>मिति</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rewards as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= htmlspecialchars($r['user_phone']) ?></td>
            <td><?= $r['reward_type'] ?></td>
            <td><?= htmlspecialchars($r['title_ne']) ?></td>
            <td><span class="badge badge-<?= $r['is_redeemed'] ? 'completed' : 'pending' ?>"><?= $r['is_redeemed'] ? 'हो' : 'होइन' ?></span></td>
            <td><?= htmlspecialchars($r['awarded_by_name'] ?? '—') ?></td>
            <td><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
