<?php
/**
 * dashboard.php — Premium Redesign
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$user = currentUser();
$db   = getDB();

$stats = $db->prepare('SELECT COUNT(*) AS total, AVG(predicted_price) AS avg_price, MAX(predicted_price) AS max_price, MIN(predicted_price) AS min_price FROM predictions WHERE user_id = ?');
$stats->execute([$user['id']]);
$s = $stats->fetch();

$total    = (int)$s['total'];
$avgPrice = (float)$s['avg_price'];
$maxPrice = (float)$s['max_price'];
$minPrice = (float)$s['min_price'];

$recent = $db->prepare('SELECT * FROM predictions WHERE user_id = ? ORDER BY created_at DESC LIMIT 6');
$recent->execute([$user['id']]);
$recentRows = $recent->fetchAll();

// Chart data
$distBuckets = ['< ₹25L', '₹25–50L', '₹50L–1Cr', '₹1–2Cr', '₹2–5Cr', '> ₹5Cr'];
$distRanges  = [[0,2500000],[2500000,5000000],[5000000,10000000],[10000000,20000000],[20000000,50000000],[50000000,PHP_INT_MAX]];
$distCounts  = [];
foreach ($distRanges as [$lo, $hi]) {
    $hiV = ($hi === PHP_INT_MAX) ? 999999999999 : $hi;
    $q = $db->prepare('SELECT COUNT(*) FROM predictions WHERE user_id=? AND predicted_price>=? AND predicted_price<?');
    $q->execute([$user['id'], $lo, $hiV]);
    $distCounts[] = (int)$q->fetchColumn();
}

$locQ = $db->prepare('SELECT location, ROUND(AVG(predicted_price)/100000,1) AS avg_lakh FROM predictions WHERE user_id=? GROUP BY location ORDER BY avg_lakh DESC LIMIT 8');
$locQ->execute([$user['id']]);
$locRows = $locQ->fetchAll();

$typeQ = $db->prepare('SELECT property_type, COUNT(*) AS cnt FROM predictions WHERE user_id=? GROUP BY property_type');
$typeQ->execute([$user['id']]);
$typeRows = $typeQ->fetchAll();

// Latest prediction
$latestQ = $db->prepare('SELECT * FROM predictions WHERE user_id=? ORDER BY created_at DESC LIMIT 1');
$latestQ->execute([$user['id']]);
$latest = $latestQ->fetch();

function fmtPrice(float $n): string {
    if ($n >= 10000000) return '₹' . number_format($n/10000000, 2) . ' Cr';
    if ($n >= 100000)   return '₹' . number_format($n/100000, 1)  . ' L';
    return '₹' . number_format($n, 0);
}

$firstName = explode(' ', $user['name'])[0];
$hour = (int)date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ══════════════════════════════════════════════════════════
   PREMIUM DASHBOARD STYLES
══════════════════════════════════════════════════════════ */

