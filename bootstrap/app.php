<?php
include 'env.php';
require ABSPATH . '/route/Route.php';
require ABSPATH . '/i18n/I18n.php';

$app = new Route\Route();

return $app->start();
