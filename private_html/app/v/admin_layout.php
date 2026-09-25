<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title); ?> - Admin</title>
  <link href="<?php echo url('css/app.css'); ?>?v=<?php echo APP_VER; ?><?php echo (APP_ENV != 'prod' ? '&t=' . time() : ''); ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-container">
  <header class="admin-header">
    <strong>BlogSite CMS</strong>
    <?php if (isset($_SESSION['admin_user_id'])) { ?>
      <nav>
        <a href="<?php echo url('/admin/posts'); ?>">Posts</a>
        <a href="<?php echo url('/admin/settings'); ?>">Settings</a>
        <a href="<?php echo url('/admin/password'); ?>">Password</a>
        <a href="<?php echo url('/'); ?>" target="_blank">View site</a>
        <a href="<?php echo url('/admin/logout'); ?>">Logout</a>
      </nav>
    <?php } ?>
  </header>

  <?php foreach (flash() as $flash) { ?>
    <div class="notice notice-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
      <?php echo htmlspecialchars($flash['message']); ?>
    </div>
  <?php } ?>

  <main class="admin-main">
    <?php echo $content; ?>
  </main>
</div>
</body>
</html>
