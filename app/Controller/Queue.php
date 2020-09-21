<?php

namespace App\Controller;
use App\Model\Messages;
use App\Model\Queues;
use Lib\Telegram;

use MangaCrawlers\Manga;

class Queue
{
    private $error = [];

    public function get_mesage_threrade(string $_chat_id)
    {
        $msg = new Messages();
        $Q = new Queues();
        if (!($querry = $msg->get_all_messages($_chat_id))) {
            $this->error["message"] = "couldnt do get_all_messages";
            return false;
        }
        $start_chapter = $querry[2]['content'];
        $finish_chapter = $querry[3]['content'];

        // later on add the > if(type == 1) to handel normal and vip

        for ($i = $start_chapter; $i <= $finish_chapter; $i++) {
            if (
                !$Q->set_queue(
                    $_chat_id,
                    $querry[0]['content'],
                    $querry[1]['content'],
                    $i,
                    1,
                    time(),
                    "pending"
                )
            ) {
                $this->error["message"] = "couldnt do set_queue properly";
                return false;
            }
        }

        if (!$msg->finish($_chat_id)) {
            $this->error["message"] = "couldnt erase the message from database";
            return false;
        }
        return true;
    }

    public function run(int $count = null)
    {
        $Q = new Queues();
        $download = new Manga();
        $tg = new Telegram();
        if (!($manga_q = $Q->get_queue("pending"))) {
            $this->error["message"] = "there is no pending queue";
            return false;
        }
        if (!$Q->update_queue($manga_q['id'], $manga_q['chat_id'], "pending", "processing")) {
            $this->error["message"] = "error happend in update pending to processing";
            return false;
        }
        if (
            !$download->downloader(
                $manga_q['crawler'],
                $manga_q['manga'],
                $manga_q['chapter'],
                ABSPATH . "upload/"
            )
        ) {
            $this->error["message"] = "couldent use the downloader";
            return false;
        }
        if (!$Q->update_queue($manga_q['id'], $manga_q['chat_id'], "processing", "finished")) {
            $this->error["message"] = "error happend in update processing to finished";
            return false;
        }
        if (
            !$tg->send_file_request(
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
            )
        ) {
            $this->error["message"] = "couldnt upload the file";
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
