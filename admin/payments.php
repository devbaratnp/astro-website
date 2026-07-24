<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$validActions = ['approved', 'rejected'];
$alertHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_action']) && in_array($_POST['payment_action'], $validActions, true)) {
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

    $alertHtml = '<div class="alert-success">भुक्तानी अपडेट गरियो</div>';
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

<?= $alertHtml ?>

<div class="page-header">
    <h1>भुक्तानी प्रमाणिकरण</h1>
</div>

<div class="section-tabs">
    <a href="?status=pending" class="section-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=approved" class="section-tab <?= $statusFilter === 'approved' ? 'active' : '' ?>">स्वीकृत</a>
    <a href="?status=rejected" class="section-tab <?= $statusFilter === 'rejected' ? 'active' : '' ?>">अस्वीकृत</a>
    <a href="?status=all" class="section-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<div class="data-table-wrap">
    <table class="data-table" role="table" aria-label="Payments list">
        <thead>
            <tr>
                <th>#</th>
                <th>नाम</th>
                <th>फोन</th>
                <th>रकम</th>
                <th>विधि</th>
                <th>स्थिति</th>
                <th>मिति</th>
                <th>कार्य</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td class="td-name"><?= htmlspecialchars($p['user_name']) ?></td>
                <td><?= htmlspecialchars($p['user_phone']) ?></td>
                <td class="td-amount">रु <?= number_format($p['amount']) ?></td>
                <td><?= strtoupper($p['method']) ?></td>
                <td><span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
                <td><?= $p['created_at'] ?></td>
                <td>
                    <?php if ($p['status'] === 'pending'): ?>
                    <form method="POST" class="action-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" name="payment_action" value="approved" class="btn btn-xs" style="background:#155724;color:white">स्वीकृत</button>
                        <button type="submit" name="payment_action" value="rejected" class="btn btn-xs btn-danger">अस्वीकृत</button>
                    </form>
                    <?php else: ?>
                        <?php if ($p['admin_notes']): ?>
                            <small style="display:block;color:#755f59;font-size:.75rem"><?= htmlspecialchars($p['admin_notes']) ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
            <tr><td colspan="8"><div class="empty-state">कुनै भुक्तानी फेला परेन</div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mobile-records">
    <?php if (empty($payments)): ?>
    <div class="empty-state">कुनै भुक्तानी फेला परेन</div>
    <?php else: foreach ($payments as $p): ?>
    <div class="mobile-record">
        <div class="mobile-record-header">
            <div>
                <h3 class="mobile-record-title"><?= htmlspecialchars($p['user_name']) ?></h3>
                <span class="mobile-record-ref">#<?= $p['id'] ?> · <?= htmlspecialchars($p['user_phone']) ?></span>
            </div>
            <span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span>
        </div>
        <div class="mobile-record-body">
            <div class="mobile-record-field">
                <span>रकम</span>
                <strong>रु <?= number_format($p['amount']) ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>विधि</span>
                <strong><?= strtoupper($p['method']) ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>मिति</span>
                <strong><?= $p['created_at'] ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>Ref.</span>
                <strong><?= htmlspecialchars($p['transaction_ref'] ?: '—') ?></strong>
            </div>
        </div>
        <?php if ($p['status'] === 'pending'): ?>
        <div class="mobile-record-footer">
            <form method="POST" class="action-form">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" name="payment_action" value="approved" class="btn btn-sm" style="background:#155724;color:white">स्वीकृत</button>
                <button type="submit" name="payment_action" value="rejected" class="btn btn-sm btn-danger">अस्वीकृत</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
