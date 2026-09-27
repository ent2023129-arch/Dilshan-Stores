<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Contact';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitize($_POST['email'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Please enter your name.';
    }
    if ($email === '' || !is_valid_email($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($message === '') {
        $errors['message'] = 'Please enter a message.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())'
        );
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
        $success = true;
        // Clear posted values after a successful save
        $name = $email = $message = '';
    }
}

include __DIR__ . '/includes/header.php';
?>

<header class="ds-hero text-center py-5">
  <div class="container">
    <h1 class="fade-in-up" style="font-size:2.2rem;">Contact Us</h1>
    <p class="lead fade-in-up">Questions, feedback or feature requests &mdash; we'd love to hear from you.</p>
  </div>
</header>

<div class="ds-auth-wrapper">
  <div class="ds-form-card fade-in-up">
    <h3><i class="fa-solid fa-envelope"></i> Send a Message</h3>
    <p class="text-muted mb-4">We usually reply within one business day.</p>

    <?php if ($success): ?>
      <div class="alert alert-success">Thanks for reaching out! Your message has been received.</div>
    <?php endif; ?>

    <form method="POST" action="contact.php" data-validate novalidate>
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" data-rule="required"
               value="<?= isset($name) ? sanitize($name) : '' ?>">
        <div class="invalid-feedback-custom"><?= $errors['name'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" data-rule="required|email"
               value="<?= isset($email) ? sanitize($email) : '' ?>">
        <div class="invalid-feedback-custom"><?= $errors['email'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" rows="4" class="form-control" data-rule="required"><?= isset($message) ? sanitize($message) : '' ?></textarea>
        <div class="invalid-feedback-custom"><?= $errors['message'] ?? '' ?></div>
      </div>
      <button type="submit" class="btn btn-ds-primary w-100 py-2"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
