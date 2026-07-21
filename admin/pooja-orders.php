<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    if (!in_array($status, $validStatuses, true)) {
        $alertHtml = '<div class="alert-error">अमान्य स्थिति</div>';
    } else {
        $stmt = $db->prepare("UPDATE pooja_bookings SET status = :status, admin_notes = :notes WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':notes' => sanitize($_POST['admin_notes'] ?? ''),
            ':id' => $_POST['id']
        ]);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    }
}

$statusFilter = $_GET['status'] ?? 'pending';
$query = $statusFilter === 'all'
    ? $db->query("SELECT pb.*, ps.title_ne AS service_name FROM pooja_bookings pb LEFT JOIN pooja_services ps ON pb.service_id = ps.id ORDER BY pb.created_at DESC")
    : $db->prepare("SELECT pb.*, ps.title_ne AS service_name FROM pooja_bookings pb LEFT JOIN pooja_services ps ON pb.service_id = ps.id WHERE pb.status = :status ORDER BY pb.created_at DESC");

if ($statusFilter !== 'all') {
    $query->execute([':status' => $statusFilter]);
}
$bookings = $statusFilter === 'all' ? $query->fetchAll() : $query->fetchAll();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>पूजा अर्डर व्यवस्थापन</h1>
</div>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=confirmed" class="<?= $statusFilter === 'confirmed' ? 'active' : '' ?>">पुष्टि</a>
    <a href="?status=completed" class="<?= $statusFilter === 'completed' ? 'active' : '' ?>">सम्पन्न</a>
    <a href="?status=cancelled" class="<?= $statusFilter === 'cancelled' ? 'active' : '' ?>">रद्द</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<div class="data-table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>नाम</th>
                <th>फोन</th>
                <th>सेवा</th>
                <th>मिति</th>
                <th>स्ट्रिम</th>
                <th>स्थिति</th>
                <th>कार्य</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td class="td-name"><?= htmlspecialchars($b['name']) ?></td>
                <td><?= htmlspecialchars($b['phone']) ?></td>
                <td><?= htmlspecialchars($b['service_name'] ?? '—') ?></td>
                <td><?= $b['preferred_date'] ?> <?= $b['preferred_time'] ?? '' ?></td>
                <td><?= $b['is_live_stream'] ? 'लाइभ' : '—' ?></td>
                <td><span class="badge badge-<?= $b['status'] ?>"><?= $b['status'] ?></span></td>
                <td>
                    <form method="POST" class="action-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <input name="admin_notes" placeholder="नोट" value="<?= htmlspecialchars($b['admin_notes'] ?? '') ?>" class="form-input" style="width:80px;font-size:.8rem">
                        <select name="status" class="form-input" style="width:auto;font-size:.8rem" onchange="this.form.submit()">
                            <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                            <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                            <option value="completed" <?= $b['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                            <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-sm">अपडेट</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($bookings)): ?>
            <tr><td colspan="8" class="empty-state">कुनै पूजा अर्डर छैन</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
