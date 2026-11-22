<?php
/**
 * compare.php — Compare Two Properties
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$user = currentUser();
$db   = getDB();

$stmt = $db->prepare('SELECT id, city, location, property_type, area, bedrooms, predicted_price, created_at FROM predictions WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$predictions = $stmt->fetchAll();

function fmtPrice(float $n): string {
    if ($n >= 10000000) return '₹' . round($n / 10000000, 2) . ' Cr';
    if ($n >= 100000)   return '₹' . round($n / 100000, 1)  . ' L';
    return '₹' . number_format($n, 0);
}

$pageTitle = 'Compare Properties';
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="bi bi-list"></i></button>

    <main class="main-content" style="padding-top:calc(var(--navbar-h) + 1.5rem);">

        <div class="mb-4">
            <h1 class="fw-800 text-navy mb-1" style="font-size:1.6rem;">
                <i class="bi bi-arrow-left-right me-2 text-blue"></i>Compare Properties
            </h1>
            <p class="text-muted small mb-0">Select two predictions to compare side by side.</p>
        </div>

        <?php if (count($predictions) < 2): ?>
        <div class="hp-card">
            <div class="empty-state">
                <i class="bi bi-arrow-left-right"></i>
                <h5>Need at least 2 predictions</h5>
                <p>Make at least two property predictions to compare them.</p>
                <a href="/house-price-prediction/predict.php" class="btn btn-accent btn-sm mt-2">
                    <i class="bi bi-magic me-2"></i>Predict Now
                </a>
            </div>
        </div>
        <?php else: ?>

        <!-- Selectors -->
        <div class="row g-4 mb-4">
            <?php foreach([1,2] as $slot): ?>
            <div class="col-md-6">
                <div class="hp-card">
                    <div class="card-header">
                        <i class="bi bi-house-<?= $slot === 1 ? 'fill' : 'door-open' ?> me-2 text-blue"></i>
                        Property <?= $slot ?>
                    </div>
                    <div class="card-body">
                        <label class="small fw-600 text-muted mb-2 d-block">SELECT PREDICTION</label>
                        <select class="form-select compare-select" data-slot="<?= $slot ?>">
                            <option value="">-- Select a Prediction --</option>
                            <?php foreach ($predictions as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                #<?= $p['id'] ?> — <?= htmlspecialchars($p['property_type']) ?>,
                                <?= htmlspecialchars($p['location']) ?>, <?= htmlspecialchars($p['city']) ?>
                                (<?= fmtPrice($p['predicted_price']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Compare Cards -->
        <div class="row g-4">
            <div class="col-md-6" id="comparePanel1">
                <div class="hp-card">
                    <div class="empty-state py-4">
                        <i class="bi bi-house" style="font-size:2.5rem;"></i>
                        <p class="mt-2 mb-0 small">Select Property 1 above</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" id="comparePanel2">
                <div class="hp-card">
                    <div class="empty-state py-4">
                        <i class="bi bi-house-door" style="font-size:2.5rem;"></i>
                        <p class="mt-2 mb-0 small">Select Property 2 above</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- VS badge -->
        <div class="text-center my-n3 position-relative" style="z-index:10;">
            <span class="badge rounded-pill px-4 py-2 fw-800 fs-6"
                  style="background:linear-gradient(135deg,var(--blue),var(--accent));box-shadow:var(--shadow-md);">
                VS
            </span>
        </div>

        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
