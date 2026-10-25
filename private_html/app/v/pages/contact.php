<article class="single-post">
  <h1><?php echo htmlspecialchars($settings['contact_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
  <?php if ($error !== '') { ?>
    <div class="notice notice-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <form id="contact-form" method="post" action="<?php echo htmlspecialchars(url('/' . $settings['contact_slug']), ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
    <label>Name <input type="text" name="name" autocomplete="name" maxlength="255" required value="<?php echo htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8'); ?>"></label>
    <label>Email <input type="email" name="email" autocomplete="email" maxlength="254" required value="<?php echo htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8'); ?>"></label>
    <label>Message <textarea name="message" rows="8" maxlength="10000" required><?php echo htmlspecialchars($values['message'], ENT_QUOTES, 'UTF-8'); ?></textarea></label>
    <?php if ($captcha) { ?>
      <div id="contact-captcha"></div>
      <p id="captcha-error" role="alert" hidden>CAPTCHA is unavailable. Please reload and try again.</p>
    <?php } ?>
    <button type="submit">Send message</button>
  </form>
  <?php if ($settings['contact_description'] !== '') { ?>
    <div class="contact-description"><?php echo nl2br(htmlspecialchars($settings['contact_description'], ENT_QUOTES, 'UTF-8')); ?></div>
  <?php } ?>
  <?php if ($settings['list_layout'] === 'footer') { ?>
    <div class="post-footer"><a class="button" href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($settings['back_text']); ?></a></div>
  <?php } ?>
</article>
<?php if ($captcha) { ?>
<script>
(function () {
  var form = document.getElementById('contact-form'), widget = null;
  function failed() {
    document.getElementById('captcha-error').hidden = false;
  }
  window.contactCaptchaReady = function () {
    widget = grecaptcha.render('contact-captcha', {
      sitekey: <?php echo json_encode(defined('APP_GOOGLE_HIDDEN_RECAPTCHA_SITE_KEY') ? APP_GOOGLE_HIDDEN_RECAPTCHA_SITE_KEY : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
      size: 'invisible', callback: function () { form.submit(); }, 'error-callback': failed
    });
  };
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (widget === null) { failed(); return; }
    if (form.checkValidity()) { grecaptcha.execute(widget); }
  });
}());
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=contactCaptchaReady&amp;render=explicit" async defer></script>
<?php } ?>
