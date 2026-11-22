<?php
/**
 * includes/sidebar.php — Premium Dark Sidebar
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = currentUser();

$navItems = [
    ['dashboard', 'bi-grid-1x2-fill',    'Dashboard',          '/house-price-prediction/dashboard.php', ''],
    ['predict',   'bi-magic',             'Predict Price',      '/house-price-prediction/predict.php',   'AI'],
    ['history',   'bi-clock-history',     'My Predictions',     '/house-price-prediction/history.php',   ''],
    ['compare',   'bi-arrow-left-right',  'Compare',            '/house-price-prediction/compare.php',   ''],
];
$infoItems = [
    ['about', 'bi-info-circle', 'About Project', '/house-price-prediction/about.php'],
    ['index', 'bi-house-door',  'Home Page',     '/house-price-prediction/index.php'],
];
?>

<style>
/* ══════════════════════════════════════════════════════════
   PREMIUM SIDEBAR
══════════════════════════════════════════════════════════ */
.hp-sidebar {
    position: fixed;
    top: 0; left: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #0a1628 0%, #0d1f3c 50%, #0a1628 100%);
    border-right: 1px solid rgba(255,255,255,0.06);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 900;
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    scrollbar-width: none;
}
.hp-sidebar::-webkit-scrollbar { display: none; }

/* Subtle glow on sidebar */
.hp-sidebar::before {
    content: '';
    position: fixed;
    top: 0; left: 0;
    width: 260px; height: 100vh;
    background:
        radial-gradient(ellipse 60% 40% at 30% 20%, rgba(30,111,241,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 50% 30% at 70% 80%, rgba(168,85,247,0.08) 0%, transparent 60%);
    pointer-events: none;
    z-index: -1;
}

/* ── Brand ─────────────────────────────────────────────── */
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 1.25rem 1.25rem;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.sb-brand-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e6ff1, #00d4ff);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: #fff;
    box-shadow: 0 4px 15px rgba(30,111,241,0.4);
    flex-shrink: 0;
}
.sb-brand-text {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.01em;
}
.sb-brand-text span {
    background: linear-gradient(135deg, #00d4ff, #a855f7);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* ── User profile ───────────────────────────────────────── */
.sidebar-profile {
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.9rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    margin-bottom: 0.5rem;
}
.sb-avatar {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e6ff1 0%, #a855f7 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; font-weight: 800; color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(30,111,241,0.3);
    position: relative;
}
.sb-avatar::after {
    content: '';
    position: absolute;
    bottom: -2px; right: -2px;
    width: 11px; height: 11px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid #0a1628;
}
.sb-user-name  { font-size: 0.88rem; font-weight: 700; color: #fff; line-height: 1.3; }
.sb-user-role  { font-size: 0.72rem; color: rgba(255,255,255,0.35); font-weight: 500; }

/* ── Navigation ─────────────────────────────────────────── */
.sidebar-nav { padding: 0 0.75rem; flex: 1; }

.sb-section-label {
    font-size: 0.63rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.2);
    text-transform: uppercase;
    padding: 1rem 0.6rem 0.4rem;
}

.sb-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.72rem 0.9rem;
    border-radius: 10px;
    color: rgba(255,255,255,0.45);
    font-size: 0.88rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
    margin-bottom: 2px;
    cursor: pointer;
}
.sb-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
    transition: transform 0.2s;
}
.sb-link .sb-badge {
    margin-left: auto;
    font-size: 0.6rem;
    font-weight: 800;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #1e6ff1, #00d4ff);
    color: #fff;
    letter-spacing: 0.04em;
}

.sb-link:hover {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.85);
}
.sb-link:hover i { transform: translateX(2px); }

.sb-link.active {
    background: linear-gradient(135deg, rgba(30,111,241,0.25), rgba(0,212,255,0.1));
    color: #fff;
    border: 1px solid rgba(30,111,241,0.3);
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(30,111,241,0.15);
}
.sb-link.active i { color: #00d4ff; }

/* Active left accent bar */
.sb-link.active::before {
    content: '';
    position: absolute;
    left: -0.75rem;
    top: 25%; bottom: 25%;
    width: 3px;
    border-radius: 0 3px 3px 0;
    background: linear-gradient(180deg, #1e6ff1, #00d4ff);
}

.sb-link.danger { color: rgba(239,68,68,0.6); }
.sb-link.danger:hover { background: rgba(239,68,68,0.1); color: #ef4444; }

/* ── Sidebar Footer ─────────────────────────────────────── */
.sidebar-bottom {
    padding: 1rem 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.05);
    margin-top: auto;
}
.sb-status {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.9rem;
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.15);
    border-radius: 10px;
}
.sb-status-dot {
    width: 8px; height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse-green 2s infinite;
    flex-shrink: 0;
}
@keyframes pulse-green {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    50%       { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
}
.sb-status-text { font-size: 0.75rem; font-weight: 600; color: #10b981; }
</style>

<aside class="hp-sidebar" id="hpSidebar">

    <!-- Brand -->
    <a href="/house-price-prediction/index.php" class="sidebar-brand">
        <div class="sb-brand-icon"><i class="bi bi-houses-fill"></i></div>
        <div class="sb-brand-text">HousePrice <span>AI</span></div>
    </a>

    <!-- User Profile -->
    <div class="sidebar-profile">
        <div class="sb-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
        <div>
            <div class="sb-user-name"><?= htmlspecialchars($user['name']) ?></div>
            <div class="sb-user-role">Premium User</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="sb-section-label">Main Menu</div>
        <?php foreach ($navItems as [$page, $icon, $label, $href, $badge]): ?>
        <a class="sb-link <?= $currentPage === $page ? 'active' : '' ?>" href="<?= $href ?>">
            <i class="bi <?= $icon ?>"></i>
            <span><?= $label ?></span>
            <?php if ($badge): ?>
            <span class="sb-badge"><?= $badge ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>

        <div class="sb-section-label">Information</div>
        <?php foreach ($infoItems as [$page, $icon, $label, $href]): ?>
        <a class="sb-link <?= $currentPage === $page ? 'active' : '' ?>" href="<?= $href ?>">
            <i class="bi <?= $icon ?>"></i>
            <span><?= $label ?></span>
        </a>
        <?php endforeach; ?>

        <div class="sb-section-label">Account</div>
        <a class="sb-link danger" href="/house-price-prediction/logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
        </a>
    </nav>

    <!-- Bottom status -->
    <div class="sidebar-bottom">
        <div class="sb-status">
            <div class="sb-status-dot"></div>
            <span class="sb-status-text">ML Model Active · R² 0.92</span>
        </div>
    </div>

</aside>
