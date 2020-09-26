<?php

namespace App\Controller;

use App\Model\Messages;
use App\Model\Queues;
use App\Model\Mangas;
use Lib\Telegram;

use MangaCrawlers\Manga;

class Queue
{
    private $error = [];

    public function get_mesage_threrade(string $_chat_id)
    {
        $msg = new Messages();
        $Q = new Queues();
        $tg = new Telegram();
        $manga = new Mangas();

        if (!($querry = $msg->get_all_messages($_chat_id))) {
            $this->error["message"] = "couldnt do get_all_messages";
            return false;
        }
        $start_chapter = $querry[2]['content'];
        $finish_chapter = $querry[3]['content'];

        // later on add the > if(type == 1) to handel normal and vip

        for ($i = $start_chapter; $i <= $finish_chapter; $i++) {
            $manga_existance = $manga->get_manga($querry[1]['content'], $i);
            if (!is_array($manga_existance)) {
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
            } else {
                $tg->send_file_id_request($_chat_id, $manga_existance['dir']);
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
        $manga = new Mangas();

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
                "There is a problem in DB  " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($Q->get_error()),
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
                    json_encode($download->get_error()),
                $_ENV["ADMIN_ID"]
            );
        }

        $message = $tg->send_file_request(
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
                "_" .
                $manga_q['chapter'] .
                ".pdf"
        );

        if (!$message) {
            $this->error_function("There is a problem in sending the files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in SendFile  " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($tg->get_error()),
                $_ENV["ADMIN_ID"]
            );
        } else {
            $manga->set_manga(
                $message['file_id'],
                $manga_q['crawler'],
                $manga_q['manga'],
                $manga_q['chapter'],
                time()
            );
        }
        if (!$Q->update_queue($manga_q['id'], $manga_q['chat_id'], "processing", "finished")) {
            return $this->error_function(
                "Problem in complete queue Q id : " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($Q->get_error()),
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
