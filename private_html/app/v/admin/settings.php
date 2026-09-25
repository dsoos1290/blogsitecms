<h1>Settings</h1>

<form class="admin-card" method="post" action="<?php echo url('/admin/settings'); ?>">
  <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf); ?>">

  <label>Title
    <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>">
  </label>

  <label>Description
    <textarea name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
  </label>

  <label>Disclaimer
    <textarea name="disclaimer" rows="4"><?php echo htmlspecialchars($settings['disclaimer']); ?></textarea>
  </label>

  <label>Copyright
    <textarea name="copyright" rows="2"><?php echo htmlspecialchars($settings['copyright']); ?></textarea>
  </label>

  <label>Blog language / HTML lang
    <input type="text" name="language" value="<?php echo htmlspecialchars($settings['language']); ?>" placeholder="en">
  </label>

  <label>Page slug
    <input type="text" name="page_slug" value="<?php echo htmlspecialchars($settings['page_slug']); ?>" placeholder="page">
  </label>

  <label>Continue text
    <input type="text" name="continue_text" value="<?php echo htmlspecialchars($settings['continue_text']); ?>" placeholder="Continue">
  </label>

  <label>Back text
    <input type="text" name="back_text" value="<?php echo htmlspecialchars($settings['back_text']); ?>" placeholder="Back">
  </label>

  <label>Post order
    <select name="post_order">
      <option value="created_at"<?php echo $settings['post_order'] === 'created_at' ? ' selected' : ''; ?>>created_at DESC</option>
      <option value="modified_at"<?php echo $settings['post_order'] === 'modified_at' ? ' selected' : ''; ?>>modified_at DESC</option>
    </select>
  </label>

  <label>Posts per page
    <input type="number" name="posts_per_page" min="1" max="100" value="<?php echo (int) $settings['posts_per_page']; ?>">
  </label>

  <label>Post list style
    <select name="list_layout">
      <option value="simple"<?php echo $settings['list_layout'] === 'simple' ? ' selected' : ''; ?>>Title + content only</option>
      <option value="footer"<?php echo $settings['list_layout'] === 'footer' ? ' selected' : ''; ?>>Title + content + date + Continue button</option>
    </select>
  </label>

  <button type="submit">Save settings</button>
</form>
