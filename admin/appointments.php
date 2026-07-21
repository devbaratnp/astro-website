<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

$validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    if (!in_array($status, $validStatuses, true)) {
        echo '<div class="alert alert-danger">अमान्य स्थिति</div>';
    } else {
        $stmt = $db->prepare("UPDATE appointments SET status = :status, admin_notes = :notes WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':notes' => $_POST['admin_notes'] ?? '',
            ':id' => $_POST['id']
        ]);
        echo '<div class="alert alert-success">अपडेट गरियो</div>';
    }
}

$statusFilter = $_GET['status'] ?? 'pending';

$query = "SELECT a.*, p.title_ne AS product_title FROM appointments a LEFT JOIN products p ON a.product_id = p.id ORDER BY a.created_at DESC";
$where = '';
$params = [];

if ($statusFilter !== 'all') {
    $where = " WHERE a.status = :status";
    $params[':status'] = $statusFilter;
}

$stmt = $db->prepare("SELECT a.*, p.title_ne AS product_title, p.title_en AS product_title_en, p.price AS product_price FROM appointments a LEFT JOIN products p ON a.product_id = p.id$where ORDER BY a.created_at DESC");
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>परामर्श व्यवस्थापन</h1>
</div>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">पुष्टि</a>
    <a href="?status=completed" class="<?= $statusFilter === 'completed' ? 'active' : '' ?>">सम्पन्न</a>
    <a href="?status=cancelled" class="<?= $statusFilter === 'cancelled' ? 'active' : '' ?>">रद्द</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>नाम</th>
            <th>फोन</th>
            <th>सेवा</th>
            <th>मिति</th>
            <th>समय</th>
            <th>माध्यम</th>
            <th>स्टोर</th>
            <th>स्थिति</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><strong><?= htmlspecialchars($a['name']) ?></strong></td>
            <td><?= htmlspecialchars($a['phone']) ?></td>
            <td><?= $a['service_type'] ?></td>
            <td><?= $a['preferred_date'] ?? '—' ?></td>
            <td><?= $a['preferred_time'] ? date('h:i A', strtotime($a['preferred_time'])) : '—' ?></td>
            <td><?= $a['consultation_mode'] ?></td>
            <td><?= $a['product_title'] ? htmlspecialchars($a['product_title']) : '—' ?></td>
            <td><span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
            <td>
                <button class="btn-small" onclick="toggleDetails(<?= $a['id'] ?>)">विवरण</button>
            </td>
        </tr>
        <tr id="details-<?= $a['id'] ?>" class="details-row" style="display:none">
            <td colspan="9">
                <form method="POST" class="inline-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <div class="detail-grid">
                        <div><strong>इमेल:</strong> <?= htmlspecialchars($a['email'] ?: '—') ?></div>
                        <div><strong>फोन:</strong> <?= htmlspecialchars($a['phone']) ?></div>
                        <div><strong>जन्म मिति:</strong> <?= $a['birth_date'] ?? '—' ?> <?= $a['birth_time'] ? date('h:i A', strtotime($a['birth_time'])) : '' ?></div>
                        <div><strong>जन्म स्थान:</strong> <?= htmlspecialchars($a['birth_place'] ?: '—') ?></div>
                        <div><strong>सन्देश:</strong> <?= nl2br(htmlspecialchars($a['message'])) ?></div>
                        <div><strong>भिडियो लिङ्क:</strong> <?= $a['meeting_url'] ? '<a href="' . htmlspecialchars($a['meeting_url']) . '" target="_blank">' . htmlspecialchars($a['meeting_url']) . '</a>' : '—' ?></div>
                        <?php if ($a['product_title']): ?>
                        <div><strong>स्टोर उत्पादन:</strong> <?= htmlspecialchars($a['product_title']) ?><?= $a['product_price'] ? ' (रु ' . number_format($a['product_price']) . ')' : '' ?></div>
                        <?php endif; ?>
                        <div><strong>नोट:</strong> <textarea name="admin_notes" rows="2"><?= htmlspecialchars($a['admin_notes'] ?? '') ?></textarea></div>
                        <div>
                            <select name="status">
                                <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                                <option value="confirmed" <?= $a['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                                <option value="completed" <?= $a['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                                <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-small btn-primary">अपडेट गर्नुहोस्</button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<script>
function toggleDetails(id) {
    var row = document.getElementById('details-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
