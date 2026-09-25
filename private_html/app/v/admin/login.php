<div class="admin-card admin-card-small">
  <h1>Login</h1>

  <?php if ($error !== '') { ?>
    <div class="notice notice-error"><?php echo htmlspecialchars($error); ?></div>
  <?php } ?>

  <form method="post" action="<?php echo url('/admin/login'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf); ?>">

    <label>Username
      <input type="text" name="username" required autofocus>
    </label>

    <label>Password
      <input type="password" name="password" required>
    </label>

    <button type="submit">Login</button>
  </form>
</div>
