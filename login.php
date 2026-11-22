<?php
/**
 * login.php — HousePrice AI (Premium Redesign)
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) { header('Location: /house-price-prediction/dashboard.php'); exit; }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']  ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($pass)) {
        $error = 'Password is required.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, name, email, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            setUserSession($user);
            $redirect = $_GET['redirect'] ?? '/house-price-prediction/dashboard.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — HousePrice AI</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
/* ── Reset ───────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Inter', sans-serif; overflow-x: hidden; }

/* ── CSS Variables ───────────────────────────────────────────── */
:root {
    --navy:    #0a1628;
    --blue:    #1e6ff1;
    --blue2:   #3b82f6;
    --accent:  #00d4ff;
    --accent2: #a855f7;
    --white:   #ffffff;
    --glass:   rgba(255,255,255,0.07);
    --glass-b: rgba(255,255,255,0.15);
    --radius:  16px;
}

/* ── Page Layout ─────────────────────────────────────────────── */
body {
    min-height: 100vh;
    background: #050d1a;
    display: flex;
    align-items: stretch;
}

/* ── Animated Background ─────────────────────────────────────── */
.bg-canvas {
    position: fixed;
    inset: 0;
    z-index: 0;
    overflow: hidden;
}

/* Deep gradient base */
.bg-canvas::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 20% 20%, rgba(30,111,241,0.35) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 80% 80%, rgba(168,85,247,0.25) 0%, transparent 55%),
        radial-gradient(ellipse 50% 40% at 50% 50%, rgba(0,212,255,0.12) 0%, transparent 50%),
        linear-gradient(135deg, #050d1a 0%, #0a1628 50%, #0d1f3c 100%);
}

/* Animated glowing orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: float 8s ease-in-out infinite;
    pointer-events: none;
}
.orb-1 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(30,111,241,0.4), transparent 70%);
    top: -150px; left: -100px;
    animation-duration: 10s;
}
.orb-2 {
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(168,85,247,0.3), transparent 70%);
    bottom: -100px; right: -80px;
    animation-duration: 12s;
    animation-delay: -4s;
}
.orb-3 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(0,212,255,0.2), transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    animation-duration: 15s;
    animation-delay: -7s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%       { transform: translate(30px, -40px) scale(1.05); }
    66%       { transform: translate(-20px, 25px) scale(0.95); }
}

/* Particle grid */
.grid-overlay {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(30,111,241,0.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(30,111,241,0.06) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
}

/* ── Split Layout ────────────────────────────────────────────── */
.page-wrapper {
    position: relative;
    z-index: 1;
    display: flex;
    width: 100%;
    min-height: 100vh;
}

/* ── LEFT PANEL ──────────────────────────────────────────────── */
.left-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 3rem 4rem;
    position: relative;
}

/* Floating property cards */
.prop-cards-wrap {
    position: relative;
    width: 380px;
    height: 420px;
    margin-bottom: 3rem;
}

.prop-card {
    position: absolute;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 20px;
    padding: 1.25rem 1.5rem;
    color: #fff;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
    transition: transform 0.3s ease;
}
.prop-card:hover { transform: translateY(-4px) !important; }

/* Card 1 — main prediction */
.prop-card-1 {
    width: 300px;
    top: 0; left: 20px;
    animation: cardFloat1 6s ease-in-out infinite;
    z-index: 3;
}
/* Card 2 — small stat */
.prop-card-2 {
    width: 220px;
    top: 160px; right: 0;
    animation: cardFloat2 7s ease-in-out infinite;
    z-index: 2;
    animation-delay: -2s;
}
/* Card 3 — location */
.prop-card-3 {
    width: 240px;
    bottom: 0; left: 10px;
    animation: cardFloat3 8s ease-in-out infinite;
    z-index: 2;
    animation-delay: -4s;
}

@keyframes cardFloat1 {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-12px); }
}
@keyframes cardFloat2 {
    0%, 100% { transform: translateY(0px) rotate(2deg); }
    50%       { transform: translateY(-10px) rotate(2deg); }
}
@keyframes cardFloat3 {
    0%, 100% { transform: translateY(0px) rotate(-1deg); }
    50%       { transform: translateY(-8px) rotate(-1deg); }
}

