<?php
class Pages extends App {
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $settings = $this->settings();

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
      $page = $total_pages;
    }

    $offset = ($page - 1) * $per_page;

    $result = $this->db->query(
      "SELECT id, title, content, created_at, modified_at " .
      "FROM " . $this->table('posts') .
      " WHERE active = 1 AND show_in_list = 1 " .
      "ORDER BY " . $order . " DESC " .
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
      'total_pages' => $total_pages,
      'date_field' => $order
    ));
    $this->render('pages/index');
  }

  public function post() {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

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

    $this->set(array(
      'title' => $post['title'] . ' - ' . $settings['site_title'],
      'meta_description' => $settings['site_description'],
      'settings' => $settings,
      'post' => $post
    ));
    $this->render('pages/post');
  }
}
