CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_list` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_sitemap` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `list_idx` (`active`, `show_in_list`, `created_at`),
  KEY `list_modified_idx` (`active`, `show_in_list`, `modified_at`),
  KEY `sitemap_idx` (`active`, `show_in_sitemap`, `modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`, `password_hash`, `created_at`)
SELECT 'admin', 'sha256$10000$blogsitecms-initial-admin$abf87665fe5f391bae162278db32a79c91af28caf99f0e3165c9a5974de921b1', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin');

INSERT INTO `settings` (`key`, `value`) VALUES
  ('site_title', 'BlogSite CMS'),
  ('site_description', ''),
  ('disclaimer', ''),
  ('copyright', '&copy; 2026 BlogSite CMS'),
  ('post_order', 'created_at'),
  ('posts_per_page', '10'),
  ('list_layout', 'footer'),
  ('language', 'en'),
  ('page_slug', 'page'),
  ('continue_text', 'Continue'),
  ('back_text', 'Back')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
