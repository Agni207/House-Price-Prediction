<?php
/**
 * register.php
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) { header('Location: /house-price-prediction/dashboard.php'); exit; }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $conf  = $_POST['confirm']    ?? '';

    if (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($pass !== $conf) {
        $error = 'Passwords do not match.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $ins  = $db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $ins->execute([$name, $email, $hash]);
            $userId = (int)$db->lastInsertId();
            setUserSession(['id' => $userId, 'name' => $name, 'email' => $email]);
            header('Location: /house-price-prediction/dashboard.php');
            exit;
        }
    }
}

$pageTitle = 'Register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | HousePrice AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/house-price-prediction/assets/css/style.css">
</head>
<body style="padding-top:0;">

<div class="auth-wrapper" style="padding-top:0;">
    <!-- Left panel -->
    <div class="auth-left d-none d-lg-flex">
        <div class="text-center">
            <div class="brand-icon mx-auto mb-4" style="width:64px;height:64px;font-size:1.75rem;">
                <i class="bi bi-houses-fill"></i>
            </div>
            <h2 class="fw-800 mb-3">HousePrice <span class="text-accent">AI</span></h2>
            <p class="opacity-75" style="max-width:360px;line-height:1.75;">
                Join thousands of users getting accurate AI-powered property
                price predictions for major Indian cities.
            </p>
            <div class="d-flex gap-4 justify-content-center mt-4">
                <div class="text-center">
                    <div class="fw-800 fs-4" style="color:var(--accent);">120+</div>
                    <div class="small opacity-60">Data Points</div>
                </div>
                <div class="text-center">
                    <div class="fw-800 fs-4" style="color:var(--accent);">~92%</div>
                    <div class="small opacity-60">Accuracy</div>
                </div>
                <div class="text-center">
                    <div class="fw-800 fs-4" style="color:var(--accent);">10</div>
                    <div class="small opacity-60">Cities</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel -->
    <div class="auth-right">
        <div class="auth-card">
            <a href="/house-price-prediction/index.php" class="d-flex align-items-center gap-2 mb-4 text-decoration-none">
                <div class="brand-icon" style="width:32px;height:32px;font-size:.85rem;">
                    <i class="bi bi-houses-fill"></i>
                </div>
                <span class="fw-700 text-navy">HousePrice <span class="text-blue">AI</span></span>
            </a>

            <h2 class="auth-title">Create Account</h2>
            <p class="auth-sub">Sign up to start predicting property prices.</p>

            <?php if ($error): ?>
            <div class="alert-hp error mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>
                <div class="hp-form-group">
                    <label>Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-person text-muted"></i>
                        </span>
                        <input type="text" name="name" class="form-control border-start-0"
                               placeholder="Your full name" required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                </div>

                <div class="hp-form-group">
                    <label>Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input type="email" name="email" class="form-control border-start-0"
                               placeholder="you@example.com" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="hp-form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password" name="password" id="passField"
                               class="form-control border-start-0 border-end-0"
                               placeholder="Min. 8 characters" required>
                        <button type="button" class="input-group-text bg-white"
                                onclick="togglePass('passField', this)">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                </div>

                <div class="hp-form-group">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-lock-fill text-muted"></i>
                        </span>
                        <input type="password" name="confirm" id="confField"
                               class="form-control border-start-0 border-end-0"
                               placeholder="Repeat password" required>
                        <button type="button" class="input-group-text bg-white"
                                onclick="togglePass('confField', this)">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label small text-muted" for="terms">
                            I agree to the <a href="#" class="text-blue">Terms</a> and
                            <a href="#" class="text-blue">Privacy Policy</a>.
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="divider"><span>Already have an account?</span></div>
            <a href="/house-price-prediction/login.php"
               class="btn btn-outline-secondary w-100 rounded-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
    const f = document.getElementById(id);
    const isPass = f.type === 'password';
    f.type = isPass ? 'text' : 'password';
    btn.querySelector('i').className = isPass ? 'bi bi-eye-slash text-muted' : 'bi bi-eye text-muted';
}
</script>
</body>
</html>
