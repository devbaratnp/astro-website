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
        $stmt = $db->prepare("UPDATE appointments SET status = :status, admin_notes = :notes WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':notes' => $_POST['admin_notes'] ?? '',
            ':id' => $_POST['id']
        ]);
        $alertHtml = '<div class="alert-success">अपडेट गरियो</div>';
    }
}

$statusFilter = $_GET['status'] ?? 'pending';
$query = "SELECT a.*, p.title_ne AS product_title, p.title_en AS product_title_en, p.price AS product_price FROM appointments a LEFT JOIN products p ON a.product_id = p.id";
$where = '';
$params = [];

if ($statusFilter !== 'all') {
    $where = " WHERE a.status = :status";
    $params[':status'] = $statusFilter;
}

$stmt = $db->prepare("$query$where ORDER BY a.created_at DESC");
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>परामर्श व्यवस्थापन</h1>
</div>

<div class="section-tabs">
    <a href="?status=pending" class="section-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">पेन्डिङ</a>
    <a href="?status=confirmed" class="section-tab <?= $statusFilter === 'confirmed' ? 'active' : '' ?>">पुष्टि</a>
    <a href="?status=completed" class="section-tab <?= $statusFilter === 'completed' ? 'active' : '' ?>">सम्पन्न</a>
    <a href="?status=cancelled" class="section-tab <?= $statusFilter === 'cancelled' ? 'active' : '' ?>">रद्द</a>
    <a href="?status=all" class="section-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">सबै</a>
</div>

<div class="data-table-wrap">
    <table class="data-table" role="table" aria-label="Appointments list">
        <thead>
            <tr>
                <th>नाम</th>
                <th>फोन</th>
                <th>सेवा</th>
                <th>मिति</th>
                <th>समय</th>
                <th>माध्यम</th>
                <th>स्थिति</th>
                <th>कार्य</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $a): ?>
            <tr>
                <td class="td-name"><?= htmlspecialchars($a['name']) ?></td>
                <td><?= htmlspecialchars($a['phone']) ?></td>
                <td><?= htmlspecialchars($a['service_type']) ?></td>
                <td><?= $a['preferred_date'] ?? '—' ?></td>
                <td><?= $a['preferred_time'] ? date('h:i A', strtotime($a['preferred_time'])) : '—' ?></td>
                <td><?= htmlspecialchars($a['consultation_mode']) ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
                <td><button class="btn-sm" data-action="toggle-details" data-id="<?= $a['id'] ?>" aria-expanded="false">विवरण</button></td>
            </tr>
            <tr id="details-<?= $a['id'] ?>" class="details-row" style="display:none">
                <td colspan="8">
                    <form method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <div class="detail-grid">
                            <div><strong>इमेल:</strong> <?= htmlspecialchars($a['email'] ?: '—') ?></div>
                            <div><strong>फोन:</strong> <?= htmlspecialchars($a['phone']) ?></div>
                            <div><strong>जन्म मिति:</strong> <?= $a['birth_date'] ?? '—' ?> <?= $a['birth_time'] ? date('h:i A', strtotime($a['birth_time'])) : '' ?></div>
                            <div><strong>जन्म स्थान:</strong> <?= htmlspecialchars($a['birth_place'] ?: '—') ?></div>
                            <div><strong>सन्देश:</strong> <?= nl2br(htmlspecialchars($a['message'] ?? '')) ?></div>
                            <div><strong>भिडियो लिङ्क:</strong> <?= $a['meeting_url'] ? '<a href="' . htmlspecialchars($a['meeting_url']) . '" target="_blank" rel="noreferrer">' . htmlspecialchars($a['meeting_url']) . '</a>' : '—' ?></div>
                            <?php if (!empty($a['product_title'])): ?>
                            <div><strong>स्टोर उत्पादन:</strong> <?= htmlspecialchars($a['product_title']) ?><?= !empty($a['product_price']) ? ' (रु ' . number_format($a['product_price']) . ')' : '' ?></div>
                            <?php endif; ?>
                            <div><strong>नोट:</strong> <textarea name="admin_notes" rows="2" class="form-textarea"><?= htmlspecialchars($a['admin_notes'] ?? '') ?></textarea></div>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                                <select name="status" class="form-select" style="width:auto">
                                    <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                                    <option value="confirmed" <?= $a['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                                    <option value="completed" <?= $a['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                                    <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">अपडेट गर्नुहोस्</button>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
            <tr><td colspan="8"><div class="empty-state">कुनै परामर्श छैन</div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mobile-records">
    <?php if (empty($appointments)): ?>
    <div class="empty-state">कुनै परामर्श छैन</div>
    <?php else: foreach ($appointments as $a): ?>
    <div class="mobile-record">
        <div class="mobile-record-header">
            <div>
                <h3 class="mobile-record-title"><?= htmlspecialchars($a['name']) ?></h3>
                <span class="mobile-record-ref"><?= htmlspecialchars($a['phone']) ?> · <?= htmlspecialchars($a['service_type']) ?></span>
            </div>
            <span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span>
        </div>
        <div class="mobile-record-body">
            <div class="mobile-record-field">
                <span>मिति</span>
                <strong><?= $a['preferred_date'] ?? '—' ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>समय</span>
                <strong><?= $a['preferred_time'] ? date('h:i A', strtotime($a['preferred_time'])) : '—' ?></strong>
            </div>
            <div class="mobile-record-field">
                <span>माध्यम</span>
                <strong><?= htmlspecialchars($a['consultation_mode']) ?></strong>
            </div>
        </div>
        <div class="mobile-record-footer">
            <a href="?edit=<?= $a['id'] ?>" class="btn btn-sm btn-outline">विवरण</a>
            <form method="POST" style="display:inline-flex;gap:6px;flex-wrap:wrap">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <select name="status" class="form-select" style="width:auto;font-size:11px;padding:4px 8px" onchange="this.form.submit()">
                    <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>पेन्डिङ</option>
                    <option value="confirmed" <?= $a['status'] === 'confirmed' ? 'selected' : '' ?>>पुष्टि</option>
                    <option value="completed" <?= $a['status'] === 'completed' ? 'selected' : '' ?>>सम्पन्न</option>
                    <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>रद्द</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-xs btn-primary">अपडेट</button>
            </form>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