.card-label   { font-size: 0.7rem; color: rgba(255,255,255,0.5); font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.4rem; }
.card-price   { font-size: 1.9rem; font-weight: 800; background: linear-gradient(135deg, #00d4ff, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; }
.card-sub     { font-size: 0.75rem; color: rgba(255,255,255,0.5); margin-top: 0.3rem; }
.card-tags    { display: flex; gap: 0.5rem; margin-top: 0.9rem; flex-wrap: wrap; }
.card-tag     { background: rgba(30,111,241,0.25); border: 1px solid rgba(30,111,241,0.4); border-radius: 20px; padding: 0.2rem 0.65rem; font-size: 0.7rem; font-weight: 600; color: rgba(255,255,255,0.8); }
.card-tag.purple { background: rgba(168,85,247,0.2); border-color: rgba(168,85,247,0.4); }
.card-tag.cyan   { background: rgba(0,212,255,0.15); border-color: rgba(0,212,255,0.3); }

.card-stat-val  { font-size: 1.5rem; font-weight: 800; color: #00d4ff; }
.card-stat-lbl  { font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }
.card-mini-row  { display: flex; justify-content: space-between; gap: 1rem; }

.accuracy-bar   { height: 5px; border-radius: 3px; background: rgba(255,255,255,0.1); margin-top: 0.4rem; overflow: hidden; }
.accuracy-fill  { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #1e6ff1, #00d4ff); animation: barFill 2s ease forwards; }
@keyframes barFill { from { width: 0; } to { width: 92%; } }

/* Left panel text */
.left-headline  { font-size: 2.2rem; font-weight: 800; color: #fff; line-height: 1.25; margin-bottom: 1rem; }
.left-headline span { background: linear-gradient(135deg, #00d4ff, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.left-sub       { color: rgba(255,255,255,0.55); font-size: 1rem; line-height: 1.7; max-width: 380px; margin-bottom: 2rem; }

.feature-pills  { display: flex; flex-wrap: wrap; gap: 0.65rem; }
.feature-pill {
    display: flex; align-items: center; gap: 0.45rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 30px;
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: rgba(255,255,255,0.75);
    backdrop-filter: blur(10px);
    transition: all 0.2s;
}
.feature-pill:hover { background: rgba(30,111,241,0.2); border-color: rgba(30,111,241,0.4); color: #fff; }
.feature-pill i { font-size: 0.85rem; color: #00d4ff; }

/* ── RIGHT PANEL ─────────────────────────────────────────────── */
.right-panel {
    flex: 0 0 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    background: rgba(255,255,255,0.03);
    border-left: 1px solid rgba(255,255,255,0.07);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    position: relative;
    overflow: hidden;
}

/* Soft glow behind the form */
.right-panel::before {
    content: '';
    position: absolute;
    top: -100px; right: -100px;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(30,111,241,0.15), transparent 70%);
    pointer-events: none;
}
.right-panel::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -80px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(168,85,247,0.1), transparent 70%);
    pointer-events: none;
}

/* ── Login Card ──────────────────────────────────────────────── */
.login-card {
    width: 100%;
    max-width: 400px;
    position: relative;
    z-index: 2;
}

/* Logo area */
.logo-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2.5rem;
    text-decoration: none;
}
.logo-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e6ff1, #00d4ff);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: #fff;
    box-shadow: 0 8px 25px rgba(30,111,241,0.4);
    flex-shrink: 0;
}
.logo-text { font-size: 1.2rem; font-weight: 800; color: #fff; }
.logo-text span { background: linear-gradient(135deg, #00d4ff, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

/* Heading */
.login-heading {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.4rem;
    line-height: 1.2;
}
.login-sub {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.45);
    margin-bottom: 2rem;
    font-weight: 400;
}

/* Error / success alerts */
.login-alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-radius: 12px;
    padding: 0.9rem 1.1rem;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
    font-weight: 500;
    animation: slideDown 0.3s ease;
    border: 1px solid;
}
.login-alert.error   { background: rgba(239,68,68,0.12);  border-color: rgba(239,68,68,0.3);  color: #fca5a5; }
.login-alert.success { background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.3); color: #6ee7b7; }

@keyframes slideDown {
    from { opacity:0; transform: translateY(-8px); }
    to   { opacity:1; transform: translateY(0); }
}

/* ── Floating Label Inputs ───────────────────────────────────── */
.field-wrap {
    position: relative;
    margin-bottom: 1.25rem;
}

.field-wrap label {
    position: absolute;
    top: 50%; left: 48px;
    transform: translateY(-50%);
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255,255,255,0.4);
    pointer-events: none;
    transition: all 0.2s ease;
    background: transparent;
    padding: 0 4px;
    z-index: 2;
}

.field-wrap input {
    width: 100%;
    background: rgba(255,255,255,0.06) !important;
    border: 1.5px solid rgba(255,255,255,0.1) !important;
    border-radius: 12px !important;
    padding: 0.9rem 1rem 0.9rem 3rem !important;
    font-size: 0.95rem !important;
    font-weight: 500;
    color: #fff !important;
    transition: all 0.25s ease !important;
    outline: none !important;
    box-shadow: none !important;
    -webkit-text-fill-color: #fff;
}
.field-wrap input::placeholder { color: transparent; }
.field-wrap input:-webkit-autofill,
.field-wrap input:-webkit-autofill:hover,
.field-wrap input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px rgba(30,111,241,0.1) inset !important;
    -webkit-text-fill-color: #fff !important;
    border-color: rgba(30,111,241,0.4) !important;
    caret-color: #fff;
}

/* Focus & filled states */
.field-wrap input:focus,
.field-wrap input:not(:placeholder-shown) {
    border-color: rgba(30,111,241,0.6) !important;
    background: rgba(30,111,241,0.08) !important;
    box-shadow: 0 0 0 3px rgba(30,111,241,0.12) !important;
}
.field-wrap input:focus + .field-label,
.field-wrap input:not(:placeholder-shown) + .field-label {
    top: 0;
    left: 46px;
    font-size: 0.72rem;
    color: #00d4ff;
    font-weight: 600;
}

/* Icons inside fields */
.field-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: rgba(255,255,255,0.3);
    transition: color 0.2s;
    z-index: 3;
    pointer-events: none;
}
.field-wrap input:focus ~ .field-icon,
.field-wrap:has(input:not(:placeholder-shown)) .field-icon { color: #1e6ff1; }

/* Password toggle */
.pass-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255,255,255,0.3);
    font-size: 1rem;
    cursor: pointer;
    padding: 0;
    z-index: 3;
    transition: color 0.2s;
    line-height: 1;
}
.pass-toggle:hover { color: rgba(255,255,255,0.7); }

/* ── Options row ─────────────────────────────────────────────── */
.options-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.custom-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
.custom-check input[type="checkbox"] {
    width: 16px; height: 16px;
    accent-color: #1e6ff1;
    cursor: pointer;
}
.custom-check span {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.45);
    font-weight: 500;
}

.forgot-link {
    font-size: 0.82rem;
    font-weight: 600;
    color: #00d4ff;
    text-decoration: none;
    transition: color 0.2s;
}
.forgot-link:hover { color: #a855f7; }

/* ── Submit Button ───────────────────────────────────────────── */
.btn-login {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e6ff1 0%, #00d4ff 100%);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(30,111,241,0.4);
    margin-bottom: 1.5rem;
}
.btn-login::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}
.btn-login:hover::before { transform: translateX(100%); }
.btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(30,111,241,0.55); }
.btn-login:active { transform: translateY(0); }
.btn-login .btn-shine { position: absolute; inset: 0; }

