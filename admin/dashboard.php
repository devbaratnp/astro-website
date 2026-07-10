<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = Database::getConnection();

$pendingAppointments = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$todayAppointments = $db->query("SELECT COUNT(*) FROM appointments WHERE preferred_date = CURDATE()")->fetchColumn();
$pendingPooja = $db->query("SELECT COUNT(*) FROM pooja_bookings WHERE status = 'pending'")->fetchColumn();
$pendingPayments = $db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
?>

<h1>ड्यासबोर्ड</h1>
<div class="stats-grid">
    <div class="stat-card">
        <strong><?= $pendingAppointments ?></strong>
        <span>नयाँ परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $todayAppointments ?></strong>
        <span>आजको परामर्श</span>
    </div>
    <div class="stat-card">
        <strong><?= $pendingPooja ?></strong>
        <span>पूजा अर्डर</span>
    </div>
    <div class="stat-card">
        <strong><?= $pendingPayments ?></strong>
        <span>भुक्तानी पेन्डिङ</span>
    </div>
</div>

<div class="recent-section">
    <h2>भर्खरका परामर्श अनुरोध</h2>
    <?php
    $recent = $db->query("SELECT id, name, phone, service_type, preferred_date, status, created_at FROM appointments ORDER BY created_at DESC LIMIT 10");
    while ($row = $recent->fetch()):
    ?>
    <div class="list-item">
        <span class="list-name"><?= htmlspecialchars($row['name']) ?></span>
        <span class="list-phone"><?= htmlspecialchars($row['phone']) ?></span>
        <span class="list-service"><?= htmlspecialchars($row['service_type']) ?></span>
        <span class="list-date"><?= $row['preferred_date'] ?? '—' ?></span>
        <span class="badge badge-<?= $row['status'] ?>"><?= $row['status'] ?></span>
    </div>
    <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
