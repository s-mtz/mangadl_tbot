<?php

namespace App\Controller;
use App\Model\Messages;
use App\Model\Queues;
use Lib\Telegram;

use MangaCrawlers\Manga;

use function PHPSTORM_META\type;

class Queue
{
    private $error = [];

    public function get_mesage_threrade(string $_chat_id)
    {
        $msg = new Messages();
        $Q = new Queues();
        $start_chapter = $msg->get_all_messages($_chat_id)[2]['content'];
        $finish_chapter = $msg->get_all_messages($_chat_id)[3]['content'];

        // later on add the > if(type == 1) to handel normal and vip

        for ($i = $start_chapter; $i <= $finish_chapter; $i++) {
            $Q->set_queue(
                $_chat_id,
                $msg->get_all_messages($_chat_id)[0]['content'],
                $msg->get_all_messages($_chat_id)[1]['content'],
                $i,
                1,
                time(),
                "pendindg"
            );
        }

        if (!$msg->finish($_chat_id)) {
            $this->error["message"] = "couldnt erase the message from database";
            return false;
        }
        return true;
    }

    public function run_queue(int $count = null)
    {
        $Q = new Queues();
        $download = new Manga();
        $tg = new Telegram();
        $manga_q = $Q->get_queue("pendindg");
        $Q->update_queue($manga_q['id'], $manga_q['chat_id'], "pendindg", "processing");
        $download->downloader(
            $manga_q['crawler'],
            $manga_q['manga'],
            $manga_q['chapter'],
            ABSPATH . "upload/"
        );
        $Q->update_queue($manga_q['id'], $manga_q['chat_id'], "processing", "finished");
        $tg->send_file_request(
            $manga_q['chat_id'],
            ABSPATH .
                "upload/" .
                $manga_q['crawler'] .
                "/" .
                $manga_q['manga'] .
                "/" .
                $manga_q['chapter'] .
                "/" .
                $manga_q['manga'] .
                " " .
                $manga_q['chapter'] .
                ".pdf"
        );
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
