<?php
include 'env.php';
require '../route/Route.php';

$app = new Route\Route();

return $app->start();
