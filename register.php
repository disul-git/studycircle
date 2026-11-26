<?php
require_once __DIR__ . '/config.php';
if (current_user()) redirect('dashboard.php');
$pageTitle = 'Create your account';
$errors = [];
$name = $email = '';
if (is_post()) {
    verify_csrf();
    $name = posted_string('name', 120);
    $email = strtolower(posted_string('email', 190));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (mb_strlen($name) < 2) $errors[] = 'Enter your name (at least 2 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'The passwords do not match.';
    if (empty($_POST['terms'])) $errors[] = 'Please accept the terms to continue.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'An account with this email already exists.';
        else {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,'student','active')");
            $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'role'=>'student','status'=>'active'];
            set_flash('success', 'Welcome to StudyCircle! Your account is ready.');
            redirect('dashboard.php');
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-layout"><div class="container auth-grid"><div class="auth-aside"><span class="eyebrow">YOUR NEXT STUDY CHAPTER</span><div class="illustration">📚🌱</div><h2>Find your people.<br>Share what you know.</h2><p>Create an account to upload notes, ask questions, and join study groups across your subjects.</p></div><form class="form-card" method="post" novalidate><span class="eyebrow">JOIN THE COMMUNITY</span><h2>Create your account</h2><p>It only takes a minute to get started.</p><?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?><?= csrf_field() ?>
<div class="form-group"><label for="name">Full name</label><input id="name" name="name" type="text" maxlength="120" autocomplete="name" value="<?= e($name) ?>" required></div>
<div class="form-group"><label for="email">Email address</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($email) ?>" required></div>
<div class="form-group"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><div class="form-help">Use at least 8 characters.</div></div>
<div class="form-group"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
<label class="checkbox-row"><input type="checkbox" name="terms" value="1" <?= isset($_POST['terms'])?'checked':'' ?>> I agree to the <a href="privacy.php">terms and community guidelines</a>.</label>
<button class="btn btn-primary btn-block mt-2" type="submit">Create account</button><p class="mt-2">Already a member? <a href="login.php">Log in</a></p></form></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
