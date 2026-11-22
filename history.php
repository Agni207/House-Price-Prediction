<?php
/**
 * history.php — Prediction History
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$user = currentUser();
$db   = getDB();

$stmt = $db->prepare('SELECT * FROM predictions WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();

function fmtPrice(float $n): string {
    if ($n >= 10000000) return '₹' . round($n / 10000000, 2) . ' Cr';
    if ($n >= 100000)   return '₹' . round($n / 100000, 1)  . ' L';
    return '₹' . number_format($n, 0);
}

$pageTitle = 'Prediction History';
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="bi bi-list"></i></button>

    <main class="main-content" style="padding-top:calc(var(--navbar-h) + 1.5rem);">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h1 class="fw-800 text-navy mb-1" style="font-size:1.6rem;">
                    <i class="bi bi-clock-history me-2 text-blue"></i>Prediction History
                </h1>
                <p class="text-muted small mb-0">
                    All your saved property predictions (<?= count($rows) ?> total).
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/house-price-prediction/compare.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left-right me-1"></i>Compare
                </a>
                <a href="/house-price-prediction/predict.php" class="btn btn-accent btn-sm px-3">
                    <i class="bi bi-magic me-1"></i>New Prediction
                </a>
            </div>
        </div>

        <!-- Search -->
        <div class="hp-card mb-4">
            <div class="card-body py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="historySearch" class="form-control"
                                   placeholder="Search by city, location, type, price…">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="small text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Showing <?= count($rows) ?> prediction<?= count($rows) !== 1 ? 's' : '' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="hp-card">
            <div class="card-body p-0">
                <?php if (count($rows) > 0): ?>
                <div class="table-responsive">
                    <table class="history-table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Property Type</th>
                                <th>City</th>
                                <th>Location</th>
                                <th>Area</th>
                                <th>BHK</th>
                                <th>Bath</th>
                                <th>Age</th>
                                <th>Furnishing</th>
                                <th>Condition</th>
                                <th>Predicted Price</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $i => $row): ?>
                            <tr class="history-row">
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <span class="tag blue"><?= htmlspecialchars($row['property_type']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['city']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= number_format($row['area']) ?> sqft</td>
                                <td><?= $row['bedrooms'] ?> BHK</td>
                                <td><?= $row['bathrooms'] ?></td>
                                <td><?= $row['property_age'] ?> yr</td>
                                <td><span class="tag"><?= htmlspecialchars($row['furnishing']) ?></span></td>
                                <td>
                                    <?php
                                    $condColors = ['Excellent'=>'green','Good'=>'blue','Average'=>'orange','Poor'=>'orange'];
                                    $cc = $condColors[$row['condition_type']] ?? '';
                                    ?>
                                    <span class="tag <?= $cc ?>"><?= htmlspecialchars($row['condition_type']) ?></span>
                                </td>
                                <td>
                                    <span class="price-badge"><?= fmtPrice($row['predicted_price']) ?></span>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                title="View Details"
                                                data-prediction="<?= htmlspecialchars(json_encode($row, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="openHistoryDetail(this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                                title="Delete"
                                                onclick="deletePrediction(<?= $row['id'] ?>, this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-house-slash"></i>
                    <h5>No predictions yet</h5>
                    <p>Your prediction history will appear here after your first prediction.</p>
                    <a href="/house-price-prediction/predict.php" class="btn btn-accent btn-sm mt-2">
                        <i class="bi bi-magic me-2"></i>Make First Prediction
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));color:#fff;">
                <h5 class="modal-title fw-700">
                    <i class="bi bi-house me-2"></i>Property Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailModalBody">
                <!-- Filled by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
