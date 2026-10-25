<h1>Contact messages</h1>
<?php if (!$messages) { ?><p>No messages yet.</p><?php } ?>
<?php foreach ($messages as $message) { ?>
  <?php $status = in_array($message['status'], array('approved', 'rejected'), true) ? $message['status'] : 'pending'; ?>
  <?php $status_labels = array('pending' => 'Awaiting reply', 'approved' => 'Replied', 'rejected' => 'No reply needed'); ?>
  <article id="message-<?php echo (int) $message['id']; ?>" class="admin-card message-content message-<?php echo $status; ?>">
    <p class="message-status"><strong>Status: <?php echo $status_labels[$status]; ?></strong></p>
    <h2>#<?php echo (int) $message['id']; ?> &mdash; <?php echo htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><a href="mailto:<?php echo htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8'); ?></a><br>
    <time><?php echo htmlspecialchars($message['display_date'], ENT_QUOTES, 'UTF-8'); ?></time></p>
    <div><?php echo nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')); ?></div>
    <?php if ($message['extra'] !== '') { ?>
      <pre class="message-extra"><code><small><?php echo htmlspecialchars($message['extra'], ENT_QUOTES, 'UTF-8'); ?></small></code></pre>
    <?php } ?>
    <p><small>Admin email: <?php echo htmlspecialchars($message['notification_status'], ENT_QUOTES, 'UTF-8'); ?> &middot;
      Sender confirmation: <?php echo htmlspecialchars($message['confirmation_status'], ENT_QUOTES, 'UTF-8'); ?></small></p>
    <div class="actions">
      <?php foreach (array('approved' => 'Replied', 'rejected' => 'No reply needed') as $action => $label) { ?>
        <form method="post" action="<?php echo url('/admin/messages/status'); ?>" onsubmit="return confirm('<?php echo $status === $action ? 'Reset this message to awaiting reply?' : 'Set message status to: ' . $label . '?'; ?>');">
          <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="id" value="<?php echo (int) $message['id']; ?>">
          <input type="hidden" name="page" value="<?php echo (int) $page; ?>">
          <input type="hidden" name="status" value="<?php echo $action; ?>">
          <button type="submit" aria-pressed="<?php echo $status === $action ? 'true' : 'false'; ?>" title="<?php echo $status === $action ? 'Reset to awaiting reply' : $label; ?>"><?php echo $action === 'approved' ? '&#10003;' : '&#10007;'; ?> <?php echo $label; ?></button>
        </form>
      <?php } ?>
    </div>
  </article>
<?php } ?>
<?php if ($total_pages > 1) { ?>
  <nav class="pagination" aria-label="Message pages">
    <?php if ($page > 1) { ?><a href="<?php echo url('/admin/messages?page=' . ($page - 1)); ?>">Previous</a><?php } ?>
    <span class="current"><?php echo (int) $page; ?> / <?php echo (int) $total_pages; ?></span>
    <?php if ($page < $total_pages) { ?><a href="<?php echo url('/admin/messages?page=' . ($page + 1)); ?>">Next</a><?php } ?>
  </nav>
<?php } ?>
