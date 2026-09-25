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
  <nav class="pagination" aria-label="Pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
      <?php if ($i === $page) { ?>
        <span class="current"><?php echo $i; ?></span>
      <?php } else { ?>
        <a href="<?php echo url('/?page=' . $i); ?>"><?php echo $i; ?></a>
      <?php } ?>
    <?php } ?>
  </nav>
<?php } ?>
