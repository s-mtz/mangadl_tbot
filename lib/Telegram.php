<?php

namespace Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Telegram
{
    private $apiURL;
    private $client;

    public function __construct()
    {
        $this->apiURL = 'https://api.telegram.org/bot' . $_ENV['BOT_TOKEN'] . '/';
        $this->client = new Client(['base_uri' => $this->apiURL]);
    }

    public function proccess_request($offset = 0)
    {
        $response = $this->client->get('getUpdates', [
            "query" => ["offset" => $offset],
        ]);
        $updates = json_decode($response->getBody(), true);
        return $updates;
    }

    public function send_message_request(int $_chat_id, string $_message)
    {
        $res = $this->client->post('sendMessage', [
            'query' => [
                'chat_id' => $_chat_id,
                'text' => $_message,
            ],
        ]);
        if ($res->getStatusCode() !== 200) {
            return false;
        }
        $response = json_decode($res->getBody()->getContents());

        return $response->ok;
    }

    public function send_file_request(int $_chat_id, string $_path)
    {
        $res = $this->client->post('sendDocument', [
            'multipart' => [
                ['name' => 'chat_id', 'contents' => $_chat_id],
                [
                    'name' => 'document',
                    'contents' => fopen($_path, 'r'),
                ],
            ],
        ]);

        if ($res->getStatusCode() !== 200) {
            return false;
        }
        $response = json_decode($res->getBody()->getContents());

        return $response->ok;
    }
}
