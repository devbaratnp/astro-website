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
];
?>
<h1>नमस्ते, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'प्रशासक') ?> 🙏</h1>

<div class="stats-grid">
    <div class="stat-card">
        <strong><?= $stats['pending_appointments'] ?></strong>
        <span>नयाँ परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $stats['today_appointments'] ?></strong>
        <span>आजको परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $stats['pending_pooja'] ?></strong>
        <span>पूजा अर्डर</span>
    </div>
    <div class="stat-card">
        <strong><?= $stats['pending_payments'] ?></strong>
        <span>भुक्तानी पेन्डिङ</span>
    </div>
    <div class="stat-card">
        <strong><?= $stats['unread_messages'] ?></strong>
        <span>नपढिएको सन्देश</span>
    </div>
    <div class="stat-card">
        <strong><?= $stats['active_services'] ?></strong>
        <span>सक्रिय सेवा</span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="recent-section">
        <h2>भर्खरका परामर्श</h2>
        <?php
        $recent = $db->query("SELECT id, name, phone, service_type, preferred_date, status, created_at FROM appointments ORDER BY created_at DESC LIMIT 8");
        if ($recent->rowCount() > 0):
            while ($row = $recent->fetch()):
        ?>
        <div class="list-item">
            <span class="list-name"><?= htmlspecialchars($row['name']) ?></span>
            <span class="list-phone"><?= htmlspecialchars($row['phone']) ?></span>
            <span class="list-service"><?= htmlspecialchars($row['service_type']) ?></span>
            <span class="list-date"><?= $row['preferred_date'] ?? '—' ?></span>
            <span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <p style="color:var(--muted);text-align:center;padding:24px">कुनै परामर्श छैन</p>
        <?php endif; ?>
    </div>

    <div class="recent-section">
        <h2>भर्खरका सन्देशहरू</h2>
        <?php
        $msgs = $db->query("SELECT id, name, subject, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 8");
        if ($msgs->rowCount() > 0):
            while ($m = $msgs->fetch()):
        ?>
        <div class="list-item" style="<?= !$m['is_read'] ? 'font-weight:700' : '' ?>">
            <span class="list-name"><?= htmlspecialchars($m['name']) ?></span>
            <span class="list-service"><?= htmlspecialchars($m['subject']) ?></span>
            <span class="badge badge-<?= $m['is_read'] ? 'completed' : 'pending' ?>"><?= $m['is_read'] ? 'पढियो' : 'नयाँ' ?></span>
            <span class="list-date"><?= $m['created_at'] ?></span>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <p style="color:var(--muted);text-align:center;padding:24px">कुनै सन्देश छैन</p>
        <?php endif; ?>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;margin-top:24px">
    <div class="stat-card" style="text-align:center">
        <strong><?= $stats['total_appointments'] ?></strong>
        <span>जम्मा परामर्श</span>
    </div>
    <div class="stat-card" style="text-align:center">
        <strong><?= $stats['active_services'] ?></strong>
        <span>सक्रिय पूजा सेवा</span>
    </div>
    <div class="stat-card" style="text-align:center">
        <strong><?= $stats['total_articles'] ?></strong>
        <span>प्रकाशित लेख</span>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
