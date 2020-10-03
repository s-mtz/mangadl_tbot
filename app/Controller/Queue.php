<?php

namespace App\Controller;

use App\Model\Messages;
use App\Model\Queues;
use App\Model\UsersMeta;
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
        $meta = new UsersMeta();

        $querry = $msg->get_all_messages($_chat_id);
        if (!$querry) {
            $this->error["message"] = "couldnt do get_all_messages";
            return false;
        }

        $start_chapter = $querry[2]['content'];
        $user_vip = $meta->get_value($_chat_id, "vip");
        if ($user_vip) {
            $finish_chapter = $querry[3]['content'];
        } else {
            $finish_chapter = $querry[2]['content'];
        }

        if (!$msg->finish($_chat_id)) {
            $this->error["message"] = "couldnt erase the message from database";
            return false;
        }

        for ($i = $start_chapter; $i <= $finish_chapter; $i++) {
            $manga_existance = $manga->get_manga($querry[1]['content'], $i);
            if (!is_array($manga_existance)) {
                if (
                    !$Q->set_queue(
                        $_chat_id,
                        $querry[0]['content'],
                        $querry[1]['content'],
                        $i,
                        (int) $user_vip,
                        time(),
                        "pending"
                    )
                ) {
                    $this->error["message"] = "couldnt do set_queue properly";
                    return false;
                }
            } else {
                if (
                    $tg->send_file_id_request_pdf(
                        $_chat_id,
                        $manga_existance['pdf_id'],
                        $querry[1]['content'] . " ch " . $i . " PDF"
                    ) &&
                    $tg->send_file_id_request_zip(
                        $_chat_id,
                        $manga_existance['zip_id'],
                        $querry[1]['content'] . " ch " . $i . " ZIP"
                    )
                ) {
                    if (
                        !$Q->set_queue(
                            $_chat_id,
                            $querry[0]['content'],
                            $querry[1]['content'],
                            $i,
                            (int) $user_vip,
                            time(),
                            "finished"
                        )
                    ) {
                        $this->error["message"] = "couldnt do set_queue properly";
                        return false;
                    } else {
                        continue;
                    }
                }
                $this->error["message"] = "couldnt do send_file_id_request properly";
                return false;
            }
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
        if (!$Q->update_queue($manga_q['id'], "processing")) {
            $Q->update_queue($manga_q['id'], "error");
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
            $Q->update_queue($manga_q['id'], "error");
            $this->error_function("There is a problem in donwloading files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in donwloading files " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($download->get_error()),
                $_ENV["ADMIN_ID"]
            );
        }

        $messageZIP = $tg->send_file_request(
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
                ".zip",
            $manga_q['manga'] . " ch " . $manga_q['chapter'] . " ZIP"
        );

        $messagePDF = $tg->send_file_request(
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
                ".pdf",
            $manga_q['manga'] . " ch " . $manga_q['chapter'] . " PDF"
        );

        if (!$messagePDF || !$messageZIP) {
            $Q->update_queue($manga_q['id'], "error");
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
                $messagePDF['file_id'],
                $messageZIP['file_id'],
                $manga_q['crawler'],
                $manga_q['manga'],
                $manga_q['chapter'],
                time()
            );
        }

        if (!$Q->update_queue($manga_q['id'], "finished")) {
            $Q->update_queue($manga_q['id'], "error");
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
