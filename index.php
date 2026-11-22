<?php
/**
 * index.php — HousePrice AI Landing Page
 */
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Hero ─────────────────────────────────────────────────── -->
<section class="hp-hero">
    <div class="container">
        <div class="row align-items-center min-vh-100 py-5">
            <div class="col-lg-7 col-xl-6">
                <div class="hero-badge fade-in-up">
                    <i class="bi bi-stars"></i>
                    AI-Powered Real Estate Intelligence
                </div>
                <h1 class="hero-title fade-in-up delay-1">
                    Predict Your<br>
                    <span class="highlight">Dream Home's</span><br>
                    Value with AI
                </h1>
                <p class="hero-subtitle fade-in-up delay-2">
                    Leverage the power of Machine Learning to get accurate,
                    data-driven property price predictions for 10+ major
                    Indian cities in seconds.
                </p>
                <div class="d-flex flex-wrap gap-3 fade-in-up delay-3">
                    <a href="<?= isLoggedIn() ? '/house-price-prediction/predict.php' : '/house-price-prediction/login.php' ?>"
                       class="btn btn-hero-primary">
                        <i class="bi bi-magic me-2"></i>Predict House Price
                    </a>
                    <a href="#features" class="btn btn-hero-secondary">
                        <i class="bi bi-play-circle me-2"></i>Explore Features
                    </a>
                </div>

                <div class="hero-stats fade-in-up delay-4">
                    <div>
                        <div class="hero-stat-value" data-counter="120">120+</div>
                        <div class="hero-stat-label">Training Samples</div>
                    </div>
                    <div>
                        <div class="hero-stat-value" data-counter="10">10</div>
                        <div class="hero-stat-label">Major Cities</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">~92%</div>
                        <div class="hero-stat-label">Model Accuracy</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">&lt;1s</div>
                        <div class="hero-stat-label">Prediction Time</div>
                    </div>
                </div>
            </div>

            <!-- Hero card widget -->
            <div class="col-lg-5 col-xl-6 d-none d-lg-block">
                <div class="fade-in-up delay-3" style="padding:2rem;">
                    <div class="hp-card p-4 ms-auto" style="max-width:360px;">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="brand-icon"><i class="bi bi-houses-fill"></i></div>
                            <div>
                                <div class="fw-700 text-navy">Live AI Prediction</div>
                                <div class="small text-muted">Random Forest Model</div>
                            </div>
                        </div>
                        <div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:12px;padding:1.5rem;color:#fff;margin-bottom:1rem;">
                            <div class="opacity-60 small mb-1">Estimated Value</div>
                            <div style="font-size:2rem;font-weight:800;color:var(--accent);">₹82,50,000</div>
                            <div class="opacity-60 small mt-1">Range: ₹74L – ₹91L</div>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6"><div class="tag blue w-100 justify-content-center"><i class="bi bi-geo-alt"></i>Bangalore</div></div>
                            <div class="col-6"><div class="tag green w-100 justify-content-center"><i class="bi bi-building"></i>Apartment</div></div>
                            <div class="col-6"><div class="tag w-100 justify-content-center"><i class="bi bi-door-open"></i>3 BHK</div></div>
                            <div class="col-6"><div class="tag orange w-100 justify-content-center"><i class="bi bi-rulers"></i>1200 sqft</div></div>
                        </div>
                        <div class="mt-3 pt-3 border-top d-flex align-items-center gap-2">
                            <span class="badge rounded-pill" style="background:var(--green-pale);color:var(--green);">
                                <i class="bi bi-shield-check me-1"></i>AI Verified
                            </span>
                            <span class="small text-muted ms-auto">R² Score: 0.92</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── How It Works ─────────────────────────────────────────── -->
<section class="py-6 bg-white" id="how-it-works" style="padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge"><i class="bi bi-diagram-3"></i>Workflow</div>
            <h2 class="section-title">How It <span>Works</span></h2>
            <p class="section-sub mx-auto">
                A seamless pipeline from your input to an AI-powered price prediction.
            </p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="workflow-steps">
                    <?php
                    $steps = [
                        ['bi-person-fill-gear', 'User Input',      'Enter property details: city, area, bedrooms, age, etc.'],
                        ['bi-filetype-php',     'PHP Backend',     'PHP validates and sanitizes all inputs securely.'],
                        ['bi-shield-check',     'Data Validation', 'Server-side validation ensures clean, safe data.'],
                        ['bi-filetype-py',      'Python ML Model', 'Preprocessed data is fed to the Random Forest model.'],
                        ['bi-stars',            'Price Prediction','Model returns predicted price with confidence range.'],
                        ['bi-database',         'MySQL Database',  'Result is stored in the database for future reference.'],
                        ['bi-display',          'Display Result',  'Beautiful result card shown to the user instantly.'],
                    ];
                    foreach ($steps as [$icon, $title, $desc]):
                    ?>
                    <div class="workflow-step">
                        <i class="bi <?= $icon ?> me-2 text-blue"></i>
                        <strong><?= $title ?></strong>
                        <div class="text-muted small mt-1"><?= $desc ?></div>
                    </div>
                    <?php if ($title !== 'Display Result'): ?>
                    <div class="workflow-arrow"><i class="bi bi-arrow-down-circle-fill"></i></div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center">
                <div class="p-4 w-100">
                    <div class="hp-card p-4">
                        <h5 class="fw-700 mb-4 text-navy"><i class="bi bi-bar-chart-line me-2 text-blue"></i>ML Pipeline</h5>
                        <div class="d-flex flex-column gap-2">
                            <?php
                            $pipeline = [
                                ['Dataset Loading',     '100%', 'blue'],
                                ['Data Preprocessing',  '100%', 'blue'],
                                ['Feature Engineering', '100%', 'blue'],
                                ['Model Training',      '100%', 'green'],
                                ['Model Evaluation',    '100%', 'green'],
                                ['Prediction Output',   '100%', 'accent'],
                            ];
                            foreach ($pipeline as [$label, $pct, $color]):
                            ?>
                            <div>
                                <div class="d-flex justify-content-between small fw-500 mb-1">
                                    <span><?= $label ?></span><span><?= $pct ?></span>
                                </div>
                                <div class="progress" style="height:6px;border-radius:3px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width:<?= $pct ?>;background:var(--<?= $color ?>);border-radius:3px;"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <div class="fw-800 text-blue fs-5">0.92</div>
                                    <div class="small text-muted">R² Score</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-800 text-green fs-5">±8%</div>
                                    <div class="small text-muted">Error Rate</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-800 text-accent-dark fs-5">300</div>
                                    <div class="small text-muted">Trees</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Features ─────────────────────────────────────────────── -->
