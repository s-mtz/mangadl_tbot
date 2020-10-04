<?php

define('ABSPATH', __DIR__ . '/../');

$dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
$dotenv->load();
$dotenv->required([
    'HOST',
    'ADMIN_ID',
    'BOT_TOKEN',
    'MYSQL_HOST',
    'MYSQL_USER',
    'MYSQL_PASSWORD',
    'MYSQL_DATABASE',
    'DEFAULT_LIMIT',
]);
