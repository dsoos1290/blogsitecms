<?php
class Admin extends App {
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->requireAdmin();
    $this->redirect('/admin/posts');
  }

  public function login() {
    if ($this->isAdmin()) {
      $this->redirect('/admin/posts');
    }

    $error = '';

    if ($this->requestIsPost()) {
      $this->checkCsrf();

      $username = isset($_POST['username']) ? trim($_POST['username']) : '';
      $password = isset($_POST['password']) ? $_POST['password'] : '';
      $username_sql = $this->db->real_escape_string($username);

      $result = $this->db->query(
        "SELECT id, username, password_hash FROM " . $this->table('users') .
        " WHERE username = '" . $username_sql . "' LIMIT 1"
      );
      $user = $result->fetch_assoc();

      if ($user && $this->passwordVerify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $this->redirect('/admin/posts');
      }

      $error = 'Invalid username or password.';
    }

    $this->set(array(
      'title' => 'Admin login',
      'error' => $error,
      'csrf' => $this->csrfToken()
    ));
    $this->render('admin/login', 'admin_layout');
  }

  public function logout() {
    unset($_SESSION['admin_user_id'], $_SESSION['admin_username']);
    session_regenerate_id(true);
    $this->redirect('/admin/login');
  }

  public function posts() {
    $this->requireAdmin();

    $settings = $this->settings();

    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $request_path = parse_url($request_uri, PHP_URL_PATH);
    $admin_posts_path = (BASE_URL !== '' ? BASE_URL : '') . '/admin/posts';

    if ($request_path === $admin_posts_path && isset($_GET['page'])) {
      $query_page = (int) $_GET['page'];
      $this->redirect(
        $query_page > 1
          ? '/admin/posts/page/' . $query_page
          : '/admin/posts'
      );
    }

    $order = $settings['post_order'] === 'modified_at'
      ? 'modified_at'
      : 'created_at';

    $per_page = (int) $settings['posts_per_page'];
    if ($per_page < 1) {
      $per_page = 10;
    }

    if (isset($_GET['page']) && !ctype_digit((string) $_GET['page'])) {
      header('HTTP/1.1 404 Not Found');
      die('Page not found.');
    }

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page <= 1 && isset($_GET['page'])) {
      $this->redirect('/admin/posts');
    }

    $count_result = $this->db->query(
      "SELECT COUNT(*) AS total FROM " . $this->table('posts')
    );
    $count_row = $count_result->fetch_assoc();
    $total = (int) $count_row['total'];
    $total_pages = $total > 0 ? (int) ceil($total / $per_page) : 1;

    if ($page > $total_pages) {
      $this->redirect(
        $total_pages > 1
          ? '/admin/posts/page/' . $total_pages
          : '/admin/posts'
      );
    }

    $offset = ($page - 1) * $per_page;

    $result = $this->db->query(
      "SELECT id, title, active, show_in_list, show_in_sitemap, created_at, modified_at " .
      "FROM " . $this->table('posts') . " " .
      "ORDER BY " . $order . " DESC, id DESC " .
      "LIMIT " . $offset . ", " . $per_page
    );

    $posts = array();
    while ($row = $result->fetch_assoc()) {
      $posts[] = $row;
    }

    $this->set(array(
      'title' => 'Posts',
      'posts' => $posts,
      'page' => $page,
      'total_pages' => $total_pages,
      'csrf' => $this->csrfToken()
    ));
    $this->render('admin/posts', 'admin_layout');
  }

  public function postCreate() {
    $this->requireAdmin();

    $post = array(
      'id' => 0,
      'title' => '',
      'content' => '',
      'active' => 1,
      'show_in_list' => 1,
      'show_in_sitemap' => 1
    );

    if ($this->requestIsPost()) {
      $this->checkCsrf();
      if ($this->savePost($post)) {
        $this->flash('success', 'Post created.');
        $this->redirect('/admin/posts');
      }
    }

    $this->set(array(
      'title' => 'New post',
      'post' => $post,
      'csrf' => $this->csrfToken(),
      'form_action' => '/admin/posts/create'
    ));
    $this->render('admin/post_form', 'admin_layout');
  }

  public function postEdit() {
    $this->requireAdmin();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $result = $this->db->query(
      "SELECT id, title, content, active, show_in_list, show_in_sitemap " .
      "FROM " . $this->table('posts') . " WHERE id = " . $id . " LIMIT 1"
    );
    $post = $result->fetch_assoc();

    if (!$post) {
      header('HTTP/1.1 404 Not Found');
      die('Post not found.');
    }

    if ($this->requestIsPost()) {
      $this->checkCsrf();
      if ($this->savePost($post)) {
        $this->flash('success', 'Post updated.');
        $this->redirect('/admin/posts');
      }
    }

    $this->set(array(
      'title' => 'Edit post',
      'post' => $post,
      'csrf' => $this->csrfToken(),
      'form_action' => '/admin/posts/edit?id=' . $id
    ));
    $this->render('admin/post_form', 'admin_layout');
  }

  private function savePost($post) {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if ($title === '') {
      $this->flash('error', 'Title is required.');
      return false;
    }

    $title_sql = $this->db->real_escape_string($title);
    $content_sql = $this->db->real_escape_string($content);
    $active = isset($_POST['active']) ? 1 : 0;
    $show_in_list = isset($_POST['show_in_list']) ? 1 : 0;
    $show_in_sitemap = isset($_POST['show_in_sitemap']) ? 1 : 0;

    if ((int) $post['id'] > 0) {
      $this->db->query(
        "UPDATE " . $this->table('posts') . " SET " .
        "title = '" . $title_sql . "', " .
        "content = '" . $content_sql . "', " .
        "active = " . $active . ", " .
        "show_in_list = " . $show_in_list . ", " .
        "show_in_sitemap = " . $show_in_sitemap . ", " .
        "modified_at = NOW() " .
        "WHERE id = " . (int) $post['id']
      );
    } else {
      $this->db->query(
        "INSERT INTO " . $this->table('posts') .
        " (title, content, active, show_in_list, show_in_sitemap, created_at, modified_at) VALUES (" .
        "'" . $title_sql . "', '" . $content_sql . "', " .
        $active . ", " . $show_in_list . ", " . $show_in_sitemap . ", NOW(), NOW())"
      );
    }

    return true;
  }

  public function postDelete() {
    $this->requireAdmin();

    if (!$this->requestIsPost()) {
      header('HTTP/1.1 405 Method Not Allowed');
      die('Method not allowed.');
    }

    $this->checkCsrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    $this->db->query(
      "DELETE FROM " . $this->table('posts') . " WHERE id = " . $id
    );

    $this->flash('success', 'Post deleted.');
    $this->redirect('/admin/posts');
  }

  public function settingsPage() {
    $this->requireAdmin();

    $favicon_path = ROOT . DS . PUB . DS . 'favicon.ico';
    $default_favicon_dir = ROOT . DS . PRIV . DS . APP . DS . 'assets';
    $default_favicon_path = $default_favicon_dir . DS . 'favicon.ico';

    if (!file_exists($default_favicon_path) && file_exists($favicon_path)) {
      if (!is_dir($default_favicon_dir)) {
        @mkdir($default_favicon_dir, 0777, true);
      }

      if (is_dir($default_favicon_dir)) {
        @copy($favicon_path, $default_favicon_path);
      }
    }

    if ($this->requestIsPost()) {
      $this->checkCsrf();

      $post_order = isset($_POST['post_order']) && $_POST['post_order'] === 'modified_at'
        ? 'modified_at'
        : 'created_at';

      $posts_per_page = isset($_POST['posts_per_page']) ? (int) $_POST['posts_per_page'] : 10;
      if ($posts_per_page < 1) {
        $posts_per_page = 1;
      }
      if ($posts_per_page > 100) {
        $posts_per_page = 100;
      }

      $list_layout = isset($_POST['list_layout']) && $_POST['list_layout'] === 'simple'
        ? 'simple'
        : 'footer';

      $language = isset($_POST['language']) ? trim($_POST['language']) : 'en';
      if (!preg_match('/^[a-zA-Z]{2,10}(?:-[a-zA-Z0-9]{2,10})?$/', $language)) {
        $language = 'en';
      }

      $direction = isset($_POST['direction']) && $_POST['direction'] === 'rtl'
        ? 'rtl'
        : 'ltr';

      $date_format = isset($_POST['date_format']) ? trim($_POST['date_format']) : 'Y-m-d H:i';
      if ($date_format === '') {
        $date_format = 'Y-m-d H:i';
      }

      $timezone = isset($_POST['timezone']) ? trim($_POST['timezone']) : (defined('APP_TZ') ? APP_TZ : 'UTC');
      if (!in_array($timezone, timezone_identifiers_list(), true)) {
        $timezone = defined('APP_TZ') ? APP_TZ : 'UTC';
      }

      $page_slug = isset($_POST['page_slug']) ? strtolower(trim($_POST['page_slug'])) : 'page';
      if (
        !preg_match('/^[a-z][a-z0-9-]*$/', $page_slug)
        || $page_slug === 'admin'
      ) {
        $page_slug = 'page';
      }

      $post_slug = isset($_POST['post_slug']) ? strtolower(trim($_POST['post_slug'])) : '';
      if (
        $post_slug !== ''
        && (
          !preg_match('/^[a-z][a-z0-9-]*$/', $post_slug)
          || $post_slug === 'admin'
        )
      ) {
        $post_slug = '';
      }

      if ($post_slug !== '' && $post_slug === $page_slug) {
        $post_slug = '';
      }

      $continue_text = isset($_POST['continue_text']) ? trim($_POST['continue_text']) : 'Continue';
      if ($continue_text === '') {
        $continue_text = 'Continue';
      }

      $back_text = isset($_POST['back_text']) ? trim($_POST['back_text']) : 'Back';
      if ($back_text === '') {
        $back_text = 'Back';
      }

      $this->saveSetting('site_title', isset($_POST['site_title']) ? trim($_POST['site_title']) : '');
      $this->saveSetting('site_description', isset($_POST['site_description']) ? trim($_POST['site_description']) : '');
      $this->saveSetting('disclaimer', isset($_POST['disclaimer']) ? trim($_POST['disclaimer']) : '');
      $this->saveSetting('copyright', isset($_POST['copyright']) ? trim($_POST['copyright']) : '');
      $this->saveSetting('post_order', $post_order);
      $this->saveSetting('posts_per_page', (string) $posts_per_page);
      $this->saveSetting('list_layout', $list_layout);
      $this->saveSetting('language', $language);
      $this->saveSetting('direction', $direction);
      $this->saveSetting('date_format', $date_format);
      $this->saveSetting('timezone', $timezone);
      $this->saveSetting('page_slug', $page_slug);
      $this->saveSetting('post_slug', $post_slug);
      $this->saveSetting('continue_text', $continue_text);
      $this->saveSetting('back_text', $back_text);
      $this->saveSetting('head_code', isset($_POST['head_code']) ? $_POST['head_code'] : '');
      $this->saveSetting('body_code', isset($_POST['body_code']) ? $_POST['body_code'] : '');

      $favicon_error = '';
      $favicon_uploaded = false;
      $favicon_restored = false;

      if (
        isset($_FILES['favicon'])
        && isset($_FILES['favicon']['error'])
        && (int) $_FILES['favicon']['error'] !== UPLOAD_ERR_NO_FILE
      ) {
        if ((int) $_FILES['favicon']['error'] !== UPLOAD_ERR_OK) {
          $favicon_error = 'Favicon upload failed.';
        } elseif (!isset($_FILES['favicon']['size']) || (int) $_FILES['favicon']['size'] > 1048576) {
          $favicon_error = 'Favicon must be 1 MB or smaller.';
        } else {
          $extension = isset($_FILES['favicon']['name'])
            ? strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION))
            : '';
          $tmp_name = isset($_FILES['favicon']['tmp_name']) ? $_FILES['favicon']['tmp_name'] : '';
          $handle = $tmp_name !== '' ? @fopen($tmp_name, 'rb') : false;
          $header = $handle ? fread($handle, 4) : false;
          if ($handle) {
            fclose($handle);
          }

          if ($extension !== 'ico' || $header !== "\x00\x00\x01\x00") {
            $favicon_error = 'Please upload a valid .ico file.';
          } elseif (!move_uploaded_file($tmp_name, $favicon_path)) {
            $favicon_error = 'Favicon could not be saved.';
          } else {
            $favicon_uploaded = true;
          }
        }
      }

      if (
        !$favicon_uploaded
        && $favicon_error === ''
        && isset($_POST['restore_favicon'])
      ) {
        if (!file_exists($default_favicon_path)) {
          $favicon_error = 'Default favicon is not available.';
        } elseif (!@copy($default_favicon_path, $favicon_path)) {
          $favicon_error = 'Default favicon could not be restored.';
        } else {
          $favicon_restored = true;
        }
      }

      if (
        !$favicon_uploaded
        && !$favicon_restored
        && $favicon_error === ''
        && isset($_POST['delete_favicon'])
        && file_exists($favicon_path)
        && !@unlink($favicon_path)
      ) {
        $favicon_error = 'Favicon could not be deleted.';
      }

      if ($favicon_error !== '') {
        $this->flash('error', $favicon_error);
      } elseif ($favicon_restored) {
        $this->flash('success', 'Default favicon restored.');
      } else {
        $this->flash('success', 'Settings saved.');
      }
      $this->redirect('/admin/settings');
    }

    $this->set(array(
      'title' => 'Settings',
      'settings' => $this->settings(),
      'favicon_exists' => file_exists($favicon_path),
      'default_favicon_exists' => file_exists($default_favicon_path),
      'csrf' => $this->csrfToken()
    ));
    $this->render('admin/settings', 'admin_layout');
  }

  public function password() {
    $this->requireAdmin();

    $error = '';

    if ($this->requestIsPost()) {
      $this->checkCsrf();

      $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
      $new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
      $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
      $id = (int) $_SESSION['admin_user_id'];

      $result = $this->db->query(
        "SELECT password_hash FROM " . $this->table('users') . " WHERE id = " . $id . " LIMIT 1"
      );
      $user = $result->fetch_assoc();

      if (!$user || !$this->passwordVerify($current, $user['password_hash'])) {
        $error = 'Current password is incorrect.';
      } elseif ($new === '') {
        $error = 'New password is required.';
      } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
      } else {
        $hash = $this->db->real_escape_string($this->passwordHash($new));
        $this->db->query(
          "UPDATE " . $this->table('users') . " SET password_hash = '" . $hash . "' WHERE id = " . $id
        );
        $this->flash('success', 'Password changed.');
        $this->redirect('/admin/password');
      }
    }

    $this->set(array(
      'title' => 'Change password',
      'error' => $error,
      'csrf' => $this->csrfToken()
    ));
    $this->render('admin/password', 'admin_layout');
  }
}