<section class="py-6" id="features" style="padding:5rem 0;background:var(--gray-50);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge"><i class="bi bi-lightning-charge"></i>Features</div>
            <h2 class="section-title">Everything You <span>Need</span></h2>
            <p class="section-sub mx-auto">
                A complete real estate intelligence platform built for accuracy and ease of use.
            </p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['blue',   'bi-magic',        'AI Prediction',       'Random Forest ML model trained on real Indian real estate data.'],
                ['green',  'bi-shield-check',  'Secure Auth',         'Password hashing, session management, and PDO prepared statements.'],
                ['orange', 'bi-bar-chart-line','Analytics Dashboard', 'Visualize price trends, distributions, and comparisons with Chart.js.'],
                ['navy',   'bi-clock-history', 'Prediction History',  'Save, browse, and search all your previous property predictions.'],
                ['accent', 'bi-arrow-left-right','Compare Properties','Side-by-side comparison of two saved property predictions.'],
                ['blue',   'bi-phone',         'Responsive Design',   'Works beautifully on desktop, tablet, and mobile devices.'],
                ['green',  'bi-geo-alt',       '10+ Cities',          'Covers Mumbai, Delhi, Bangalore, Hyderabad, Chennai, Pune & more.'],
                ['orange', 'bi-graph-up',      'Price Range',         'Provides optimistic and conservative price range estimates.'],
            ];
            foreach ($features as [$color, $icon, $title, $desc]):
            ?>
            <div class="col-sm-6 col-lg-3" data-reveal>
                <div class="feature-card">
                    <div class="feature-icon <?= $color ?>">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <h5><?= $title ?></h5>
                    <p><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── About / CTA ──────────────────────────────────────────── -->
<section style="padding:5rem 0;background:linear-gradient(135deg,var(--navy),var(--navy-light));">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-reveal>
                <div class="section-badge" style="background:rgba(30,111,241,.2);color:var(--accent);border:1px solid rgba(30,111,241,.3);">
                    <i class="bi bi-mortarboard"></i>College PBL Project
                </div>
                <h2 class="section-title text-white mt-2">About <span style="color:var(--accent)">HousePrice AI</span></h2>
                <p class="text-light opacity-75" style="line-height:1.8;">
                    This project demonstrates the integration of Machine Learning
                    with a full-stack PHP web application. Using a Random Forest
                    Regression model trained on real estate data, it predicts
                    property prices across major Indian cities with high accuracy.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php foreach(['Python', 'Scikit-learn', 'PHP', 'MySQL', 'Bootstrap 5', 'Chart.js', 'Random Forest'] as $t): ?>
                    <span class="tech-badge"><i class="bi bi-check2 me-1"></i><?= $t ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4">
                    <a href="/house-price-prediction/about.php" class="btn btn-hero-secondary">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-reveal>
                <div class="row g-3">
                    <?php
                    $stats = [
                        ['bi-database',         '120+',  'Data Samples',     'blue'],
                        ['bi-diagram-3',        '300',   'Forest Trees',     'green'],
                        ['bi-geo-alt',          '10',    'Cities Covered',   'orange'],
                        ['bi-speedometer2',     '~92%',  'R² Accuracy',      'accent'],
                    ];
                    foreach ($stats as [$icon, $val, $label, $color]):
                    ?>
                    <div class="col-6">
                        <div class="metric-card">
                            <i class="bi <?= $icon ?> text-<?= $color ?> fs-2 mb-2 d-block"></i>
                            <div class="metric-value text-<?= $color ?>"><?= $val ?></div>
                            <div class="metric-label"><?= $label ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA Banner ───────────────────────────────────────────── -->
<section style="padding:4rem 0;background:var(--white);border-top:1px solid var(--gray-200);">
    <div class="container text-center">
        <h2 class="fw-800 text-navy mb-3">Ready to Predict Your Property's Value?</h2>
        <p class="text-muted mb-4">Get an AI-powered estimate in under 1 second.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <?php if (isLoggedIn()): ?>
            <a href="/house-price-prediction/predict.php" class="btn btn-hero-primary">
                <i class="bi bi-magic me-2"></i>Start Predicting
            </a>
            <a href="/house-price-prediction/dashboard.php" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-grid me-2"></i>My Dashboard
            </a>
            <?php else: ?>
            <a href="/house-price-prediction/register.php" class="btn btn-hero-primary">
                <i class="bi bi-person-plus me-2"></i>Create Free Account
            </a>
            <a href="/house-price-prediction/login.php" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
