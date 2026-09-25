<?php
class App extends C {
  private $settings_cache = null;

  public function __construct() {
    parent::__construct();
  }

  protected function settings() {
    if ($this->settings_cache !== null) {
      return $this->settings_cache;
    }

    $settings = array(
      'site_title' => APP_TITLE,
      'site_description' => '',
      'disclaimer' => '',
      'copyright' => '&copy; ' . date('Y') . ' ' . APP_TITLE,
      'post_order' => 'created_at',
      'posts_per_page' => '10',
      'list_layout' => 'footer',
      'language' => APP_LANG,
      'direction' => 'ltr',
      'date_format' => 'Y-m-d H:i',
      'page_slug' => 'page',
      'post_slug' => '',
      'continue_text' => 'Continue',
      'back_text' => 'Back',
      'head_code' => '',
      'body_code' => ''
    );

    $result = $this->db->query(
      "SELECT `key`, `value` FROM " . $this->table('settings')
    );

    while ($row = $result->fetch_assoc()) {
      $settings[$row['key']] = $row['value'];
    }

    $this->settings_cache = $settings;
    return $settings;
  }

  protected function saveSetting($key, $value) {
    $key = $this->db->real_escape_string($key);
    $value = $this->db->real_escape_string($value);

    $this->db->query(
      "INSERT INTO " . $this->table('settings') . " (`key`, `value`) " .
      "VALUES ('" . $key . "', '" . $value . "') " .
      "ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
    );

    $this->settings_cache = null;
  }

  protected function isAdmin() {
    return isset($_SESSION['admin_user_id']) && (int) $_SESSION['admin_user_id'] > 0;
  }

  protected function requireAdmin() {
    if (!$this->isAdmin()) {
      $this->redirect('/admin/login');
    }
  }

  protected function csrfToken() {
    if (!isset($_SESSION['_csrf']) || $_SESSION['_csrf'] === '') {
      $_SESSION['_csrf'] = sha1(uniqid(mt_rand(), true));
    }

    return $_SESSION['_csrf'];
  }

  protected function checkCsrf() {
    if (
      !isset($_POST['_csrf'])
      || !isset($_SESSION['_csrf'])
      || $_POST['_csrf'] !== $_SESSION['_csrf']
    ) {
      header('HTTP/1.1 400 Bad Request');
      die('Invalid request token.');
    }
  }

  protected function passwordHash($password, $salt = null) {
    if ($salt === null) {
      if (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes(16);
        $salt = bin2hex($bytes);
      } else {
        $salt = sha1(uniqid(mt_rand(), true));
      }
    }

    $iterations = 10000;
    $hash = $salt . $password;

    for ($i = 0; $i < $iterations; $i++) {
      $hash = hash('sha256', $hash . $password . $salt);
    }

    return 'sha256$' . $iterations . '$' . $salt . '$' . $hash;
  }

  protected function passwordVerify($password, $stored) {
    $parts = explode('$', $stored);

    if (count($parts) !== 4 || $parts[0] !== 'sha256') {
      return false;
    }

    $iterations = (int) $parts[1];
    $salt = $parts[2];
    $hash = $salt . $password;

    for ($i = 0; $i < $iterations; $i++) {
      $hash = hash('sha256', $hash . $password . $salt);
    }

    $calculated = 'sha256$' . $iterations . '$' . $salt . '$' . $hash;

    if (strlen($calculated) !== strlen($stored)) {
      return false;
    }

    $result = 0;
    $length = strlen($stored);
    for ($i = 0; $i < $length; $i++) {
      $result |= ord($calculated[$i]) ^ ord($stored[$i]);
    }

    return $result === 0;
  }

  protected function requestIsPost() {
    return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
  }
}
