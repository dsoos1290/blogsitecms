<?php
class Pages extends App {
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $settings = $this->settings();

    $page_slug = isset($settings['page_slug']) ? trim($settings['page_slug']) : 'page';
    if (!preg_match('/^[a-z][a-z0-9-]*$/', $page_slug)) {
      $page_slug = 'page';
    }

    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $request_path = parse_url($request_uri, PHP_URL_PATH);
    $home_path = BASE_URL !== '' ? BASE_URL . '/' : '/';

    if (
      isset($_GET['page'])
      && !isset($_GET['page_slug'])
      && ($request_path === $home_path || $request_path === rtrim($home_path, '/'))
    ) {
      $query_page = (int) $_GET['page'];
      $this->redirect($query_page > 1 ? '/' . $page_slug . '/' . $query_page : '/');
    }

    if (isset($_GET['page_slug'])) {
      if ($_GET['page_slug'] !== $page_slug) {
        header('HTTP/1.1 404 Not Found');
        die('Page not found.');
      }

      if (!isset($_GET['page']) || !ctype_digit((string) $_GET['page'])) {
        header('HTTP/1.1 404 Not Found');
        die('Page not found.');
      }

      if ((int) $_GET['page'] <= 1) {
        $this->redirect('/');
      }
    }

    $order = $settings['post_order'] === 'modified_at'
      ? 'modified_at'
      : 'created_at';

    $per_page = (int) $settings['posts_per_page'];
    if ($per_page < 1) {
      $per_page = 10;
    }

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1) {
      $page = 1;
    }

    $count_result = $this->db->query(
      "SELECT COUNT(*) AS total FROM " . $this->table('posts') .
      " WHERE active = 1 AND show_in_list = 1"
    );
    $count_row = $count_result->fetch_assoc();
    $total = (int) $count_row['total'];
    $total_pages = $total > 0 ? (int) ceil($total / $per_page) : 1;

    if ($page > $total_pages) {
      $this->redirect(
        $total_pages > 1
          ? '/' . $page_slug . '/' . $total_pages
          : '/'
      );
    }

    $offset = ($page - 1) * $per_page;

    $result = $this->db->query(
      "SELECT id, title, content, created_at, modified_at " .
      "FROM " . $this->table('posts') .
      " WHERE active = 1 AND show_in_list = 1 " .
      "ORDER BY " . $order . " DESC, id DESC " .
      "LIMIT " . $offset . ", " . $per_page
    );

    $posts = array();
    while ($row = $result->fetch_assoc()) {
      $posts[] = $row;
    }

    $this->set(array(
      'title' => $settings['site_title'],
      'meta_description' => $settings['site_description'],
      'settings' => $settings,
      'posts' => $posts,
      'page' => $page,
      'page_slug' => $page_slug,
      'total_pages' => $total_pages,
      'date_field' => $order
    ));
    $this->render('pages/index');
  }

  public function post() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $request_path = parse_url($request_uri, PHP_URL_PATH);
    $legacy_post_path = (BASE_URL !== '' ? BASE_URL : '') . '/post';

    if ($request_path === $legacy_post_path && isset($_GET['id'])) {
      $legacy_id = (int) $_GET['id'];
      if ($legacy_id > 0) {
        $this->redirect('/' . $legacy_id);
      }
    }

    if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
      header('HTTP/1.1 404 Not Found');
      die('Post not found.');
    }

    $id = (int) $_GET['id'];

    $result = $this->db->query(
      "SELECT id, title, content, created_at, modified_at " .
      "FROM " . $this->table('posts') .
      " WHERE id = " . $id . " AND active = 1 LIMIT 1"
    );

    $post = $result->fetch_assoc();

    if (!$post) {
      header('HTTP/1.1 404 Not Found');
      die('Post not found.');
    }

    $settings = $this->settings();

    $order = $settings['post_order'] === 'modified_at'
      ? 'modified_at'
      : 'created_at';

    $this->set(array(
      'title' => $post['title'] . ' - ' . $settings['site_title'],
      'meta_description' => $settings['site_description'],
      'settings' => $settings,
      'post' => $post,
      'date_field' => $order
    ));
    $this->render('pages/post');
  }
}