/* Loading state */
.btn-login.loading { pointer-events: none; opacity: 0.8; }
.btn-login .spinner-wrap { display: none; }
.btn-login.loading .btn-text { display: none; }
.btn-login.loading .spinner-wrap { display: inline-flex; align-items: center; gap: 0.5rem; }

/* ── Divider ─────────────────────────────────────────────────── */
.or-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.or-divider::before, .or-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.08);
}
.or-divider span {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

/* ── Register Button ─────────────────────────────────────────── */
.btn-register {
    display: block;
    width: 100%;
    padding: 0.9rem;
    border-radius: 12px;
    border: 1.5px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.25s ease;
    backdrop-filter: blur(6px);
}
.btn-register:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.2);
    color: #fff;
    transform: translateY(-1px);
}

/* ── Demo credentials box ────────────────────────────────────── */
.demo-box {
    margin-top: 1.75rem;
    padding: 1rem 1.25rem;
    background: rgba(0,212,255,0.06);
    border: 1px solid rgba(0,212,255,0.2);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
}
.demo-box::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px; height: 100%;
    background: linear-gradient(180deg, #1e6ff1, #00d4ff);
    border-radius: 3px 0 0 3px;
}
.demo-box-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: #00d4ff;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.demo-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem; }
.demo-row:last-child { margin-bottom: 0; }
.demo-label { font-size: 0.78rem; color: rgba(255,255,255,0.4); font-weight: 500; }
.demo-val   { font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.8); font-family: 'Courier New', monospace; }
.demo-fill-btn {
    font-size: 0.72rem;
    font-weight: 700;
    color: #00d4ff;
    background: rgba(0,212,255,0.1);
    border: 1px solid rgba(0,212,255,0.25);
    border-radius: 20px;
    padding: 0.2rem 0.65rem;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 0.6rem;
    width: 100%;
    text-align: center;
}
.demo-fill-btn:hover { background: rgba(0,212,255,0.2); border-color: rgba(0,212,255,0.5); }

