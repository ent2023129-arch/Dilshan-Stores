<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (is_logged_in()) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

$pageTitle = 'Login';
$errors = [];
$username = '';
$successMsg = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '') {
        $errors['username'] = 'Please enter your username or email.';
    }
    if ($password === '') {
        $errors['password'] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1');
        $stmt->execute(['username' => $username, 'email' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: ' . base_url('dashboard.php'));
            exit;
        } else {
            $errors['login'] = 'Invalid username/email or password.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="ds-auth-wrapper">
  <div class="ds-form-card fade-in-up">
    <h3><i class="fa-solid fa-right-to-bracket"></i> Welcome Back</h3>
    <p class="text-muted mb-4">Log in to access your bill calculator and history.</p>

    <?php if ($successMsg): ?>
      <div class="alert alert-success"><?= sanitize($successMsg) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['login'])): ?>
      <div class="alert alert-danger"><?= sanitize($errors['login']) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" data-validate novalidate>
      <div class="mb-3">
        <label class="form-label">Username or Email</label>
        <input type="text" name="username" class="form-control" data-rule="required"
               value="<?= sanitize($username) ?>">
        <div class="invalid-feedback-custom"><?= $errors['username'] ?? '' ?></div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" data-rule="required">
        <div class="invalid-feedback-custom"><?= $errors['password'] ?? '' ?></div>
      </div>
      <button type="submit" class="btn btn-ds-primary w-100 py-2">Log In</button>
      <p class="text-center mt-3 mb-0 small">Don't have an account?
        <a href="<?= base_url('auth/register.php') ?>">Sign up</a>
      </p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
