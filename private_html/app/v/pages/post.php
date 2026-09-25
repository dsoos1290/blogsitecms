<article class="single-post">
  <h1><?php echo htmlspecialchars($post['title']); ?></h1>
  <div class="post-content"><?php echo $post['content']; ?></div>
  <p class="back-link"><a href="<?php echo url('/'); ?>">&larr; Back</a></p>
</article>
