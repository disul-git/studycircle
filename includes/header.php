<?php
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/studycircle-favicon.png">
<link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/studycircle-favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="index.php"><img class="brand-logo" src="assets/images/studycircle-logo.png" alt="NSBM StudyCircle"></a>
  <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
  <nav class="main-nav">
    <a href="index.php">Home</a>
    <a href="resources.php">Browse resources</a>
    <a href="forum.php">Q&amp;A forum</a>
    <a href="groups.php">Study groups</a>
    <?php if ($user): ?>
      <a href="dashboard.php">Dashboard</a>
      <?php if (user_is_admin()): ?><a href="admin/index.php">Admin</a><?php endif; ?>
      <a class="nav-avatar-link" href="profile.php" title="<?= e($user['name']) ?>" aria-label="Profile: <?= e($user['name']) ?>"><span class="nav-avatar" data-tooltip="<?= e($user['name']) ?>"><?php if (!empty($user['profile_picture'])): ?><img src="<?= e($user['profile_picture']) ?>" alt=""><?php else: ?><?= e(mb_strtoupper(mb_substr($user['name'],0,1))) ?><?php endif; ?></span></a>
      <form class="inline-form" action="logout.php" method="post"><?= csrf_field() ?><button class="btn btn-outline btn-small" type="submit">Log out</button></form>
    <?php else: ?>
      <a href="login.php">Log in</a><a class="btn btn-primary btn-small" href="register.php">Join StudyCircle</a>
    <?php endif; ?>
  </nav>
</header>
<?php if ($flash): ?>
<div class="container flash-wrap"><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div></div>
<?php endif; ?>
<main>
