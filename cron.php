<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(["BOT_TOKEN"]);

$bot_api_key = $_ENV["bot_token"];
$bot_username = $_ENV["bot_username"];
