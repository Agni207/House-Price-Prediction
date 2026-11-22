<?php
/**
 * about.php — About the Project
 */
$pageTitle = 'About the Project';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="about-hero pt-5 mt-5" style="padding-bottom:4rem;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="section-badge" style="background:rgba(30,111,241,.2);color:var(--accent);border:1px solid rgba(30,111,241,.3);">
                    <i class="bi bi-mortarboard"></i>College PBL Project
                </div>
                <h1 class="fw-800 mt-3 mb-4" style="font-size:clamp(2rem,4vw,3rem);">
                    AI-Based House Price<br>
                    <span style="color:var(--accent);">Prediction System</span>
                </h1>
                <p class="text-light opacity-75" style="max-width:560px;line-height:1.8;">
                    A full-stack web application that leverages Machine Learning
                    to predict residential and commercial property prices across
                    major Indian cities based on property characteristics.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php
                    $techs = ['Python 3','Scikit-learn','Pandas','NumPy','Core PHP','MySQL','Bootstrap 5','Chart.js','PDO','XAMPP'];
                    foreach ($techs as $t):
                    ?>
                    <span class="tech-badge"><i class="bi bi-check2 me-1"></i><?= $t ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <?php
                    $metrics = [
                        ['bi-database',     '120+',  'Training Samples', 'accent'],
                        ['bi-diagram-3',    '300',   'Forest Trees',     'accent'],
                        ['bi-geo-alt',      '10+',   'Cities',           'accent'],
                        ['bi-speedometer2', '~92%',  'Accuracy',         'accent'],
                    ];
                    foreach ($metrics as [$icon, $val, $label, $color]):
                    ?>
                    <div class="col-6">
                        <div class="metric-card">
                            <i class="bi <?= $icon ?> mb-2 d-block text-<?= $color ?>" style="font-size:1.75rem;"></i>
                            <div class="metric-value"><?= $val ?></div>
                            <div class="metric-label"><?= $label ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<div class="container py-5">

    <!-- What is House Price Prediction -->
    <div class="row g-5 mb-5 align-items-center">
        <div class="col-lg-6" data-reveal>
            <div class="section-badge"><i class="bi bi-question-circle"></i>Overview</div>
            <h2 class="section-title mt-2">What is House<br><span>Price Prediction?</span></h2>
            <p class="text-muted" style="line-height:1.8;">
                House Price Prediction is the process of using historical data
                and statistical models to estimate the market value of a property.
                Traditional methods relied on manual appraisals by real estate
                agents, which were subjective and time-consuming.
            </p>
            <p class="text-muted" style="line-height:1.8;">
                Modern AI/ML approaches use features like location, size, age,
                and amenities to deliver fast, data-driven, and objective estimates
                that are consistent and reproducible.
            </p>
        </div>
        <div class="col-lg-6" data-reveal>
            <div class="hp-card p-4">
                <h5 class="fw-700 text-navy mb-3"><i class="bi bi-lightbulb text-orange me-2"></i>Why AI in Real Estate?</h5>
                <?php
                $reasons = [
                    ['bi-lightning-charge text-blue',   'Speed',          '< 1 second prediction vs hours of manual appraisal.'],
                    ['bi-shield-check text-green',       'Objectivity',    'Data-driven results free from human bias.'],
                    ['bi-graph-up text-orange',          'Accuracy',       '~92% R² score on test data using Random Forest.'],
                    ['bi-arrow-repeat text-blue',        'Consistency',    'Same inputs always yield the same output.'],
                    ['bi-database text-accent-dark',     'Scalability',    'Can analyze thousands of properties simultaneously.'],
                ];
                foreach ($reasons as [$icon, $title, $desc]):
                ?>
                <div class="d-flex gap-3 mb-3">
                    <i class="bi <?= $icon ?> fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <div class="fw-600"><?= $title ?></div>
                        <div class="small text-muted"><?= $desc ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ML Algorithm -->
    <div class="hp-card mb-5 p-4 p-lg-5">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="section-badge"><i class="bi bi-diagram-3"></i>Algorithm</div>
                <h2 class="section-title mt-2">Random Forest<br><span>Regression</span></h2>
                <p class="text-muted" style="line-height:1.8;">
                    A <strong>Random Forest</strong> is an ensemble learning method
                    that builds multiple decision trees during training and outputs
                    the average prediction of all trees. This reduces overfitting
                    and improves generalization.
                </p>
                <div class="row g-3 mt-2">
                    <?php
                    $params = [
                        ['n_estimators', '300', 'Number of trees'],
                        ['max_depth', 'None', 'Tree depth (auto)'],
                        ['random_state', '42', 'Reproducibility'],
                        ['n_jobs', '-1', 'All CPU cores'],
                    ];
                    foreach ($params as [$param, $val, $desc]):
                    ?>
                    <div class="col-6">
                        <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:.85rem;">
                            <code class="text-blue small"><?= $param ?></code>
                            <div class="fw-700 text-navy"><?= $val ?></div>
                            <div class="small text-muted"><?= $desc ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <h5 class="fw-700 text-navy mb-4"><i class="bi bi-bar-chart me-2 text-blue"></i>Model Performance</h5>
                <?php
                $metrics_list = [
                    ['R² Score',                '~0.92',       'var(--green)'],
                    ['Mean Absolute Error',      '~₹3.2 Lakh',  'var(--blue)'],
                    ['Root Mean Squared Error',  '~₹5.8 Lakh',  'var(--orange)'],
                    ['Training Samples',         '120+',        'var(--blue)'],
                    ['Test Split',               '20%',         'var(--gray-500)'],
                    ['Features Used',            '13',          'var(--blue)'],
                ];
                foreach ($metrics_list as [$label, $val, $color]):
                ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small"><?= $label ?></span>
                    <span class="fw-700" style="color:<?= $color ?>"><?= $val ?></span>
                </div>
                <?php endforeach; ?>

                <div class="mt-4 pt-2">
                    <h6 class="fw-700 text-muted small mb-3" style="letter-spacing:.06em;text-transform:uppercase;">
                        Feature Importance (Approximate)
                    </h6>
                    <?php
                    $features = [
                        ['Location',      85, 'blue'],
                        ['Area',          78, 'blue'],
                        ['City',          72, 'green'],
                        ['Bedrooms',      55, 'orange'],
                        ['Furnishing',    45, 'orange'],
                        ['Property Age',  42, 'orange'],
                        ['Condition',     38, 'blue'],
                    ];
                    foreach ($features as [$feat, $pct, $color]):
                    ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small fw-500 mb-1">
                            <span><?= $feat ?></span><span><?= $pct ?>%</span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= $pct ?>%;background:var(--<?= $color ?>);border-radius:3px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dataset & Pipeline -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6" data-reveal>
            <div class="hp-card p-4 h-100">
                <h5 class="fw-700 text-navy mb-3"><i class="bi bi-table me-2 text-blue"></i>Dataset Information</h5>
                <?php
                $dsInfo = [
                    ['Source',        'Custom synthetic + public data'],
                    ['Records',       '120+ property entries'],
                    ['Cities',        '10 major Indian cities'],
                    ['Features',      '11 input features'],
                    ['Target',        'Property price (INR)'],
                    ['Price Range',   '₹3 Lakh – ₹12 Crore'],
                    ['File Format',   'CSV (dataset.csv)'],
                ];
                foreach ($dsInfo as [$k, $v]):
                ?>
                <div class="d-flex gap-3 py-2 border-bottom align-items-start">
                    <span class="text-muted small" style="min-width:120px;"><?= $k ?></span>
                    <span class="fw-600 small text-navy"><?= $v ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6" data-reveal>
            <div class="hp-card p-4 h-100">
                <h5 class="fw-700 text-navy mb-3"><i class="bi bi-gear me-2 text-blue"></i>ML Pipeline Steps</h5>
                <?php
                $pipeline = [
                    'Load dataset (CSV)',
                    'Drop nulls & duplicates',
                    'IQR outlier removal',
                    'Feature engineering (total_rooms, area_per_bedroom)',
                    'OneHotEncoder for categorical features',
                    'StandardScaler for numerical features',
                    'ColumnTransformer pipeline',
                    'Train RandomForestRegressor (300 trees)',
                    'Evaluate: MAE, RMSE, R²',
                    'Serialize model with joblib',
                    'Load & predict via predict.py',
                ];
                foreach ($pipeline as $i => $step):
                ?>
                <div class="d-flex gap-3 py-2 border-bottom align-items-center">
                    <span class="badge rounded-pill"
                          style="background:linear-gradient(135deg,var(--blue),var(--accent));min-width:28px;">
                        <?= $i + 1 ?>
                    </span>
                    <span class="small text-navy"><?= $step ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Objectives & Benefits -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6" data-reveal>
            <div class="hp-card p-4 h-100">
                <h5 class="fw-700 text-navy mb-3"><i class="bi bi-bullseye me-2 text-orange"></i>Project Objectives</h5>
                <?php
                $objectives = [
                    'Demonstrate ML integration in a real PHP web application.',
                    'Provide accurate, data-driven house price estimates.',
                    'Build a secure, session-based multi-user system.',
                    'Visualize prediction analytics with interactive charts.',
                    'Implement secure PHP↔Python communication.',
                    'Apply best practices: PDO, password hashing, validation.',
                ];
                foreach ($objectives as $obj):
                ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="bi bi-check2-circle text-green mt-1 flex-shrink-0"></i>
                    <span class="small text-muted"><?= $obj ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6" data-reveal>
            <div class="hp-card p-4 h-100">
                <h5 class="fw-700 text-navy mb-3"><i class="bi bi-exclamation-triangle me-2 text-orange"></i>Limitations</h5>
                <?php
                $limits = [
                    'Dataset size (120+ records) may limit generalization.',
                    'Does not account for real-time market fluctuations.',
                    'Location granularity is at neighborhood level.',
                    'Requires Python & Scikit-learn installed on server.',
                    'Predictions are estimates, not professional appraisals.',
                    'Model may drift without periodic retraining.',
                ];
                foreach ($limits as $lim):
                ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="bi bi-exclamation-circle text-orange mt-1 flex-shrink-0"></i>
                    <span class="small text-muted"><?= $lim ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Project Team / Contributors -->
    <div class="row g-4 mb-5" data-reveal>
        <div class="col-12">
            <div class="hp-card p-4">
                <h5 class="fw-700 text-navy mb-4"><i class="bi bi-people-fill me-2 text-blue"></i>Project Team & Contributors</h5>
                <div class="row g-3">
                    <?php
                    $team = [
                        ['1', 'Agnibha Dey', 'Project Lead, Full-Stack Architecture, ML Pipeline Integration', 'bi-person-badge'],
                        ['2', 'Ranjan Mandal', 'Machine Learning Research, Data Engineering & Model Optimization', 'bi-cpu'],
                        ['3', 'Krishna Garai', 'Backend Development, Database Schema Design & API Bridging', 'bi-database-gear'],
                        ['4', 'Bidyapriya Sahoo', 'Frontend UI/UX Engineering, Data Visualizations & Testing', 'bi-layout-text-window-reverse'],
                    ];
                    foreach ($team as [$num, $name, $role, $icon]):
                    ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary rounded-pill"><?= $num ?></span>
                                <i class="bi <?= $icon ?> text-primary fs-5"></i>
                                <span class="fw-700 text-navy"><?= $name ?></span>
                            </div>
                            <p class="text-muted small mb-0"><?= $role ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
