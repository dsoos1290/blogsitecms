<h1><?php echo htmlspecialchars($title); ?></h1>

<form class="admin-card" method="post" action="<?php echo url($form_action); ?>">
  <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf); ?>">

  <label>Title
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
  </label>

  <label>HTML content
    <textarea name="content" rows="18"><?php echo htmlspecialchars($post['content']); ?></textarea>
  </label>

  <div class="checkboxes">
    <label><input type="checkbox" name="active" value="1"<?php echo $post['active'] ? ' checked' : ''; ?>> Active</label>
    <label><input type="checkbox" name="show_in_list" value="1"<?php echo $post['show_in_list'] ? ' checked' : ''; ?>> Show in list</label>
    <label><input type="checkbox" name="show_in_sitemap" value="1"<?php echo $post['show_in_sitemap'] ? ' checked' : ''; ?>> Show in sitemap.xml</label>
  </div>

  <div class="form-actions">
    <button type="submit">Save</button>
    <a href="<?php echo url('/admin/posts'); ?>">Cancel</a>
  </div>
</form>
