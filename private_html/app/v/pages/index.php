<?php if (empty($posts)) { ?>
  <p>No posts yet.</p>
<?php } ?>

<?php foreach ($posts as $post) { ?>
  <article class="post-card">
    <h2><a href="<?php echo url('/' . (int) $post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
    <div class="post-content"><?php echo $post['content']; ?></div>

    <?php if ($settings['list_layout'] === 'footer') { ?>
      <div class="post-footer">
        <time datetime="<?php echo htmlspecialchars($post[$date_field]); ?>">
          <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($post[$date_field])) . ' ' . APP_TZ); ?>
        </time>
        <a class="button" href="<?php echo url('/' . (int) $post['id']); ?>"><?php echo htmlspecialchars($settings['continue_text']); ?></a>
      </div>
    <?php } ?>
  </article>
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
        <a href="<?php echo url($pagination_page === 1 ? '/' : '/' . $page_slug . '/' . $pagination_page); ?>"><?php echo $pagination_page; ?></a>
      <?php } ?>

      <?php $previous_pagination_page = $pagination_page; ?>
    <?php } ?>
  </nav>
<?php } ?>
