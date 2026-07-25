<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();
$alertHtml = '';

if (isset($_GET['mark_read'])) {
    validateCsrfGet();
    $stmt = $db->prepare("UPDATE contact_messages SET is_read = TRUE WHERE id = :id");
    $stmt->execute([':id' => $_GET['mark_read']]);
    header('Location: messages.php');
    exit;
}

if (isset($_GET['delete'])) {
    validateCsrfGet();
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
    $stmt->execute([':id' => $_GET['delete']]);
    $alertHtml = '<div class="alert-success">सन्देश मेटाइयो</div>';
}

$filter = $_GET['filter'] ?? 'all';
if ($filter === 'unread') {
    $messages = $db->query("SELECT * FROM contact_messages WHERE is_read = FALSE ORDER BY created_at DESC")->fetchAll();
} else {
    $messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
}

$unreadCount = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE")->fetchColumn();
?>

<?= $alertHtml ?>

<div class="page-header">
    <h1>सन्देशहरू</h1>
    <p class="header-sub"><?= $unreadCount ?> नयाँ सन्देश</p>
</div>

<div class="section-tabs">
    <a href="?filter=all" class="section-tab <?= $filter === 'all' ? 'active' : '' ?>">सबै</a>
    <a href="?filter=unread" class="section-tab <?= $filter === 'unread' ? 'active' : '' ?>">नपढिएको (<?= $unreadCount ?>)</a>
</div>

<div class="data-table-wrap">
    <table class="data-table" role="table" aria-label="Messages">
        <thead>
            <tr><th>#</th><th>नाम</th><th>सम्पर्क</th><th>विषय</th><th>स्थिति</th><th>मिति</th><th>कार्य</th></tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
            <tr style="<?= !$m['is_read'] ? 'background:rgba(230,129,24,.04);font-weight:600' : '' ?>">
                <td><?= $m['id'] ?></td>
                <td class="td-name"><?= htmlspecialchars($m['name']) ?></td>
                <td>
                    <?php if ($m['phone']): ?><div><?= htmlspecialchars($m['phone']) ?></div><?php endif; ?>
                    <?php if ($m['email']): ?><small class="text-muted"><?= htmlspecialchars($m['email']) ?></small><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($m['subject']) ?></td>
                <td><span class="badge badge-<?= $m['is_read'] ? 'completed' : 'pending' ?>"><?= $m['is_read'] ? 'पढियो' : 'नयाँ' ?></span></td>
                <td><?= $m['created_at'] ?></td>
                <td>
                    <div class="action-form">
                        <button class="btn-sm" data-action="toggle-msg" data-id="<?= $m['id'] ?>" aria-expanded="false">विवरण</button>
                        <?php if (!$m['is_read']): ?>
                        <a href="?mark_read=<?= $m['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-primary">पढियो</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $m['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-danger" data-confirm="पक्का मेटाउने?">मेटाउनुहोस्</a>
                    </div>
                </td>
            </tr>
            <tr id="msg-<?= $m['id'] ?>" class="details-row" style="display:none">
                <td colspan="7">
                    <div class="detail-grid" style="grid-template-columns:1fr">
                        <div><strong>सन्देश:</strong><br><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <tr><td colspan="7"><div class="empty-state">कुनै सन्देश छैन</div></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mobile-records">
    <?php if (empty($messages)): ?>
    <div class="empty-state">कुनै सन्देश छैन</div>
    <?php else: foreach ($messages as $m): ?>
    <div class="mobile-record" style="<?= !$m['is_read'] ? 'border-left:3px solid var(--gold-500)' : '' ?>">
        <div class="mobile-record-header">
            <div>
                <h3 class="mobile-record-title"><?= htmlspecialchars($m['name']) ?></h3>
                <span class="mobile-record-ref"><?= htmlspecialchars($m['subject']) ?> · <?= $m['created_at'] ?></span>
            </div>
            <?php if (!$m['is_read']): ?><span class="badge badge-pending">नयाँ</span><?php endif; ?>
        </div>
        <div class="mobile-record-body" style="grid-template-columns:1fr">
            <div class="mobile-record-field">
                <span>सन्देश</span>
                <strong><?= nl2br(htmlspecialchars(mb_substr($m['message'], 0, 150))) ?><?= mb_strlen($m['message']) > 150 ? '...' : '' ?></strong>
            </div>
        </div>
        <div class="mobile-record-footer">
            <button class="btn btn-xs btn-outline" data-action="toggle-msg" data-id="<?= $m['id'] ?>">पूरा विवरण</button>
            <?php if (!$m['is_read']): ?>
            <a href="?mark_read=<?= $m['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-primary">पढियो</a>
            <?php endif; ?>
            <a href="?delete=<?= $m['id'] ?>&<?= csrfQuery() ?>" class="btn btn-xs btn-danger" data-confirm="पक्का मेटाउने?">मेटाउनुहोस्</a>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
