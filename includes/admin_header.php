<?php
$pageTitle = $pageTitle ?? 'Admin';
$user = current_user();
$flash = get_flash();
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · StudyCircle Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css"></head><body class="admin-body">
<div class="admin-shell"><aside class="admin-sidebar">
<a class="brand" href="../index.php"><img class="brand-logo" src="../assets/images/studycircle-logo.png" alt="NSBM StudyCircle"></a>
<p class="sidebar-label">ADMIN WORKSPACE</p>
<a href="index.php">▦ Overview</a><a href="users.php">♙ Manage users</a><a href="resources.php">▤ Resource approvals</a><a href="subjects.php">▣ Subjects &amp; categories</a><a href="forum.php">☷ Forum moderation</a><a href="reports.php">▥ Reports</a>
<div class="sidebar-bottom"><a href="../dashboard.php">← Student site</a><a href="../logout.php">Log out</a></div>
</aside><div class="admin-main"><header class="admin-topbar"><div><span class="eyebrow">STUDYCIRCLE / ADMIN</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e($user['name'] ?? 'Administrator') ?></span></header>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="admin-content">
