<?php

namespace App\Controller;

use Lib\Telegram;
use App\Controller\Message;

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
        if (empty($data)) {
            return false;
        }
        $update = json_decode($data, true);
        if (!$update) {
            return false;
        }
        $this->tg->send_message_request($_ENV["ADMIN_ID"], $data);
        $sm = new Message();
        return $sm->listen($update['message']);
    }
}
