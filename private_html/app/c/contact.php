<?php
class Contact extends App {
  public function index() {
    $settings = $this->settings();
    // Check the path itself: query parameters must not select a different page.
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    if ($settings['contact_enabled'] !== '1' || rtrim($path, '/') !== BASE_URL . '/' . $settings['contact_slug']) {
      header('HTTP/1.1 404 Not Found');
      die('Page not found.');
    }
    $values = array('name' => '', 'email' => '', 'message' => '');
    $error = '';
    $captcha = APP_GOOGLE_HIDDEN_RECAPTCHA_SITE_KEY !== '' || APP_GOOGLE_HIDDEN_RECAPTCHA_SECRET_KEY !== '';
    if ($this->requestIsPost()) {
      $this->checkCsrf();
      foreach ($values as $key => $value) {
        $values[$key] = isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
      }
      if ($values['name'] === '' || strlen($values['name']) > 255
        || strlen($values['email']) > 254 || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)
        || $values['message'] === '' || strlen($values['message']) > 10000) {
        $error = 'Please enter your name (up to 255 bytes), a valid email address and a message (up to 10,000 bytes).';
      } elseif (isset($_SESSION['contact_last_sent']) && time() - $_SESSION['contact_last_sent'] < 60) {
        $error = 'Please wait a minute before sending another message.';
      } elseif ($captcha && !$this->verifyCaptcha()) {
        $error = 'CAPTCHA verification failed. Please try again.';
      } else {
        try {
          $saved = $this->db->query("INSERT INTO " . $this->table('contact_messages') .
            " (name, email, message, extra, created_at) VALUES ('" .
            $this->db->real_escape_string($values['name']) . "', '" .
            $this->db->real_escape_string($values['email']) . "', '" .
            $this->db->real_escape_string($values['message']) . "', '', NOW())");
        } catch (Exception $e) {
          $saved = false;
          error_log('Contact message database save failed.');
        }
        if (!$saved) {
          $error = 'Your message could not be saved. Please try again later.';
        } else {
          $id = (int) $this->db->insert_id;
          $_SESSION['contact_last_sent'] = time();
          if (APP_MAIL_TO !== '') {
            $sent = $this->sendMail(APP_MAIL_TO, $settings['contact_title'] . ' - new message',
              "Name: " . $values['name'] . "\nEmail: " . $values['email'] . "\n\n" . $values['message'] .
              "\n\n" . rtrim(APP_MAIL_BASE_URL, '/') . '/admin/messages', $values['email']);
            $this->recordMailStatus($id, 'notification_status', $sent);
          }
          if ($settings['contact_confirmation'] === '1') {
            $sent = $this->sendMail($values['email'], $settings['contact_title'] . ' - message received',
              "Thank you for contacting " . $settings['site_title'] . ".\n\nWe have received your message.\n\n" .
              rtrim(APP_MAIL_BASE_URL, '/') . '/' . $settings['contact_slug'], '');
            $this->recordMailStatus($id, 'confirmation_status', $sent);
          }
          $this->flash('success', 'Thank you. Your message has been received.');
          $this->redirect('/' . $settings['contact_slug']);
        }
      }
    }
    $this->set(array('title' => $settings['contact_title'] . ' - ' . $settings['site_title'],
      'meta_description' => $settings['site_description'], 'settings' => $settings,
      'is_post' => true, 'values' => $values, 'error' => $error,
      'csrf' => $this->csrfToken(), 'captcha' => $captcha));
    $this->render('pages/contact');
  }

  private function verifyCaptcha() {
    if (APP_GOOGLE_HIDDEN_RECAPTCHA_SITE_KEY === '' || APP_GOOGLE_HIDDEN_RECAPTCHA_SECRET_KEY === ''
      || !isset($_POST['g-recaptcha-response']) || !is_string($_POST['g-recaptcha-response'])) {
      return false;
    }
    $result = $this->httpPost('https://www.google.com/recaptcha/api/siteverify',
      http_build_query(array('secret' => APP_GOOGLE_HIDDEN_RECAPTCHA_SECRET_KEY,
        'response' => $_POST['g-recaptcha-response'])), array('Content-Type: application/x-www-form-urlencoded'));
    return is_array($result) && !empty($result['success']);
  }

  private function sendMail($to, $subject, $body, $reply_to) {
    if (APP_MAIL_API_KEY === '' || !filter_var(APP_MAIL_FROM, FILTER_VALIDATE_EMAIL)
      || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    $payload = array('from' => str_replace(array("\r", "\n", '<', '>'), '', APP_MAIL_FROM_NAME) . ' <' . APP_MAIL_FROM . '>',
      'to' => array($to), 'subject' => str_replace(array("\r", "\n"), ' ', $subject), 'text' => $body);
    if ($reply_to !== '') {
      $payload['reply_to'] = $reply_to;
    }
    $json = json_encode($payload);
    if ($json === false) {
      return false;
    }
    $result = $this->httpPost('https://api.resend.com/emails', $json,
      array('Authorization: Bearer ' . APP_MAIL_API_KEY, 'Content-Type: application/json'));
    return is_array($result) && !empty($result['id']);
  }

  private function httpPost($url, $body, $headers) {
    if (!function_exists('curl_init')) {
      return false;
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body,
      CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 15,
      CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2));
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($response === false || $status < 200 || $status >= 300) {
      return false;
    }
    return json_decode($response, true);
  }

  private function recordMailStatus($id, $column, $sent) {
    try {
      $this->db->query("UPDATE " . $this->table('contact_messages') . " SET " . $column .
        " = '" . ($sent ? 'sent' : 'failed') . "' WHERE id = " . (int) $id);
    } catch (Exception $e) {
      error_log('Contact message email status update failed.');
    }
    if (!$sent) {
      error_log('Contact message ' . (int) $id . ': ' . $column . ' email failed.');
    }
  }
}
