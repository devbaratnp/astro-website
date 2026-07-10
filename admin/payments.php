<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_action'])) {
    $stmt = $db->prepare("UPDATE payments SET status = :status, admin_notes = :notes WHERE id = :id");
    $stmt->execute([
        ':status' => $_POST['payment_action'],
        ':notes' => sanitize($_POST['admin_notes'] ?? ''),
        ':id' => $_POST['id']
    ]);

    if ($_POST['payment_action'] === 'approved') {
        $payStmt = $db->prepare("SELECT booking_type, booking_id FROM payments WHERE id = :id");
        $payStmt->execute([':id' => $_POST['id']]);
        $pay = $payStmt->fetch();
        if ($pay) {
            $table = $pay['booking_type'] === 'pooja' ? 'pooja_bookings' : 'appointments';
            $update = $db->prepare("UPDATE {$table} SET status = 'confirmed' WHERE id = :id");
            $update->execute([':id' => $pay['booking_id']]);
        }
    }

    echo '<div class="alert alert-success">भुक्तानी अपडेट गरियो</div>';
}

$statusFilter = $_GET['status'] ?? 'pending';
$query = $statusFilter === 'all'
    ? $db->query("SELECT * FROM payments ORDER BY created_at DESC")
    : $db->prepare("SELECT * FROM payments WHERE status = :status ORDER BY created_at DESC");

if ($statusFilter !== 'all') {
    $query->execute([':status' => $statusFilter]);
}
$payments = $statusFilter === 'all' ? $query->fetchAll() : $query->fetchAll();
?>

<h1>भुक्तानी प्रमाणिकरण</h1>

<div class="filter-tabs">
    <a href="?status=pending" class="<?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=approved" class="<?= $statusFilter === 'approved' ? 'active' : '' ?>">स्वीकृत</a>
    <a href="?status=rejected" class="<?= $statusFilter === 'rejected' ? 'active' : '' ?>">अस्वीकृत</a>
    <a href="?status=all" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>नाम</th>
            <th>फोन</th>
            <th>रकम</th>
            <th>विधि</th>
            <th>Ref. ID</th>
            <th>स्क्रिनसट</th>
            <th>मिति</th>
            <th>कार्य</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['user_name']) ?></td>
            <td><?= htmlspecialchars($p['user_phone']) ?></td>
            <td>रु <?= number_format($p['amount']) ?></td>
            <td><?= strtoupper($p['method']) ?></td>
            <td><?= htmlspecialchars($p['transaction_ref']) ?></td>
            <td>
                <?php if ($p['screenshot_path']): ?>
                    <a href="<?= htmlspecialchars($p['screenshot_path']) ?>" target="_blank">हेर्नुहोस्</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= $p['created_at'] ?></td>
            <td>
                <?php if ($p['status'] === 'pending'): ?>
                <form method="POST" style="display:flex;gap:6px">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input name="admin_notes" placeholder="नोट" style="width:100px;padding:4px 8px">
                    <button type="submit" name="payment_action" value="approved" class="btn-small" style="background:#155724;color:white">✔</button>
                    <button type="submit" name="payment_action" value="rejected" class="btn-small" style="background:#721c24;color:white">✘</button>
                </form>
                <?php else: ?>
                    <span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span>
                    <?php if ($p['admin_notes']): ?>
                        <small style="display:block;color:#755f59"><?= htmlspecialchars($p['admin_notes']) ?></small>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($payments)): ?>
        <tr><td colspan="9" style="text-align:center;padding:32px;color:#755f59">कुनै भुक्तानी फेला परेन</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
