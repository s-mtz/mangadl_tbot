<?php

namespace App\Controller;

use Lib\Telegram;

class Bot
{
    private $tg;
    public function __construct()
    {
        $this->tg = new Telegram();
    }

    public function start()
    {
        $data = file_get_contents('php://input');
        $update = json_decode($data, true);
        var_dump(
            $this->tg->send_message_request($update['message']['from']['id'], json_encode($update))
        );
    }
}
