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
        $update = json_decode($data, true);
        $sm = new Message();
        $sm->listen($update['message']);
    }
}
