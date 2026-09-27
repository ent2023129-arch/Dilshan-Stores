<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

$pageTitle = 'Sign Up';
$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm_password'] ?? '');

    if ($username === '' || strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters.';
    }
    if ($email === '' || !is_valid_email($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check for existing username / email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
        $stmt->execute(['u' => $username, 'e' => $email]);
        if ($stmt->fetch()) {
            $errors['username'] = 'That username or email is already registered.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password, created_at) VALUES (:u, :e, :p, NOW())'
        );
        $stmt->execute(['u' => $username, 'e' => $email, 'p' => $hashed]);

        set_flash('success', 'Account created successfully! Please log in.');
        header('Location: ' . base_url('auth/login.php'));
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="ds-auth-wrapper">
  <div class="ds-form-card fade-in-up">
    <h3><i class="fa-solid fa-user-plus"></i> Create Your Account</h3>
    <p class="text-muted mb-4">Join Dilshan Stores to save and manage your bills.</p>

    <form method="POST" action="register.php" data-validate novalidate>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" data-rule="required"
               value="<?= sanitize($username) ?>">
        <div class="invalid-feedback-custom"><?= $errors['username'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" data-rule="required|email"
               value="<?= sanitize($email) ?>">
        <div class="invalid-feedback-custom"><?= $errors['email'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" data-rule="required|minlen6">
        <div class="invalid-feedback-custom"><?= $errors['password'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" data-rule="required|match-password">
        <div class="invalid-feedback-custom"><?= $errors['confirm_password'] ?? '' ?></div>
      </div>
      <button type="submit" class="btn btn-ds-primary w-100 py-2">Sign Up</button>
      <p class="text-center mt-3 mb-0 small">Already have an account?
        <a href="<?= base_url('auth/login.php') ?>">Log in</a>
      </p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
