<?php
require_once __DIR__ . '/config.php';
if (current_user()) redirect('dashboard.php');
$pageTitle = 'Log in';
$error = '';
$email = '';
if (is_post()) {
    verify_csrf();
    $email = strtolower(posted_string('email', 190));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role,status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) $error = 'Email or password is incorrect.';
    elseif ($user['status'] !== 'active') $error = 'This account is not active. Please contact the administrator.';
    else {
        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        set_flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php');
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout"><div class="container auth-grid"><div class="auth-aside"><span class="eyebrow">GOOD TO SEE YOU AGAIN</span><div class="illustration">🧠📖</div><h2>Your study circle<br>is waiting.</h2><p>Pick up where you left off, find a useful note, or help another student with a question.</p></div><form class="form-card" method="post"><span class="eyebrow">WELCOME BACK</span><h2>Log in to StudyCircle</h2><p>Enter your account details below.</p><?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?><?= csrf_field() ?>
<div class="form-group"><label for="email">Email address</label><input id="email" type="email" name="email" autocomplete="username" value="<?= e($email) ?>" required></div>
<div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
<div class="checkbox-row"><input id="remember" type="checkbox" disabled><label for="remember">Remember me (not enabled in this demo)</label></div>
<button class="btn btn-primary btn-block mt-2" type="submit">Log in</button><p class="mt-2">New to StudyCircle? <a href="register.php">Create an account</a></p></form></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
