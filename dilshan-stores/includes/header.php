<?php
/**
 * Shared header + navigation bar.
 * Expects $pageTitle to be set by the including page (optional).
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Dilshan Stores';
}
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= sanitize($pageTitle) ?> | Dilshan Stores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark ds-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('index.html') ?>">
      <i class="fa-solid fa-store"></i> Dilshan Stores
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link <?= $current==='index.html'?'active':'' ?>" href="<?= base_url('index.html') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $current==='features.php'?'active':'' ?>" href="<?= base_url('features.php') ?>">Features</a></li>
        <li class="nav-item"><a class="nav-link <?= $current==='contact.php'?'active':'' ?>" href="<?= base_url('contact.php') ?>">Contact</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link <?= $current==='dashboard.php'?'active':'' ?>" href="<?= base_url('dashboard.php') ?>"><i class="fa-solid fa-calculator"></i> Bill Calculator</a></li>
          <li class="nav-item"><span class="nav-link text-warning">Hi, <?= sanitize($_SESSION['username']) ?></span></li>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light ms-lg-2" href="<?= base_url('auth/logout.php') ?>">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('auth/login.php') ?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-warning ms-lg-2 fw-semibold" href="<?= base_url('auth/register.php') ?>">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
