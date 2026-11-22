<?php
require_once __DIR__ . '/includes/auth.php';
destroySession();
header('Location: /house-price-prediction/login.php');
exit;
