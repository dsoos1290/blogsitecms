<!doctype html>
<html lang="<?php echo htmlspecialchars(isset($settings['language']) ? $settings['language'] : APP_LANG); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="generator" content="<?php echo FW_NAME . (APP_ENV != 'prod' ? ' v' . FW_VER : ''); ?>">
  <title><?php echo htmlspecialchars(isset($title) ? $title : APP_TITLE); ?></title>
  <?php if (isset($meta_description) && $meta_description !== '') { ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
  <?php } ?>
  <link href="<?php echo url('css/app.css'); ?>?v=<?php echo APP_VER; ?><?php echo (APP_ENV != 'prod' ? '&t=' . time() : ''); ?>" rel="stylesheet">
  <link rel="icon" href="<?php echo url('favicon.ico'); ?>">
</head>
<body>
<div class="site-container">
  <header class="site-header">
    <a class="site-title" href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($settings['site_title']); ?></a>
    <?php if ($settings['site_description'] !== '') { ?>
      <p><?php echo nl2br(htmlspecialchars($settings['site_description'])); ?></p>
    <?php } ?>
  </header>

  <?php foreach (flash() as $flash) { ?>
    <div class="notice notice-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
      <?php echo htmlspecialchars($flash['message']); ?>
    </div>
  <?php } ?>

  <main class="site-main">
    <?php echo $content; ?>
  </main>

  <footer class="site-footer">
    <?php if ($settings['disclaimer'] !== '') { ?>
      <div class="disclaimer"><?php echo nl2br(htmlspecialchars($settings['disclaimer'])); ?></div>
    <?php } ?>
    <?php if ($settings['copyright'] !== '') { ?>
      <div class="copyright"><?php echo $settings['copyright']; ?></div>
    <?php } ?>
  </footer>
</div>
</body>
</html>
