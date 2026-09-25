<?php
return array(
  'GET' => array(
    '/' => array(
      'controller' => 'pages',
      'class' => 'Pages',
      'action' => 'index'
    ),
    '/page/{page}' => array(
      'controller' => 'pages',
      'class' => 'Pages',
      'action' => 'index'
    ),
    '/post' => array(
      'controller' => 'pages',
      'class' => 'Pages',
      'action' => 'post'
    ),
    '/sitemap.xml' => array(
      'controller' => 'sitemap',
      'class' => 'Sitemap',
      'action' => 'index'
    ),
    '/admin' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'index'
    ),
    '/admin/login' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'login'
    ),
    '/admin/logout' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'logout'
    ),
    '/admin/posts' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'posts'
    ),
    '/admin/posts/create' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'postCreate'
    ),
    '/admin/posts/edit' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'postEdit'
    ),
    '/admin/settings' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'settingsPage'
    ),
    '/admin/password' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'password'
    )
  ),
  'POST' => array(
    '/admin/login' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'login'
    ),
    '/admin/posts/create' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'postCreate'
    ),
    '/admin/posts/edit' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'postEdit'
    ),
    '/admin/posts/delete' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'postDelete'
    ),
    '/admin/settings' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'settingsPage'
    ),
    '/admin/password' => array(
      'controller' => 'admin',
      'class' => 'Admin',
      'action' => 'password'
    )
  )
);
