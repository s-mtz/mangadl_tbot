<?php
require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/bootstrap/env.php';
use GuzzleHttp\Client;

$client = new Client(['base_uri' => $_ENV["HOST"]]);

$client->post('queue/3', ["timeout" => 700]);
