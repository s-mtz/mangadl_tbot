<?php
require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/bootstrap/env.php';
use Lib\Telegram;
use GuzzleHttp\Client;

$tg = new Telegram();
$respon = $tg->proccess_request();
$client = new Client(['base_uri' => $_ENV["HOST"]]);

$update_id = 0;

foreach ($respon["result"] as $element) {
    $client->post('bot', ['body' => json_encode($element)]);

    $update_id = $element['update_id'];
}

$tg->proccess_request($update_id + 1);
