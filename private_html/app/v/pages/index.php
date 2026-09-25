<?php if (empty($posts)) { ?>
  <p>No posts yet.</p>
<?php } ?>

<?php foreach ($posts as $post) { ?>
  <article class="post-card">
    <h2><a href="<?php echo url('/post?id=' . (int) $post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
    <div class="post-content"><?php echo $post['content']; ?></div>

    <?php if ($settings['list_layout'] === 'footer') { ?>
      <div class="post-footer">
        <time datetime="<?php echo htmlspecialchars($post[$date_field]); ?>">
          <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($post[$date_field])) . ' ' . APP_TZ); ?>
        </time>
        <a class="button" href="<?php echo url('/post?id=' . (int) $post['id']); ?>">Continue</a>
      </div>
    <?php } ?>
  </article>
<?php } ?>

<?php if ($total_pages > 1) { ?>
  <?php
  $pagination_items = array();

  if ($total_pages <= 9) {
    for ($i = 1; $i <= $total_pages; $i++) {
      $pagination_items[] = $i;
    }
  } elseif ($page <= 4) {
    for ($i = 1; $i <= 5; $i++) {
      $pagination_items[] = $i;
    }
    $pagination_items[] = 'ellipsis';
    $pagination_items[] = $total_pages;
  } elseif ($page >= $total_pages - 3) {
    $pagination_items[] = 1;
    $pagination_items[] = 'ellipsis';
    for ($i = $total_pages - 4; $i <= $total_pages; $i++) {
      $pagination_items[] = $i;
    }
  } else {
    $pagination_items[] = 1;
    $pagination_items[] = 'ellipsis';
    for ($i = $page - 2; $i <= $page + 2; $i++) {
      $pagination_items[] = $i;
    }
    $pagination_items[] = 'ellipsis';
    $pagination_items[] = $total_pages;
  }
  ?>

  <nav class="pagination" aria-label="Pagination">
    <?php foreach ($pagination_items as $item) { ?>
      <?php if ($item === 'ellipsis') { ?>
        <span class="ellipsis">&hellip;</span>
      <?php } elseif ($item === $page) { ?>
        <span class="current"><?php echo $item; ?></span>
      <?php } else { ?>
        <a href="<?php echo url($item === 1 ? '/' : '/page/' . $item); ?>"><?php echo $item; ?></a>
      <?php } ?>
    <?php } ?>
  </nav>
<?php } ?>
