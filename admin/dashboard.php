<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

$stats = [
    'pending_appointments' => $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn(),
    'today_appointments' => $db->query("SELECT COUNT(*) FROM appointments WHERE preferred_date = CURDATE()")->fetchColumn(),
    'pending_pooja' => $db->query("SELECT COUNT(*) FROM pooja_bookings WHERE status = 'pending'")->fetchColumn(),
    'pending_payments' => $db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn(),
    'unread_messages' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE")->fetchColumn(),
    'active_services' => $db->query("SELECT COUNT(*) FROM pooja_services WHERE is_active = TRUE")->fetchColumn(),
    'total_articles' => $db->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'total_appointments' => $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
    'total_products' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
];
?>

<div class="page-header">
    <h1>नमस्ते, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'प्रशासक') ?></h1>
</div>

<div class="stats-grid">
    <div class="stat-card stat-card-red">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['pending_appointments'] ?></span>
            <span class="stat-card-label">नयाँ परामर्श</span>
        </div>
    </div>
    <div class="stat-card stat-card-gold">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['today_appointments'] ?></span>
            <span class="stat-card-label">आजको परामर्श</span>
        </div>
    </div>
    <div class="stat-card stat-card-amber">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['pending_pooja'] ?></span>
            <span class="stat-card-label">पूजा अर्डर</span>
        </div>
    </div>
    <div class="stat-card stat-card-red">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['pending_payments'] ?></span>
            <span class="stat-card-label">भुक्तानी पेन्डिङ</span>
        </div>
    </div>
    <div class="stat-card stat-card-amber">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['unread_messages'] ?></span>
            <span class="stat-card-label">नपढिएको सन्देश</span>
        </div>
    </div>
    <div class="stat-card stat-card-green">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['active_services'] ?></span>
            <span class="stat-card-label">सक्रिय सेवा</span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="recent-section">
        <h2>भर्खरका परामर्श</h2>
        <?php
        $recent = $db->query("SELECT id, name, phone, service_type, preferred_date, status, created_at FROM appointments ORDER BY created_at DESC LIMIT 8");
        if ($recent->rowCount() > 0):
            while ($row = $recent->fetch()):
        ?>
        <div class="list-item">
            <span class="list-item-name"><?= htmlspecialchars($row['name']) ?></span>
            <span class="list-item-meta"><?= htmlspecialchars($row['phone']) ?></span>
            <span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <div class="empty-state">कुनै परामर्श छैन</div>
        <?php endif; ?>
    </div>

    <div class="recent-section">
        <h2>भर्खरका सन्देशहरू</h2>
        <?php
        $msgs = $db->query("SELECT id, name, subject, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 8");
        if ($msgs->rowCount() > 0):
            while ($m = $msgs->fetch()):
        ?>
        <div class="list-item">
            <span class="list-item-name" style="<?= !$m['is_read'] ? 'font-weight:700' : '' ?>"><?= htmlspecialchars($m['name']) ?></span>
            <span class="list-item-meta"><?= htmlspecialchars($m['subject']) ?></span>
            <span class="badge badge-<?= $m['is_read'] ? 'completed' : 'pending' ?>"><?= $m['is_read'] ? 'पढियो' : 'नयाँ' ?></span>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <div class="empty-state">कुनै सन्देश छैन</div>
        <?php endif; ?>
    </div>
</div>

<div class="stats-grid" style="margin-top:24px">
    <div class="stat-card">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['total_appointments'] ?></span>
            <span class="stat-card-label">जम्मा परामर्श</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['active_services'] ?></span>
            <span class="stat-card-label">सक्रिय पूजा सेवा</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['total_products'] ?></span>
            <span class="stat-card-label">स्टोर उत्पादन</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-body">
            <span class="stat-card-value"><?= $stats['total_articles'] ?></span>
            <span class="stat-card-label">प्रकाशित लेख</span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
