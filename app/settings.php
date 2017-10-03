<?php

return [
  'settings' => [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    'db' => array(
      'host' => getenv('DB_HOST'),
      'user' => getenv('DB_USER'),
      'pass' => getenv('DB_PASS'),
      'dbname' => getenv('DB_NAME')
    )
  ]
];