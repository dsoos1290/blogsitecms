<h1>Change password</h1>

<?php if ($error !== '') { ?>
  <div class="notice notice-error"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<form class="admin-card admin-card-small" method="post" action="<?php echo url('/admin/password'); ?>">
  <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf); ?>">

  <label>Current password
    <input type="password" name="current_password" required>
  </label>

  <label>New password
    <input type="password" name="new_password" required>
  </label>

  <label>New password again
    <input type="password" name="confirm_password" required>
  </label>

  <button type="submit">Change password</button>
</form>
