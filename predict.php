<?php
/**
 * predict.php — House Price Prediction Page
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$pageTitle = 'Predict House Price';
require_once __DIR__ . '/includes/header.php';

// Cities and locations
$cityLocations = [
    'Mumbai'    => ['Bandra','Andheri','Juhu','Powai','Thane','Borivali','Malad','Dadar','Worli','Lower Parel'],
    'Delhi'     => ['Dwarka','Rohini','Saket','Vasant Kunj','Lajpat Nagar','Pitampura','Janakpuri','Karol Bagh','Greater Kailash','Defence Colony'],
    'Bangalore' => ['Whitefield','Koramangala','Indiranagar','HSR Layout','Electronic City','Marathahalli','Sarjapur','Hebbal','Yeshwanthpur','JP Nagar'],
    'Hyderabad' => ['Gachibowli','Hitec City','Banjara Hills','Jubilee Hills','Kukatpally','Madhapur','Kondapur','Ameerpet','Secunderabad','LB Nagar'],
    'Chennai'   => ['Anna Nagar','T Nagar','Adyar','OMR','Velachery','Porur','Mylapore','Sholinganallur','Ambattur','Perambur'],
    'Pune'      => ['Baner','Wakad','Kothrud','Hadapsar','Hinjewadi','Aundh','Viman Nagar','Magarpatta','Kondhwa','Katraj'],
    'Kolkata'   => ['Salt Lake','New Town','Park Street','Ballygunge','Behala','Howrah','Tollygunge','Dum Dum','Jadavpur','Rajarhat'],
    'Ahmedabad' => ['SG Highway','Satellite','Bodakdev','Vastrapur','Thaltej','Bopal','Chandkheda','Naroda','Gota','Maninagar'],
    'Jaipur'    => ['Malviya Nagar','Vaishali Nagar','Mansarovar','C Scheme','Bani Park','Sitapura','Tonk Road','Ajmer Road','Pratap Nagar','Jagatpura'],
    'Surat'     => ['Vesu','Adajan','Katargam','Althan','Dindoli','Varachha','Udhna','Pal','Piplod','Bhatar'],
    'Lucknow'   => ['Gomti Nagar','Hazratganj','Aliganj','Indira Nagar','Mahanagar','Vikas Nagar','Rajajipuram','Alambagh','Vrindavan Yojna','Kanpur Road'],
];
?>

<div class="dashboard-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <button id="sidebarToggle" class="sidebar-toggle-btn"><i class="bi bi-list"></i></button>

    <main class="main-content" style="padding-top:calc(var(--navbar-h) + 1.5rem);">

        <!-- Page header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fw-800 text-navy mb-1" style="font-size:1.6rem;">
                    <i class="bi bi-magic me-2 text-blue"></i>AI Price Prediction
                </h1>
                <p class="text-muted small mb-0">
                    Fill in the property details and let our ML model predict the price.
                </p>
            </div>
            <a href="/house-price-prediction/history.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history me-1"></i>My History
            </a>
        </div>

        <div class="row g-4">
            <!-- Prediction Form -->
            <div class="col-xl-7">
                <div class="predict-form-card">
                    <div class="predict-form-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="brand-icon"><i class="bi bi-houses-fill"></i></div>
                            <div>
                                <h4 class="fw-800 mb-0">Property Details</h4>
                                <p class="opacity-75 small mb-0">All fields are required for accurate prediction.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <form id="predictForm" novalidate>

                            <!-- Location -->
                            <div class="form-section-title"><i class="bi bi-geo-alt text-blue"></i>Location</div>
                            <div class="row g-3">
                                <div class="col-sm-6 hp-form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <select name="city" id="city" class="form-select" required onchange="updateLocations()">
                                        <option value="">Select City</option>
                                        <?php foreach (array_keys($cityLocations) as $c): ?>
                                        <option value="<?= $c ?>"><?= $c ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Location / Area <span class="text-danger">*</span></label>
                                    <select name="location" id="location" class="form-select" required>
                                        <option value="">Select Location</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Property Type -->
                            <div class="form-section-title"><i class="bi bi-building text-blue"></i>Property Type</div>
                            <div class="hp-form-group">
                                <label>Property Type <span class="text-danger">*</span></label>
                                <input type="hidden" name="propTypeHidden" id="propTypeHidden" value="Apartment">
                                <div class="row g-2">
                                    <?php foreach(['Apartment', 'Villa', 'Independent House', 'Commercial', 'Studio'] as $i => $pt): ?>
                                    <div class="col-auto">
                                        <input type="radio" class="btn-check" name="property_type"
                                               id="pt_<?= str_replace(' ','_',$pt) ?>"
                                               value="<?= $pt ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-secondary btn-sm" for="pt_<?= str_replace(' ','_',$pt) ?>">
                                            <?= $pt ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Size & Rooms -->
                            <div class="form-section-title"><i class="bi bi-rulers text-blue"></i>Size & Rooms</div>
                            <div class="row g-3">
                                <div class="col-sm-6 hp-form-group">
                                    <label>Area (sq. ft.) <span class="text-danger">*</span></label>
                                    <input type="number" name="area" id="area" class="form-control"
                                           placeholder="e.g. 1200" min="200" max="20000" required>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Bedrooms <span class="text-danger">*</span></label>
                                    <select name="bedrooms" id="bedrooms" class="form-select" required>
                                        <option value="">Select</option>
                                        <?php for($i=1;$i<=6;$i++): ?>
                                        <option value="<?=$i?>"><?=$i?> BHK</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Bathrooms <span class="text-danger">*</span></label>
                                    <select name="bathrooms" id="bathrooms" class="form-select" required>
                                        <option value="">Select</option>
                                        <?php for($i=1;$i<=6;$i++): ?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Number of Floors <span class="text-danger">*</span></label>
                                    <select name="floors" id="floors" class="form-select" required>
                                        <option value="">Select</option>
                                        <?php for($i=1;$i<=5;$i++): ?>
                                        <option value="<?=$i?>"><?=$i?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="form-section-title"><i class="bi bi-info-circle text-blue"></i>Additional Details</div>
                            <div class="row g-3">
                                <div class="col-sm-6 hp-form-group">
                                    <label>Property Age (years) <span class="text-danger">*</span></label>
                                    <input type="number" name="property_age" id="property_age"
                                           class="form-control" placeholder="e.g. 5" min="0" max="100" required>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Parking <span class="text-danger">*</span></label>
                                    <select name="parking" id="parking" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="0">No Parking</option>
                                        <option value="1">1 Parking Spot</option>
                                        <option value="2">2 Parking Spots</option>
                                        <option value="3">3+ Parking Spots</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Furnishing Status <span class="text-danger">*</span></label>
                                    <select name="furnishing" id="furnishing" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Unfurnished">Unfurnished</option>
                                        <option value="Semi-Furnished">Semi-Furnished</option>
                                        <option value="Furnished">Furnished</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 hp-form-group">
                                    <label>Property Condition <span class="text-danger">*</span></label>
                                    <select name="condition" id="condition" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Average">Average</option>
                                        <option value="Good">Good</option>
                                        <option value="Excellent">Excellent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn-predict" id="predictBtn">
                                    <i class="bi bi-magic me-2"></i>Predict House Price with AI
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Result + Tips -->
            <div class="col-xl-5">
                <!-- Result area -->
                <div id="predictionResult" style="display:none;" class="mb-4"></div>

                <!-- Tips card -->
                <div class="hp-card mb-4" id="tipsCard">
                    <div class="card-header">
                        <i class="bi bi-lightbulb text-orange me-2"></i>Prediction Tips
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php
                            $tips = [
                                ['bi-check2-circle text-green', 'Be precise with the area in square feet.'],
                                ['bi-check2-circle text-green', 'Property age significantly affects price.'],
                                ['bi-check2-circle text-green', 'Furnished properties command 10–20% premium.'],
                                ['bi-check2-circle text-green', 'Location within a city is a major price driver.'],
                                ['bi-check2-circle text-green', 'Excellent condition increases value by ~15%.'],
                            ];
                            foreach ($tips as [$icon, $tip]):
                            ?>
                            <li class="d-flex gap-2 align-items-start mb-3">
                                <i class="bi <?= $icon ?> mt-1 flex-shrink-0"></i>
                                <span class="small text-muted"><?= $tip ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Model info -->
                <div class="hp-card">
                    <div class="card-header">
                        <i class="bi bi-cpu text-blue me-2"></i>ML Model Info
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <div class="fw-800 fs-5 text-blue">~92%</div>
                                <div class="small text-muted">R² Score</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-800 fs-5 text-green">300</div>
                                <div class="small text-muted">Trees</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-800 fs-5 text-orange">±8%</div>
                                <div class="small text-muted">MAPE</div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="tag green"><i class="bi bi-shield-check"></i>Active</span>
                            <span class="small text-muted ms-1">Random Forest Regressor</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const cityLocations = <?= json_encode($cityLocations) ?>;

function updateLocations() {
    const city   = document.getElementById('city').value;
    const locSel = document.getElementById('location');
    locSel.innerHTML = '<option value="">Select Location</option>';
    if (city && cityLocations[city]) {
        cityLocations[city].forEach(loc => {
            const opt = document.createElement('option');
            opt.value = loc; opt.textContent = loc;
            locSel.appendChild(opt);
        });
    }
}

// Sync radio buttons to hidden field
document.querySelectorAll('input[name="property_type"]').forEach(r => {
    r.addEventListener('change', function() {
        const hidden = document.getElementById('propTypeHidden');
        if (hidden) hidden.value = this.value;
    });
});

// Override form field access in script.js for radio button
const origPropType = document.getElementById('property_type');
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