/* ── Footer note ─────────────────────────────────────────────── */
.login-footer-note {
    text-align: center;
    margin-top: 1.75rem;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.2);
}
.login-footer-note span { color: rgba(255,255,255,0.35); }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 1024px) {
    .left-panel  { display: none !important; }
    .right-panel { flex: 1; border-left: none; padding: 2rem 1.5rem; }
}
@media (max-width: 480px) {
    .login-heading { font-size: 1.75rem; }
    .right-panel   { padding: 1.5rem 1.25rem; }
    .login-card    { max-width: 100%; }
}

/* ── Focus visible outline ───────────────────────────────────── */
input:focus-visible { outline: none !important; }

/* Spinner */
.btn-spinner {
    width: 20px; height: 20px;
    border: 2.5px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>

<!-- Animated background -->
<div class="bg-canvas">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-overlay"></div>
</div>

<div class="page-wrapper">

    <!-- ══════════════════════════════════════════════════════════
         LEFT PANEL — Animated showcase
    ══════════════════════════════════════════════════════════════ -->
    <div class="left-panel d-none d-lg-flex flex-column justify-content-center">

        <!-- Floating cards -->
        <div class="prop-cards-wrap">

            <!-- Card 1 — Main prediction -->
            <div class="prop-card prop-card-1">
                <div class="card-label"><i class="bi bi-stars me-1"></i>AI Prediction Result</div>
                <div class="card-price">₹82,50,000</div>
                <div class="card-sub">Range: ₹74L — ₹91L &nbsp;·&nbsp; ₹6,875/sqft</div>
                <div class="card-tags">
                    <span class="card-tag"><i class="bi bi-geo-alt"></i> Bangalore</span>
                    <span class="card-tag purple"><i class="bi bi-building"></i> Apartment</span>
                    <span class="card-tag cyan">3 BHK</span>
                    <span class="card-tag">1,200 sqft</span>
                </div>
                <div style="margin-top:1rem;">
                    <div style="font-size:.7rem;color:rgba(255,255,255,.4);margin-bottom:.35rem;">Model Confidence</div>
                    <div class="accuracy-bar"><div class="accuracy-fill"></div></div>
                    <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-top:.3rem;">92% R² Score</div>
                </div>
            </div>

            <!-- Card 2 — Stats -->
            <div class="prop-card prop-card-2">
                <div class="card-label">Quick Stats</div>
                <div class="card-mini-row">
                    <div>
                        <div class="card-stat-val">10+</div>
                        <div class="card-stat-lbl">Cities</div>
                    </div>
                    <div>
                        <div class="card-stat-val">120+</div>
                        <div class="card-stat-lbl">Samples</div>
                    </div>
                </div>
                <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid rgba(255,255,255,.08);">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="width:8px;height:8px;border-radius:50%;background:#10b981;animation:blink 1.5s infinite;"></div>
                        <span style="font-size:.72rem;color:rgba(255,255,255,.5);">ML Model Active</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 — Location card -->
            <div class="prop-card prop-card-3">
                <div class="card-label"><i class="bi bi-geo-alt me-1"></i>Top Location</div>
                <div style="font-size:1rem;font-weight:700;color:#fff;margin:.2rem 0;">Koramangala, Bangalore</div>
                <div style="font-size:.75rem;color:rgba(255,255,255,.45);">Avg: ₹14,500 / sq.ft</div>
                <div class="card-tags" style="margin-top:.6rem;">
                    <span class="card-tag cyan">Furnished</span>
                    <span class="card-tag">Excellent</span>
                    <span class="card-tag purple">Premium</span>
                </div>
            </div>
        </div>

        <!-- Text content -->
        <div class="left-headline">
            Predict Property<br>
            Prices with <span>AI</span>
        </div>
        <p class="left-sub">
            Get instant, accurate house price predictions for 10+ major Indian cities powered by a Random Forest ML model with ~92% accuracy.
        </p>
        <div class="feature-pills">
            <div class="feature-pill"><i class="bi bi-lightning-charge-fill"></i>&lt;1s Prediction</div>
            <div class="feature-pill"><i class="bi bi-shield-check"></i>Secure & Private</div>
            <div class="feature-pill"><i class="bi bi-graph-up-arrow"></i>92% Accurate</div>
            <div class="feature-pill"><i class="bi bi-geo-alt-fill"></i>10+ Cities</div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         RIGHT PANEL — Login form
    ══════════════════════════════════════════════════════════════ -->
    <div class="right-panel">
        <div class="login-card">

            <!-- Logo -->
            <a href="/house-price-prediction/index.php" class="logo-wrap">
                <div class="logo-icon"><i class="bi bi-houses-fill"></i></div>
                <div class="logo-text">HousePrice <span>AI</span></div>
            </a>

            <!-- Heading -->
            <h1 class="login-heading">Welcome back</h1>
            <p class="login-sub">Sign in to your account to continue</p>

            <!-- Alert -->
            <?php if ($error): ?>
            <div class="login-alert error">
                <i class="bi bi-exclamation-circle-fill" style="font-size:1.1rem;flex-shrink:0;"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" id="loginForm" novalidate>

                <!-- Email -->
                <div class="field-wrap">
                    <input
                        type="email"
                        name="email"
                        id="emailField"
                        placeholder="Email address"
                        autocomplete="email"
                        required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label class="field-label" for="emailField">Email address</label>
                    <i class="bi bi-envelope field-icon"></i>
                </div>

                <!-- Password -->
                <div class="field-wrap">
                    <input
                        type="password"
                        name="password"
                        id="passField"
                        placeholder="Password"
                        autocomplete="current-password"
                        required>
                    <label class="field-label" for="passField">Password</label>
                    <i class="bi bi-lock field-icon"></i>
                    <button type="button" class="pass-toggle" id="passToggle" onclick="togglePass()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <!-- Options -->
                <div class="options-row">
                    <label class="custom-check">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="submitBtn">
                    <span class="btn-text">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </span>
                    <span class="spinner-wrap">
                        <span class="btn-spinner"></span>
                        Signing in…
                    </span>
                </button>

            </form>

            <!-- Divider -->
            <div class="or-divider"><span>New to HousePrice AI?</span></div>

            <!-- Register -->
            <a href="/house-price-prediction/register.php" class="btn-register">
                <i class="bi bi-person-plus me-2"></i>Create a free account
            </a>

            <!-- Demo credentials -->
            <div class="demo-box">
                <div class="demo-box-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Demo Credentials
                </div>
                <div class="demo-row">
                    <span class="demo-label">Email</span>
                    <span class="demo-val">demo@houseprice.ai</span>
                </div>
                <div class="demo-row">
                    <span class="demo-label">Password</span>
                    <span class="demo-val">Demo@1234</span>
                </div>
                <div class="demo-fill-btn" onclick="fillDemo()">
                    <i class="bi bi-lightning-charge me-1"></i>Fill Demo Credentials
                </div>
            </div>

            <!-- Footer note -->
            <div class="login-footer-note">
                Protected by bcrypt &nbsp;·&nbsp; <span>PHP 8.2 + MySQL PDO</span>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Password toggle */
function togglePass() {
    const f   = document.getElementById('passField');
    const eye = document.getElementById('eyeIcon');
    const show = f.type === 'password';
    f.type = show ? 'text' : 'password';
    eye.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}

/* Fill demo credentials */
function fillDemo() {
    const email = document.getElementById('emailField');
    const pass  = document.getElementById('passField');
    email.value = 'demo@houseprice.ai';
    pass.value  = 'Demo@1234';
    // Trigger input events to activate floating labels
    email.dispatchEvent(new Event('input'));
    pass.dispatchEvent(new Event('input'));
    // Visual feedback
    email.style.transition = 'border-color 0.3s';
    pass.style.transition  = 'border-color 0.3s';
    email.style.borderColor = 'rgba(0,212,255,0.6)';
    pass.style.borderColor  = 'rgba(0,212,255,0.6)';
    setTimeout(() => {
        email.style.borderColor = '';
        pass.style.borderColor  = '';
    }, 1000);
}

/* Loading state on submit */
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
});

/* Floating label fix for autofill (Chrome) */
document.querySelectorAll('.field-wrap input').forEach(input => {
    input.addEventListener('animationstart', e => {
        if (e.animationName === 'onAutoFillStart') {
            input.classList.add('filled');
        }
    });
});
</script>
</body>
</html>
