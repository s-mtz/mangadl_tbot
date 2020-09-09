<?php

namespace Lib;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class Telegram
{
    private $apiURL;
    private $client;

    public function __construct()
    {
        $this->apiURL =
            'https://api.telegram.org/bot' . $_ENV['BOT_TOKEN'] . '/';
        $this->client = new Client(['base_uri' => $this->apiURL]);
    }

    public function proccess_request()
    {
        $response = $this->client->get('getUpdates');
        $updates = json_decode($response->getBody());
        return $updates;
    }

    public function send_message_request(int $_chat_id, string $_message)
    {
        $this->client->post('sendMessage', [
            'query' => [
                'chat_id' => $_chat_id,
                'text' => $_message,
            ],
        ]);
    }

    public function send_file_request(int $_chat_id, string $_path)
    {
        $this->client->post('sendDocument', [
            // 'query' => [
            //     'chat_id' => $_chat_id,
            //     'Document' => fopen($_path, 'r'),
            // ],
            'multipart' => [
                ['name' => 'chat_id', 'contents' => $_chat_id],
                [
                    'name' => 'document',
                    'contents' => fopen($_path, 'r'),
                ],
            ],
        ]);
    }

    public function mangafreak(string $_url)
    {
    }
}
