<div class="admin-title-row">
  <h1>Posts</h1>
  <a class="button" href="<?php echo url('/admin/posts/create'); ?>">New post</a>
</div>

<?php if (empty($posts)) { ?>
  <p>No posts yet.</p>
<?php } else { ?>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Active</th>
          <th>List</th>
          <th>Sitemap</th>
          <th>Created</th>
          <th>Modified</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $post) { ?>
          <tr>
            <td><?php echo htmlspecialchars($post['title']); ?></td>
            <td><?php echo $post['active'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo $post['show_in_list'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo $post['show_in_sitemap'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo htmlspecialchars($post['created_at']); ?></td>
            <td><?php echo htmlspecialchars($post['modified_at']); ?></td>
            <td class="actions">
              <a href="<?php echo url('/admin/posts/edit?id=' . (int) $post['id']); ?>">Edit</a>
              <form method="post" action="<?php echo url('/admin/posts/delete'); ?>" onsubmit="return confirm('Delete this post?');">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                <input type="hidden" name="id" value="<?php echo (int) $post['id']; ?>">
                <button class="link-button danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>