/* Layout */
body { background: #f0f4ff !important; }

.dash-layout {
    display: flex;
    min-height: 100vh;
    padding-top: 64px; /* navbar height */
}

.dash-main {
    margin-left: 260px;
    flex: 1;
    padding: 2rem 2rem 3rem;
    min-width: 0;
}

/* ── Top Welcome Banner ──────────────────────────────────── */
.welcome-banner {
    background: linear-gradient(135deg, #0a1628 0%, #1a2d5a 50%, #0d1f3c 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.75rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(10,22,40,0.25);
}
.welcome-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(30,111,241,0.25), transparent 65%);
    pointer-events: none;
}
.welcome-banner::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 30%;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(168,85,247,0.15), transparent 65%);
    pointer-events: none;
}
.welcome-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.4rem;
    position: relative;
    z-index: 1;
}
.welcome-title span {
    background: linear-gradient(135deg, #00d4ff, #a855f7);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.welcome-sub { font-size: 0.9rem; color: rgba(255,255,255,0.5); position: relative; z-index: 1; }

.welcome-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.25rem;
    position: relative;
    z-index: 1;
}
.w-badge {
    display: flex; align-items: center; gap: 0.4rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 0.3rem 0.9rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.65);
}
.w-badge i { color: #00d4ff; }

.btn-new-predict {
    background: linear-gradient(135deg, #1e6ff1, #00d4ff);
    color: #fff !important;
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 6px 20px rgba(30,111,241,0.4);
    transition: all 0.25s;
    position: relative;
    z-index: 1;
    white-space: nowrap;
}
.btn-new-predict:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(30,111,241,0.5); }

/* ── Stat Cards ──────────────────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
@media (max-width: 1200px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .stat-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: #fff;
    border-radius: 18px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(10,22,40,0.07);
    border: 1px solid rgba(255,255,255,0.8);
    transition: transform 0.25s, box-shadow 0.25s;
}
.kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 35px rgba(10,22,40,0.12); }

/* Top gradient stripe */
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 18px 18px 0 0;
}
.kpi-card.blue::before   { background: linear-gradient(90deg, #1e6ff1, #3b82f6); }
.kpi-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
.kpi-card.purple::before { background: linear-gradient(90deg, #a855f7, #c084fc); }
.kpi-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

/* Background watermark icon */
.kpi-card::after {
    content: '';
    position: absolute;
    bottom: -15px; right: -15px;
    width: 90px; height: 90px;
    border-radius: 50%;
    opacity: 0.07;
}
.kpi-card.blue::after   { background: #1e6ff1; }
.kpi-card.green::after  { background: #10b981; }
.kpi-card.purple::after { background: #a855f7; }
.kpi-card.orange::after { background: #f59e0b; }

.kpi-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}
.kpi-icon.blue   { background: #eff6ff; color: #1e6ff1; }
.kpi-icon.green  { background: #ecfdf5; color: #10b981; }
.kpi-icon.purple { background: #faf5ff; color: #a855f7; }
.kpi-icon.orange { background: #fffbeb; color: #f59e0b; }

.kpi-value { font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 0.4rem; }
.kpi-value.sm { font-size: 1.4rem; }
.kpi-label { font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
.kpi-trend {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.75rem; font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    margin-top: 0.75rem;
}
.kpi-trend.up   { background: #ecfdf5; color: #10b981; }
.kpi-trend.info { background: #eff6ff; color: #1e6ff1; }
.kpi-trend.warn { background: #fffbeb; color: #f59e0b; }

/* ── Charts Grid ─────────────────────────────────────────── */
.charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.75rem; }
@media (max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }

/* ── Dashboard Card ──────────────────────────────────────── */
.dash-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(10,22,40,0.07);
    border: 1px solid rgba(255,255,255,0.8);
    overflow: hidden;
    transition: box-shadow 0.25s;
}
.dash-card:hover { box-shadow: 0 8px 30px rgba(10,22,40,0.1); }

.dash-card-header {
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
}
.dash-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dash-card-title i { font-size: 1rem; }
.dash-card-title i.blue   { color: #1e6ff1; }
.dash-card-title i.green  { color: #10b981; }
.dash-card-title i.purple { color: #a855f7; }
.dash-card-title i.orange { color: #f59e0b; }

.dash-card-body { padding: 1.5rem; }
.dash-card-body.p0 { padding: 0; }

.btn-link-sm {
    font-size: 0.78rem;
    font-weight: 600;
    color: #1e6ff1;
    text-decoration: none;
    padding: 0.3rem 0.75rem;
    background: #eff6ff;
    border-radius: 8px;
    transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 0.3rem;
}
.btn-link-sm:hover { background: #dbeafe; color: #1d4ed8; }

/* ── Recent Predictions Table ────────────────────────────── */
.pred-table { width: 100%; border-collapse: collapse; }
.pred-table th {
    background: #f8fafc;
    padding: 0.75rem 1.25rem;
    font-size: 0.72rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border-bottom: 1px solid #f1f5f9;
    white-space: nowrap;
}
.pred-table td {
    padding: 1rem 1.25rem;
    font-size: 0.875rem;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
}
.pred-table tr:last-child td { border-bottom: none; }
.pred-table tbody tr { transition: background 0.15s; }
.pred-table tbody tr:hover { background: #f8fafc; }

.prop-type-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
    flex-shrink: 0;
}

.price-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #1e6ff1;
    font-weight: 700;
    font-size: 0.82rem;
    border-radius: 20px;
    padding: 0.3rem 0.85rem;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
}

.location-cell { font-size: 0.82rem; }
.location-name { font-weight: 600; color: #0f172a; }
.location-city { color: #94a3b8; font-size: 0.75rem; }

.btn-delete-sm {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1px solid #fee2e2;
    background: #fff5f5;
    color: #ef4444;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.btn-delete-sm:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

/* ── Latest Prediction Card ──────────────────────────────── */
.latest-pred-card {
    background: linear-gradient(135deg, #0a1628 0%, #1a2d5a 100%);
    border-radius: 16px;
    padding: 1.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.latest-pred-card::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(30,111,241,0.3), transparent 65%);
}
.lp-label { font-size: 0.72rem; font-weight: 600; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.4rem; }
.lp-price { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #00d4ff, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; }
.lp-range { font-size: 0.78rem; color: rgba(255,255,255,0.4); margin-top: 0.3rem; }

.lp-detail {
    background: rgba(255,255,255,0.07);
    border-radius: 10px;
    padding: 0.65rem 0.9rem;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
    display: flex; align-items: center; gap: 0.5rem;
}
.lp-detail strong { color: #fff; font-weight: 600; }

/* ── Model info card ─────────────────────────────────────── */
.model-metric {
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
}
.model-metric-val  { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.model-metric-lbl  { font-size: 0.72rem; color: #94a3b8; font-weight: 600; margin-top: 0.25rem; }

/* ── Empty state ─────────────────────────────────────────── */
.dash-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #94a3b8;
}
.dash-empty i    { font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.4; }
.dash-empty h6   { font-weight: 700; color: #64748b; margin-bottom: 0.4rem; }
.dash-empty p    { font-size: 0.85rem; }

/* ── Responsive sidebar ──────────────────────────────────── */
@media (max-width: 991px) {
    .hp-sidebar { transform: translateX(-100%); }
    .hp-sidebar.open { transform: translateX(0); box-shadow: 0 0 60px rgba(0,0,0,0.5); }
    .dash-main { margin-left: 0; padding: 1.25rem; }
}

/* Mobile toggle */
.sidebar-toggle-btn {
    display: none;
    position: fixed;
    bottom: 1.5rem; left: 1.5rem;
    z-index: 1000;
    width: 50px; height: 50px;
    border-radius: 14px;
    background: linear-gradient(135deg, #1e6ff1, #00d4ff);
    color: #fff; border: none;
    font-size: 1.3rem;
    align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(30,111,241,0.4);
}
@media (max-width: 991px) { .sidebar-toggle-btn { display: flex; } }

.sidebar-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 850;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.active { display: block; }

/* Chart canvas */
canvas { max-height: 240px !important; }
</style>

<div class="dash-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="bi bi-list"></i></button>

    <main class="dash-main">

        <!-- ── Welcome Banner ──────────────────────────────── -->
        <div class="welcome-banner mb-4">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="welcome-title">
                        <?= $greeting ?>, <span><?= htmlspecialchars($firstName) ?></span> 👋
                    </div>
                    <div class="welcome-sub">Here's your property prediction intelligence overview</div>
                    <div class="welcome-badges">
                        <span class="w-badge"><i class="bi bi-calendar3"></i><?= date('l, d M Y') ?></span>
                        <span class="w-badge"><i class="bi bi-graph-up-arrow"></i><?= $total ?> Total Predictions</span>
                        <span class="w-badge"><i class="bi bi-stars"></i>AI Model Active</span>
                    </div>
                </div>
                <a href="/house-price-prediction/predict.php" class="btn-new-predict">
                    <i class="bi bi-magic"></i>New Prediction
                </a>
            </div>
        </div>

        <!-- ── KPI Stat Cards ───────────────────────────────── -->
        <div class="stat-grid">
            <!-- Total -->
            <div class="kpi-card blue">
                <div class="kpi-icon blue"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="kpi-value" data-counter="<?= $total ?>"><?= $total ?></div>
                <div class="kpi-label">Total Predictions</div>
                <div class="kpi-trend info"><i class="bi bi-clock"></i>All time</div>
            </div>
            <!-- Avg Price -->
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-currency-rupee"></i></div>
                <div class="kpi-value sm"><?= $total ? fmtPrice($avgPrice) : '—' ?></div>
                <div class="kpi-label">Average Price</div>
                <div class="kpi-trend up"><i class="bi bi-bar-chart-fill"></i>Across all</div>
            </div>
            <!-- Max Price -->
            <div class="kpi-card purple">
                <div class="kpi-icon purple"><i class="bi bi-trophy-fill"></i></div>
                <div class="kpi-value sm"><?= $total ? fmtPrice($maxPrice) : '—' ?></div>
                <div class="kpi-label">Highest Value</div>
                <div class="kpi-trend info"><i class="bi bi-arrow-up-right"></i>Best property</div>
            </div>
            <!-- AI Accuracy -->
            <div class="kpi-card orange">
                <div class="kpi-icon orange"><i class="bi bi-stars"></i></div>
                <div class="kpi-value">~92%</div>
                <div class="kpi-label">Model Accuracy</div>
                <div class="kpi-trend warn"><i class="bi bi-shield-check"></i>R² = 0.92</div>
            </div>
        </div>

        <!-- ── Charts Row ───────────────────────────────────── -->
        <div class="charts-grid">

            <!-- Price Distribution -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">
                        <i class="bi bi-bar-chart-fill blue"></i>Price Distribution
                    </div>
                    <span style="font-size:.72rem;color:#94a3b8;font-weight:600;"><?= $total ?> entries</span>
                </div>
                <div class="dash-card-body">
                    <?php if ($total > 0): ?>
                    <canvas id="priceDistChart"></canvas>
                    <?php else: ?>
                    <div class="dash-empty"><i class="bi bi-bar-chart-line"></i><h6>No data yet</h6><p>Make your first prediction to see charts</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Property Type Doughnut -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">
                        <i class="bi bi-pie-chart-fill purple"></i>Property Types
                    </div>
                    <span style="font-size:.72rem;color:#94a3b8;font-weight:600;"><?= count($typeRows) ?> types</span>
                </div>
                <div class="dash-card-body d-flex align-items-center justify-content-center">
                    <?php if (count($typeRows) > 0): ?>
                    <canvas id="propTypeChart"></canvas>
                    <?php else: ?>
                    <div class="dash-empty"><i class="bi bi-pie-chart"></i><h6>No data yet</h6></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Location Chart (full width) ─────────────────── -->
        <?php if (count($locRows) > 0): ?>
        <div class="dash-card mb-4" style="margin-top:-0.5rem;">
            <div class="dash-card-header">
                <div class="dash-card-title">
                    <i class="bi bi-geo-alt-fill blue"></i>Location-Wise Average Price
                    <span style="font-size:.72rem;font-weight:500;color:#94a3b8;">(₹ Lakh)</span>
                </div>
            </div>
            <div class="dash-card-body" style="padding-top:1rem;">
                <canvas id="locationChart" style="max-height:180px !important;"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Bottom Row: Recent + Latest Prediction ──────── -->
        <div class="row g-3">

            <!-- Recent Predictions Table -->
            <div class="col-xl-8">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <div class="dash-card-title">
                            <i class="bi bi-clock-history blue"></i>Recent Predictions
                        </div>
                        <a href="/house-price-prediction/history.php" class="btn-link-sm">
                            <i class="bi bi-arrow-right"></i>View All
                        </a>
                    </div>
                    <div class="dash-card-body p0">
                        <?php if (count($recentRows) > 0): ?>
                        <div style="overflow-x:auto;">
                            <table class="pred-table">
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th>Location</th>
                                        <th>Area</th>
                                        <th>BHK</th>
                                        <th>Predicted Price</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dotColors = ['Apartment'=>'#1e6ff1','Villa'=>'#a855f7','Independent House'=>'#10b981','Commercial'=>'#f59e0b','Studio'=>'#00d4ff'];
                                    foreach ($recentRows as $row):
                                        $dotColor = $dotColors[$row['property_type']] ?? '#94a3b8';
                                    ?>
                                    <tr>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:.4rem;">
                                                <span class="prop-type-dot" style="background:<?= $dotColor ?>;"></span>
                                                <div>
                                                    <div style="font-weight:600;color:#0f172a;font-size:.85rem;"><?= htmlspecialchars($row['property_type']) ?></div>
                                                    <div style="font-size:.72rem;color:#94a3b8;"><?= htmlspecialchars($row['furnishing']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="location-cell">
                                            <div class="location-name"><?= htmlspecialchars($row['location']) ?></div>
                                            <div class="location-city"><?= htmlspecialchars($row['city']) ?></div>
                                        </td>
                                        <td style="color:#64748b;font-size:.82rem;"><?= number_format($row['area']) ?> sqft</td>
                                        <td style="color:#64748b;font-size:.82rem;"><?= $row['bedrooms'] ?> BHK</td>
                                        <td><span class="price-pill"><i class="bi bi-currency-rupee" style="font-size:.7rem;"></i><?= fmtPrice($row['predicted_price']) ?></span></td>
                                        <td style="color:#94a3b8;font-size:.78rem;white-space:nowrap;"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <button class="btn-delete-sm" onclick="deletePrediction(<?= $row['id'] ?>, this)" title="Delete">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="dash-empty">
                            <i class="bi bi-house-slash"></i>
                            <h6>No predictions yet</h6>
                            <p>Start by predicting your first property price</p>
                            <a href="/house-price-prediction/predict.php" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:.75rem;padding:.5rem 1.25rem;background:linear-gradient(135deg,#1e6ff1,#00d4ff);color:#fff;border-radius:10px;font-size:.85rem;font-weight:700;text-decoration:none;">
                                <i class="bi bi-magic"></i>Predict Now
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right sidebar: Latest + Model info -->
            <div class="col-xl-4 d-flex flex-column gap-3">

                <!-- Latest Prediction -->
                <?php if ($latest): ?>
                <div class="dash-card">
                    <div class="dash-card-header">
                        <div class="dash-card-title"><i class="bi bi-stars orange"></i>Latest Prediction</div>
                        <span style="font-size:.72rem;color:#94a3b8;"><?= date('d M', strtotime($latest['created_at'])) ?></span>
                    </div>
                    <div class="dash-card-body">
                        <div class="latest-pred-card mb-3">
                            <div class="lp-label">Predicted Value</div>
                            <div class="lp-price"><?= fmtPrice($latest['predicted_price']) ?></div>
                            <div class="lp-range">Range: <?= fmtPrice($latest['predicted_price'] * 0.9) ?> – <?= fmtPrice($latest['predicted_price'] * 1.1) ?></div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="lp-detail"><i class="bi bi-geo-alt" style="color:#00d4ff;"></i><span><?= htmlspecialchars($latest['location']) ?>, <strong><?= htmlspecialchars($latest['city']) ?></strong></span></div>
                            <div class="lp-detail"><i class="bi bi-building" style="color:#a855f7;"></i><span><?= htmlspecialchars($latest['property_type']) ?> · <strong><?= $latest['bedrooms'] ?> BHK</strong></span></div>
                            <div class="lp-detail"><i class="bi bi-rulers" style="color:#10b981;"></i><span><?= number_format($latest['area']) ?> sqft · <strong>₹<?= number_format($latest['predicted_price']/$latest['area'], 0) ?>/sqft</strong></span></div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="dash-card">
                    <div class="dash-card-header"><div class="dash-card-title"><i class="bi bi-stars orange"></i>Latest Prediction</div></div>
                    <div class="dash-empty"><i class="bi bi-house-add"></i><h6>None yet</h6><p>Your latest prediction will appear here</p></div>
                </div>
                <?php endif; ?>

                <!-- Model Info -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <div class="dash-card-title"><i class="bi bi-cpu-fill blue"></i>ML Model</div>
                        <span style="font-size:.7rem;background:#ecfdf5;color:#10b981;border:1px solid #bbf7d0;border-radius:20px;padding:.2rem .6rem;font-weight:700;">Active</span>
                    </div>
                    <div class="dash-card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="model-metric"><div class="model-metric-val" style="color:#1e6ff1;">0.92</div><div class="model-metric-lbl">R² Score</div></div></div>
                            <div class="col-4"><div class="model-metric"><div class="model-metric-val" style="color:#10b981;">300</div><div class="model-metric-lbl">Trees</div></div></div>
                            <div class="col-4"><div class="model-metric"><div class="model-metric-val" style="color:#f59e0b;">±8%</div><div class="model-metric-lbl">MAPE</div></div></div>
                        </div>
                        <div style="font-size:.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.6rem;">Algorithm</div>
                        <div style="display:flex;align-items:center;gap:.5rem;padding:.65rem .9rem;background:linear-gradient(135deg,#eff6ff,#f5f3ff);border-radius:10px;border:1px solid #dbeafe;">
                            <i class="bi bi-diagram-3-fill" style="color:#1e6ff1;font-size:1.1rem;"></i>
                            <div>
                                <div style="font-weight:700;color:#0f172a;font-size:.82rem;">Random Forest Regressor</div>
                                <div style="font-size:.7rem;color:#94a3b8;">Scikit-learn 1.4.2</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /col -->
        </div><!-- /row -->

    </main>
</div>

<?php
$distDataJSON = json_encode(['labels' => $distBuckets, 'counts' => $distCounts]);
$locDataJSON  = json_encode(['labels' => array_column($locRows,'location'), 'prices' => array_column($locRows,'avg_lakh')]);
$typeDataJSON = json_encode(['labels' => array_column($typeRows,'property_type'), 'counts' => array_column($typeRows,'cnt')]);

$extraScripts = "
<script src='https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js'></script>
<script>
window.__distData = $distDataJSON;
window.__locData  = $locDataJSON;
window.__typeData = $typeDataJSON;

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;

// Price Distribution
const distCtx = document.getElementById('priceDistChart');
if (distCtx && window.__distData) {
    new Chart(distCtx, {
        type: 'bar',
        data: {
            labels: window.__distData.labels,
            datasets: [{
                label: 'Predictions',
                data: window.__distData.counts,
                backgroundColor: [
                    'rgba(30,111,241,0.85)',
                    'rgba(59,130,246,0.85)',
                    'rgba(16,185,129,0.85)',
                    'rgba(168,85,247,0.85)',
                    'rgba(245,158,11,0.85)',
                    'rgba(239,68,68,0.85)',
                ],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                    grid: { color: '#f1f5f9' }, border: { display: false }
                },
                x: {
                    grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } },
                    border: { display: false }
                }
            }
        }
    });
}

// Property Type Doughnut
const typeCtx = document.getElementById('propTypeChart');
if (typeCtx && window.__typeData) {
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: window.__typeData.labels,
            datasets: [{
                data: window.__typeData.counts,
                backgroundColor: ['#1e6ff1','#10b981','#a855f7','#f59e0b','#00d4ff','#ef4444'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, font: { size: 11, weight: '600' }, color: '#64748b' }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    cornerRadius: 10,
                    padding: 12,
                }
            }
        }
    });
}

// Location Chart
const locCtx = document.getElementById('locationChart');
if (locCtx && window.__locData && window.__locData.labels.length > 0) {
    new Chart(locCtx, {
        type: 'bar',
        data: {
            labels: window.__locData.labels,
            datasets: [{
                label: 'Avg Price (₹ Lakh)',
                data: window.__locData.prices,
                backgroundColor: 'rgba(30,111,241,0.15)',
                borderColor: '#1e6ff1',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: ctx => ' ₹' + ctx.parsed.x + ' Lakh'
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } },
                    border: { display: false }
                },
                y: {
                    grid: { display: false }, ticks: { color: '#374151', font: { size: 11, weight: '600' } },
                    border: { display: false }
                }
            }
        }
    });
}

// Sidebar toggle
const sidebar = document.getElementById('hpSidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('sidebarToggle');
if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });
}
if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
}

// Delete prediction
async function deletePrediction(id, btn) {
    if (!confirm('Delete this prediction?')) return;
    try {
        const fd = new FormData();
        fd.append('action','delete'); fd.append('id', id);
        const res  = await fetch('/house-price-prediction/api/dashboard.php', { method:'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            const row = btn.closest('tr');
            row.style.transition = 'opacity .3s, transform .3s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => row.remove(), 300);
        }
    } catch(e) { alert('Failed to delete.'); }
}
</script>
";
require_once __DIR__ . '/includes/footer.php';
?>
