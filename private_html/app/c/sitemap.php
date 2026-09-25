<?php
class Sitemap extends App {
  public function index() {
    $settings = $this->settings();

    if (defined('APP_URL') && APP_URL !== '') {
      $base = rtrim(APP_URL, '/') . BASE_URL;
    } else {
      $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
      $scheme = $https ? 'https' : 'http';
      $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
      $base = $scheme . '://' . $host . BASE_URL;
    }

    $result = $this->db->query(
      "SELECT id, modified_at FROM " . $this->table('posts') .
      " WHERE active = 1 AND show_in_sitemap = 1 ORDER BY modified_at DESC"
    );

    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($base . '/', ENT_QUOTES, 'UTF-8') . "</loc>\n";
    echo "  </url>\n";

    while ($row = $result->fetch_assoc()) {
      echo "  <url>\n";
      echo '    <loc>' . htmlspecialchars($base . '/post?id=' . (int) $row['id'], ENT_QUOTES, 'UTF-8') . "</loc>\n";
      echo '    <lastmod>' . date('c', strtotime($row['modified_at'])) . "</lastmod>\n";
      echo "  </url>\n";
    }

    echo '</urlset>';
    exit;
  }
}
