<article class="single-post">
  <h1><?php echo htmlspecialchars($post['title']); ?></h1>
  <div class="post-content"><?php echo $post['content']; ?></div>

  <?php if ($settings['list_layout'] === 'footer') { ?>
    <div class="post-footer">
      <time datetime="<?php echo htmlspecialchars($post[$date_field]); ?>">
        <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($post[$date_field])) . ' ' . APP_TZ); ?>
      </time>
      <a class="button" href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($settings['back_text']); ?></a>
    </div>
  <?php } ?>
</article>
