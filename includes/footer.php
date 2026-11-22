<?php
/**
 * includes/footer.php
 */
?>
</main><!-- /.main-content or /.container -->

<footer class="hp-footer py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon-sm"><i class="bi bi-houses-fill"></i></div>
                    <span class="fw-700 fs-5 text-white">HousePrice <span class="text-accent">AI</span></span>
                </div>
                <p class="text-light opacity-75 small">
                    An AI-powered house price prediction system built with
                    Python Machine Learning and PHP backend for accurate
                    real estate valuation.
                </p>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="text-white fw-600 mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/house-price-prediction/index.php" class="footer-link">Home</a></li>
                    <li class="mb-2"><a href="/house-price-prediction/predict.php" class="footer-link">Predict</a></li>
                    <li class="mb-2"><a href="/house-price-prediction/about.php" class="footer-link">About</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-6">
                <h6 class="text-white fw-600 mb-3">Technology</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><span class="footer-tech"><i class="bi bi-filetype-py me-1"></i>Python & Scikit-learn</span></li>
                    <li class="mb-2"><span class="footer-tech"><i class="bi bi-filetype-php me-1"></i>Core PHP & MySQL</span></li>
                    <li class="mb-2"><span class="footer-tech"><i class="bi bi-bootstrap me-1"></i>Bootstrap 5</span></li>
                    <li class="mb-2"><span class="footer-tech"><i class="bi bi-graph-up me-1"></i>Chart.js</span></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="text-white fw-600 mb-3">ML Algorithm</h6>
                <div class="algo-badge mb-2">
                    <i class="bi bi-diagram-3 me-2"></i>Random Forest Regression
                </div>
                <p class="text-light opacity-75 small mt-2 mb-0">
                    Trained on 120+ Indian real estate data points across 10 major cities.
                </p>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-light opacity-50 small mb-0">
                    &copy; <?= date('Y') ?> HousePrice AI — College PBL Project
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-light opacity-50 small mb-0">
                    Built with <i class="bi bi-heart-fill text-danger"></i> using PHP + Python + ML
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="/house-price-prediction/assets/js/script.js"></script>
<?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
