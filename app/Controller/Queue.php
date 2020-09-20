<?php

namespace App\Controller;
use App\Model\Messages;
use App\Model\Queues;

use MangaCrawlers\Validator;

class Queue
{
    private $error = [];

    public function get_mesage_threrade(string $_chat_id)
    {
        $msg = new Messages();
        $Q = new Queues();
        if (
            !$Q->set_queue(
                $_chat_id,
                $msg->get_all_messages($_chat_id)[0],
                $msg->get_all_messages($_chat_id)[1],
                $msg->get_all_messages($_chat_id)[2],
                1,
                time(),
                "pendindg"
            )
        ) {
            $this->error["message"] = "couldnt set queue";
            return false;
        }
        if (!$msg->finish($_chat_id)) {
            $this->error["message"] = "couldnt erase the message from database";
            return false;
        }
        return true;
    }

    public function get_error()
    {
        if (empty($this->error)) {
            return false;
        }
        return $this->error;
    }
}
