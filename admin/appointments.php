<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $db->prepare("UPDATE appointments SET status = :status, admin_notes = :notes WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':notes' => $_POST['admin_notes'] ?? '',
        ':id' => $_POST['id']
    ]);
    echo '<div class="alert alert-success">अपडेट गरियो</div>';
}

$statusFilter = $_GET['status'] ?? 'pending';
$query = $statusFilter === 'all'
    ? $db->query("SELECT * FROM appointments ORDER BY created_at DESC")
    : $db->prepare("SELECT * FROM appointments WHERE status = :status ORDER BY created_at DESC");

if ($statusFilter !== 'all') {
    $query->execute([':status' => $statusFilter]);
}
$appointments = $statusFilter === 'all' ? $query->fetchAll() : $query->fetchAll();
?>

<h1>परामर्श व्यवस्थापन</h1>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">पुष्टि</a>
    <a href="?status=completed" class="<?= $statusFilter === 'completed' ? 'active' : '' ?>">सम्पन्न</a>
    <a href="?status=cancelled" class="<?= $statusFilter === 'cancelled' ? 'active' : '' ?>">रद्द</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>नाम</th>
            <th>फोन</th>
            <th>सेवा</th>
            <th>मिति</th>
            <th>माध्यम</th>
            <th>स्थिति</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= htmlspecialchars($a['phone']) ?></td>
            <td><?= htmlspecialchars($a['service_type']) ?></td>
            <td><?= $a['preferred_date'] ?? '—' ?> <?= $a['preferred_time'] ?? '' ?></td>
            <td><?= $a['consultation_mode'] ?></td>
            <td><span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
            <td>
                <button class="btn-small" onclick="toggleDetails(<?= $a['id'] ?>)">विवरण</button>
            </td>
        </tr>
        <tr id="details-<?= $a['id'] ?>" class="details-row" style="display:none">
            <td colspan="8">
                <form method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <div class="detail-grid">
                        <div><strong>इमेल:</strong> <?= htmlspecialchars($a['email'] ?: '—') ?></div>
                        <div><strong>जन्म मिति:</strong> <?= $a['birth_date'] ?? '—' ?> <?= $a['birth_time'] ?? '' ?></div>
                        <div><strong>जन्म स्थान:</strong> <?= htmlspecialchars($a['birth_place'] ?: '—') ?></div>
                        <div><strong>सन्देश:</strong> <?= nl2br(htmlspecialchars($a['message'])) ?></div>
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

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
