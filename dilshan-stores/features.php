<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Features';
include __DIR__ . '/includes/header.php';
?>

<header class="ds-hero text-center py-5">
  <div class="container">
    <h1 class="fade-in-up" style="font-size:2.2rem;">Features</h1>
    <p class="lead fade-in-up">Everything Dilshan Stores' Automatic Bill Calculator can do for you.</p>
  </div>
</header>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-cart-plus card-icon"></i>
          <h5>Dynamic Item Entry</h5>
          <p class="text-muted mb-0">Add, edit the quantity of, or remove line items and watch the bill update in real time &mdash; no page reloads.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-percent card-icon"></i>
          <h5>Flexible Discounts</h5>
          <p class="text-muted mb-0">Apply a discount as a flat rupee amount or a percentage of the subtotal.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-receipt card-icon"></i>
          <h5>Automatic Tax (GST)</h5>
          <p class="text-muted mb-0">Set any tax percentage and the tax amount and grand total recalculate instantly.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-print card-icon"></i>
          <h5>Print / Save as PDF</h5>
          <p class="text-muted mb-0">A clean, print-friendly layout lets you hand over or save a physical copy of the invoice.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-clock-rotate-left card-icon"></i>
          <h5>Bill History</h5>
          <p class="text-muted mb-0">Every saved bill is linked to your account so you can review past invoices any time.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4 fade-in-up">
        <div class="ds-card">
          <i class="fa-solid fa-lock card-icon"></i>
          <h5>Secure Login</h5>
          <p class="text-muted mb-0">Passwords are hashed and never stored in plain text; sessions keep your account protected.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background:#fff;">
  <div class="container text-center">
    <h2 class="ds-section-title fade-in-up">See It In Action</h2>
    <p class="ds-section-sub fade-in-up mb-4">Ready to create your first bill?</p>
    <?php if (is_logged_in()): ?>
      <a href="<?= base_url('dashboard.php') ?>" class="btn btn-ds-primary btn-lg px-4">Open Bill Calculator</a>
    <?php else: ?>
      <a href="<?= base_url('auth/register.php') ?>" class="btn btn-ds-primary btn-lg px-4">Create a Free Account</a>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
