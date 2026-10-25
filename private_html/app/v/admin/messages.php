<h1>Contact messages</h1>
<?php if (!$messages) { ?><p>No messages yet.</p><?php } ?>
<?php foreach ($messages as $message) { ?>
  <article class="admin-card message-content">
    <h2>#<?php echo (int) $message['id']; ?> &mdash; <?php echo htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><a href="mailto:<?php echo htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8'); ?></a><br>
    <time><?php echo htmlspecialchars($message['display_date'], ENT_QUOTES, 'UTF-8'); ?></time></p>
    <div><?php echo nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')); ?></div>
    <p><small>Admin email: <?php echo htmlspecialchars($message['notification_status'], ENT_QUOTES, 'UTF-8'); ?> &middot;
      Sender confirmation: <?php echo htmlspecialchars($message['confirmation_status'], ENT_QUOTES, 'UTF-8'); ?></small></p>
  </article>
<?php } ?>
<?php if ($total_pages > 1) { ?>
  <nav class="pagination" aria-label="Message pages">
    <?php if ($page > 1) { ?><a href="<?php echo url('/admin/messages?page=' . ($page - 1)); ?>">Previous</a><?php } ?>
    <span class="current"><?php echo (int) $page; ?> / <?php echo (int) $total_pages; ?></span>
    <?php if ($page < $total_pages) { ?><a href="<?php echo url('/admin/messages?page=' . ($page + 1)); ?>">Next</a><?php } ?>
  </nav>
<?php } ?>
