/* ============================================================
   HousePrice AI — Main JavaScript
   script.js
   ============================================================ */

'use strict';

/* ── 1. Sidebar Toggle ────────────────────────────────────── */
function initSidebar() {
    const sidebar  = document.getElementById('hpSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (!sidebar) return;

    function openSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', () => {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (overlay) overlay.addEventListener('click', closeSidebar);
}

/* ── 2. Navbar scroll effect ──────────────────────────────── */
function initNavbarScroll() {
    const navbar = document.querySelector('.hp-navbar');
    if (!navbar) return;
    window.addEventListener('scroll', () => {
        navbar.style.boxShadow = window.scrollY > 10
            ? '0 4px 20px rgba(0,0,0,.25)'
            : '';
    }, { passive: true });
}

/* ── 3. Prediction Form ───────────────────────────────────── */
function initPredictForm() {
    const form      = document.getElementById('predictForm');
    const resultDiv = document.getElementById('predictionResult');
    const submitBtn = document.getElementById('predictBtn');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // UI: loading state
        const btnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="hp-spinner me-2"></span>Analyzing with AI…';

        if (resultDiv) {
            resultDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="hp-spinner" style="width:48px;height:48px;border-color:rgba(30,111,241,.2);border-top-color:var(--blue)"></div>
                    <p class="mt-3 text-muted">Running ML Model…</p>
                </div>`;
            resultDiv.style.display = 'block';
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        try {
            const formData = new FormData(form);
            const res = await fetch('/house-price-prediction/api/predict.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                displayResult(data);
            } else {
                showResultError(data.error || 'Prediction failed. Please try again.');
            }
        } catch (err) {
            showResultError('Network error. Please check your connection.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = btnText;
        }
    });

    function displayResult(d) {
        const fmt    = n => '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
        const fmtSF  = n => '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 }) + '/sq.ft';

        const city    = document.getElementById('city')?.value || '—';
        const loc     = document.getElementById('location')?.value || '—';
        const type    = document.querySelector('input[name="property_type"]:checked')?.value || document.getElementById('propTypeHidden')?.value || 'Apartment';
        const area    = document.getElementById('area')?.value || d.area || '—';
        const beds    = document.getElementById('bedrooms')?.value || '—';
        const baths   = document.getElementById('bathrooms')?.value || '—';
        const floors  = document.getElementById('floors')?.value || '1';
        const age     = document.getElementById('property_age')?.value || '0';
        const parkVal = document.getElementById('parking')?.value;
        const park    = parkVal === '0' ? 'No Parking' : (parkVal === '1' ? '1 Spot' : (parkVal ? parkVal + ' Spots' : 'No'));
        const furn    = document.getElementById('furnishing')?.value || '—';
        const cond    = document.getElementById('condition')?.value || '—';

        if (resultDiv) {
            resultDiv.innerHTML = `
<div class="result-card fade-in-up" id="resultCardEl">
    <div class="position-relative" style="z-index:1">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="brand-icon"><i class="bi bi-stars"></i></div>
            <span class="fw-700 fs-5">AI Prediction Result</span>
        </div>

        <div class="result-label mb-1">Estimated Market Value</div>
        <div class="result-price mb-2">${fmt(d.predicted_price)}</div>
        <div class="result-range mb-4">
            <i class="bi bi-arrow-left-right me-1"></i>
            Price Range: ${fmt(d.price_low)} – ${fmt(d.price_high)}
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="result-per-sqft">
                    <div class="result-label mb-1">Per Sq. Ft</div>
                    <div class="fw-700 fs-6 text-white">${fmtSF(d.price_per_sqft)}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="result-per-sqft">
                    <div class="result-label mb-1">Total Area</div>
                    <div class="fw-700 fs-6 text-white">${Number(d.area || area).toLocaleString('en-IN')} sq.ft</div>
                </div>
            </div>
        </div>

        <h6 class="fw-700 mb-3 opacity-75" style="font-size:.8rem;letter-spacing:.06em;text-transform:uppercase">Property Summary</h6>
        <div class="row g-2 mb-4">
            ${[
                ['bi-geo-alt','Location', loc + ', ' + city],
                ['bi-building','Type', type],
                ['bi-door-open','Bedrooms', beds + (beds !== '—' && !String(beds).includes('BHK') ? ' BHK' : '')],
                ['bi-droplet','Bathrooms', baths],
                ['bi-layers','Floors', floors + ' Floor' + (floors > 1 ? 's' : '')],
                ['bi-calendar','Property Age', age + ' yrs old'],
                ['bi-car-front','Parking', park],
                ['bi-house-gear','Furnishing', furn],
                ['bi-star','Condition', cond],
            ].map(([icon, label, val]) =>
                `<div class="col-6 col-md-4">
                    <div class="result-detail-item">
                        <i class="bi ${icon} opacity-75"></i>
                        <div>
                            <div class="opacity-60" style="font-size:.72rem">${label}</div>
                            <div class="fw-600" style="font-size:.88rem">${val}</div>
                        </div>
                    </div>
                </div>`
            ).join('')}
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-light btn-sm fw-600 flex-fill" onclick="resetFormForNewPrediction()">
                <i class="bi bi-arrow-counterclockwise me-1"></i>New Prediction
            </button>
            <button class="btn btn-sm fw-600 flex-fill" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)"
                onclick="savePrediction(${d.prediction_id || 0})">
                <i class="bi bi-check-circle-fill text-success me-1"></i>Saved to History
            </button>
        </div>

        <div class="mt-3 pt-3 border-top border-secondary opacity-50 small text-center">
            <i class="bi bi-info-circle me-1"></i>
            Trained Random Forest ML Regressor · Accuracy ~${d.model_r2 ? (d.model_r2 * 100).toFixed(1) : '93.7'}% (R²)
        </div>
    </div>
</div>`;
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function showResultError(msg) {
        if (resultDiv) {
            resultDiv.innerHTML = `
                <div class="alert-hp error p-3 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><strong>Prediction Error</strong><br><small>${escHtml(msg)}</small></div>
                </div>`;
        }
    }
}

function savePrediction(id) {
    // Already saved server-side; just show a toast
    showToast('Prediction saved to your history!', 'success');
}

function resetFormForNewPrediction() {
    const form = document.getElementById('predictForm');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        const header = document.querySelector('.predict-form-header');
        if (header) {
            header.style.transition = 'box-shadow 0.3s ease';
            header.style.boxShadow = '0 0 25px rgba(30,111,241,0.5)';
            setTimeout(() => { header.style.boxShadow = ''; }, 1200);
        }
    }
}

/* ── 4. Dashboard Charts ──────────────────────────────────── */
function initDashboardCharts() {
    if (typeof Chart === 'undefined') return;

    // Price distribution (bar)
    const distCtx = document.getElementById('priceDistChart');
    if (distCtx && window.__distData) {
        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: window.__distData.labels,
                datasets: [{
                    label: 'Number of Predictions',
                    data:  window.__distData.counts,
                    backgroundColor: [
                        'rgba(30,111,241,.8)',
                        'rgba(0,198,255,.8)',
                        'rgba(16,185,129,.8)',
                        'rgba(245,158,11,.8)',
                        'rgba(239,68,68,.8)',
                    ],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Location wise (horizontal bar)
    const locCtx = document.getElementById('locationChart');
    if (locCtx && window.__locData) {
        new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: window.__locData.labels,
                datasets: [{
                    label: 'Avg Price (₹ Lakh)',
                    data:  window.__locData.prices,
                    backgroundColor: 'rgba(30,111,241,.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(0,0,0,.05)' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // Property type (doughnut)
    const typeCtx = document.getElementById('propTypeChart');
    if (typeCtx && window.__typeData) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: window.__typeData.labels,
                datasets: [{
                    data: window.__typeData.counts,
                    backgroundColor: [
                        '#1e6ff1', '#00c6ff', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 12 }, padding: 16 }
                    }
                }
            }
        });
    }
}

/* ── 5. Compare functionality ─────────────────────────────── */
function initCompare() {
    const selects = document.querySelectorAll('.compare-select');
    selects.forEach(sel => {
        sel.addEventListener('change', function () {
            loadCompareData(this.dataset.slot, this.value);
        });
    });
}

async function loadCompareData(slot, id) {
    if (!id) return;
    const panel = document.getElementById('comparePanel' + slot);
    if (!panel) return;

    panel.innerHTML = '<div class="text-center py-3"><div class="hp-spinner" style="width:30px;height:30px;border-top-color:var(--blue)"></div></div>';

    try {
        const res  = await fetch(`/house-price-prediction/api/dashboard.php?action=get_prediction&id=${encodeURIComponent(id)}`);
        const data = await res.json();
        if (data.success && data.prediction) {
            renderCompareCard(slot, data.prediction);
        }
    } catch (_) {
        panel.innerHTML = '<p class="text-danger small text-center">Failed to load.</p>';
    }
}

function renderCompareCard(slot, p) {
    const panel = document.getElementById('comparePanel' + slot);
    if (!panel) return;
    const fmt = n => '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    panel.innerHTML = `
        <div class="compare-card selected">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-700">${escHtml(p.property_type)} — ${escHtml(p.location)}</span>
                <span class="badge bg-accent">${fmt(p.predicted_price)}</span>
            </div>
            ${[
                ['City',        p.city],
                ['Location',    p.location],
                ['Area',        p.area + ' sq.ft'],
                ['Bedrooms',    p.bedrooms + ' BHK'],
                ['Bathrooms',   p.bathrooms],
                ['Floors',      p.floors],
                ['Age',         p.property_age + ' yrs'],
                ['Parking',     p.parking == 1 ? 'Yes' : 'No'],
                ['Furnishing',  p.furnishing],
                ['Condition',   p.condition_type],
                ['Price',       fmt(p.predicted_price)],
            ].map(([k, v]) => `
                <div class="compare-row">
                    <div class="compare-label">${k}</div>
                    <div class="compare-val ${k === 'Price' ? 'highlight' : ''}">${escHtml(String(v))}</div>
                </div>`).join('')}
        </div>`;
}

/* ── 6. History Search ────────────────────────────────────── */
function initHistorySearch() {
    const searchInput = document.getElementById('historySearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', debounce(function () {
        const q    = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.history-row');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }, 250));
}

/* ── 7. Delete prediction ─────────────────────────────────── */
async function deletePrediction(id, btn) {
    if (!confirm('Delete this prediction?')) return;
    try {
        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('id', id);
        const res  = await fetch('/house-price-prediction/api/dashboard.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            const row = btn.closest('tr');
            if (row) {
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
            showToast('Prediction deleted.', 'info');
        }
    } catch (_) {
        showToast('Failed to delete.', 'error');
    }
}

/* ── 8. Toast Notification ────────────────────────────────── */
function showToast(msg, type = 'success') {
    const colors = {
        success: '#10b981', error: '#ef4444', info: '#1e6ff1', warning: '#f59e0b'
    };
    const icons = {
        success: 'bi-check-circle-fill', error: 'bi-x-circle-fill',
        info: 'bi-info-circle-fill', warning: 'bi-exclamation-triangle-fill'
    };

    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;top:80px;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;';
        document.body.appendChild(container);
    }

    const el = document.createElement('div');
    el.style.cssText = `
        background:${colors[type]};color:#fff;
        border-radius:10px;padding:.75rem 1.25rem;
        font-size:.88rem;font-weight:600;
        display:flex;align-items:center;gap:.6rem;
        box-shadow:0 8px 25px rgba(0,0,0,.15);
        animation:fadeInUp .3s ease both;
        max-width:320px;
    `;
    el.innerHTML = `<i class="bi ${icons[type]}"></i>${escHtml(msg)}`;
    container.appendChild(el);

    setTimeout(() => {
        el.style.transition = 'opacity .3s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
    }, 3500);
}

/* ── 9. Utility ───────────────────────────────────────────── */
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;');
}

function debounce(fn, delay) {
    let t;
    return function (...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

/* ── 10. Scroll reveal ────────────────────────────────────── */
function initScrollReveal() {
    const els = document.querySelectorAll('[data-reveal]');
    if (!els.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    els.forEach(el => observer.observe(el));
}

/* ── 11. Number counter animation ─────────────────────────── */
function animateCounter(el, target, duration = 1500) {
    const start = 0;
    const step  = target / (duration / 16);
    let   current = start;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = Math.floor(current).toLocaleString('en-IN');
    }, 16);
}

function initCounters() {
    document.querySelectorAll('[data-counter]').forEach(el => {
        const target = parseInt(el.dataset.counter, 10);
        if (!isNaN(target)) {
            const observer = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) {
                    animateCounter(el, target);
                    observer.unobserve(el);
                }
            });
            observer.observe(el);
        }
    });
}

/* ── 12. Bootstrap tooltips ───────────────────────────────── */
function initTooltips() {
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el));
}

/* ── Init ─────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initNavbarScroll();
    initPredictForm();
    initDashboardCharts();
    initCompare();
    initHistorySearch();
    initScrollReveal();
    initCounters();
    initTooltips();
});
