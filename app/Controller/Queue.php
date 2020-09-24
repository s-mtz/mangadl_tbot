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

    private function error_function($_message, $_chat_id)
    {
        $tg = new Telegram();
        $this->error["message"] = $_message;
        $tg->send_message_request($_chat_id, $_message);
        return false;
    }

    public function run(int $_count = 1)
    {
        $tg = new Telegram();
        $Q = new Queues();
        if ($Q->get_processing_count() > $_count) {
            return null;
        }
        $download = new Manga();
        $manga_q = $Q->get_queue("pending");
        if (!$manga_q) {
            return false;
        }
        if (!$Q->update_queue($manga_q['id'], $manga_q['chat_id'], "pending", "processing")) {
            $this->error_function("Couldn't proccess your job", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in DB  " . $manga_q['id'] . " ERROR : " . $Q->get_error(),
                $_ENV["ADMIN_ID"]
            );
        }
        if (
            !$download->downloader(
                $manga_q['crawler'],
                $manga_q['manga'],
                $manga_q['chapter'],
                ABSPATH . "upload/"
            )
        ) {
            $this->error_function("There is a problem in donwloading files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in donwloading files " .
                    $manga_q['id'] .
                    " ERROR : " .
                    $download->get_error(),
                $_ENV["ADMIN_ID"]
            );
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
            $this->error_function("There is a problem in sending the files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in SendFile  " .
                    $manga_q['id'] .
                    " ERROR : " .
                    $tg->get_error(),
                $_ENV["ADMIN_ID"]
            );
        }
        if (!$Q->update_queue($manga_q['id'], $manga_q['chat_id'], "processing", "finished")) {
            return $this->error_function(
                "Problem in complete queue Q id : " .
                    $manga_q['id'] .
                    " ERROR : " .
                    $Q->get_error(),
                $_ENV["ADMIN_ID"]
            );
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
