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
          <th>#</th>
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
            <td><?php echo (int) $post['id']; ?></td>
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

<?php if ($total_pages > 1) { ?>
  <?php
  $pagination_pages = array(1, $total_pages, $page - 1, $page, $page + 1);
  $pagination_pages = array_unique($pagination_pages);
  sort($pagination_pages);

  $previous_pagination_page = null;
  ?>

  <nav class="pagination" aria-label="Pagination">
    <?php foreach ($pagination_pages as $pagination_page) { ?>
      <?php if ($pagination_page < 1 || $pagination_page > $total_pages) { continue; } ?>

      <?php if ($previous_pagination_page !== null && $pagination_page > $previous_pagination_page + 1) { ?>
        <span class="ellipsis">&hellip;</span>
      <?php } ?>

      <?php if ($pagination_page === $page) { ?>
        <span class="current"><?php echo $pagination_page; ?></span>
      <?php } else { ?>
        <a href="<?php echo url($pagination_page === 1 ? '/admin/posts' : '/admin/posts/page/' . $pagination_page); ?>"><?php echo $pagination_page; ?></a>
      <?php } ?>

      <?php $previous_pagination_page = $pagination_page; ?>
    <?php } ?>
  </nav>
<?php } ?>
