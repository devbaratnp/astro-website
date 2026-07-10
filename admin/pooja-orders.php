<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $db->prepare("UPDATE pooja_bookings SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['status'],
        ':id' => $_POST['id']
    ]);
    echo '<div class="alert alert-success">अपडेट गरियो</div>';
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

<h1>पूजा अर्डर व्यवस्थापन</h1>

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
            <th>स्ट्रिम</th>
            <th>स्थिति</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['name']) ?></td>
            <td><?= htmlspecialchars($b['phone']) ?></td>
            <td><?= htmlspecialchars($b['service_name'] ?? '—') ?></td>
            <td><?= $b['preferred_date'] ?> <?= $b['preferred_time'] ?? '' ?></td>
            <td><?= $b['is_live_stream'] ? 'लाइभ' : '—' ?></td>
            <td><span class="badge badge-<?= $b['status'] ?>"><?= $b['status'] ?></span></td>
            <td>
                <form method="POST" style="display:flex;gap:6px">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                        <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                        <option value="completed" <?= $b['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                        <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                    </select>
                    <button type="submit" name="update_status" class="btn-small">अपडेट</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
