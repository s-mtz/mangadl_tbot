<?php

namespace App\Controller;

use App\Model\Messages;
use App\Model\Queues;
use App\Controller\Users;
use App\Model\Mangas;
use I18n;
use Lib\Telegram;
use MangaCrawlers\Manga;

class Queue
{
    private $error = [];
    private $messages;
    private $queue_model;
    private $telegram;
    private $mangas;
    private $user;
    public function __construct()
    {
        $this->messages = new Messages();
        $this->queue_model = new Queues();
        $this->telegram = new Telegram();
        $this->mangas = new Mangas();
        $this->user = new Users();
    }

    public function get_mesage_threrade(string $_chat_id)
    {
        $querry = $this->messages->get_all_messages($_chat_id);
        if (!$querry) {
            $this->error["message"] = "couldnt do get_all_messages";
            return false;
        }

        $start_chapter = $querry[2]['content'];
        $user_vip = $this->user->get_meta($_chat_id, "vip");
        if ($user_vip) {
            $finish_chapter = $querry[3]['content'];
        } else {
            $finish_chapter = $querry[2]['content'];
        }

        if (!$this->messages->finish($_chat_id)) {
            $this->error["message"] = "couldnt erase the message from database";
            return false;
        }

        for ($i = $start_chapter; $i <= $finish_chapter; $i++) {
            $manga_existance = $this->mangas->get_manga($querry[1]['content'], $i);
            if (!is_array($manga_existance)) {
                if (
                    !$this->queue_model->set_queue(
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
                } else {
                    continue;
                }
            } else {
                if ($this->user->get_meta($_chat_id, 'limit') < 1) {
                    $this->queue_model->change_pendings($_chat_id, "out_of_stock");
                    $this->tg->send_message_request($_chat_id, I18n::get("OutOfStock"));
                    return false;
                }
                if (
                    $this->telegram->send_file_id_request_pdf(
                        $_chat_id,
                        $manga_existance['pdf_id'],
                        $querry[1]['content'] . " ch " . $i . " PDF"
                    ) &&
                    $this->telegram->send_file_id_request_zip(
                        $_chat_id,
                        $manga_existance['zip_id'],
                        $querry[1]['content'] . " ch " . $i . " ZIP"
                    )
                ) {
                    if (
                        !$this->queue_model->set_queue(
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
                        $this->user->update_limit($_chat_id, -1);
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
        $this->telegram = new Telegram();
        $this->error["message"] = $_message;
        $this->telegram->send_message_request($_chat_id, $_message);
        return false;
    }

    public function run(int $_count = 1)
    {
        if ($this->queue_model->get_processing_count() > $_count) {
            return null;
        }
        $download = new Manga();
        $manga_q = $this->queue_model->get_queue("pending");
        if (!$manga_q) {
            return false;
        }
        if ($this->user->get_meta($manga_q['chat_id'], 'limit') < 1) {
            $this->queue_model->change_pendings($manga_q['chat_id'], "out_of_stock");
            $this->tg->send_message_request($manga_q['chat_id'], I18n::get("OutOfStock"));
            return false;
        }
        if (!$this->queue_model->update_queue($manga_q['id'], "processing")) {
            $this->queue_model->update_queue($manga_q['id'], "error");
            $this->error_function("Couldn't proccess your job", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in DB  " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($this->queue_model->get_error()),
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
            $this->queue_model->update_queue($manga_q['id'], "error");
            $this->error_function("There is a problem in donwloading files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in donwloading files " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($download->get_error()),
                $_ENV["ADMIN_ID"]
            );
        }

        $messageZIP = $this->telegram->send_file_request(
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

        $messagePDF = $this->telegram->send_file_request(
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
            $this->queue_model->update_queue($manga_q['id'], "error");
            $this->error_function("There is a problem in sending the files", $manga_q['chat_id']);
            return $this->error_function(
                "There is a problem in SendFile  " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($this->telegram->get_error()),
                $_ENV["ADMIN_ID"]
            );
        } else {
            $this->user->update_limit($manga_q['chat_id'], -1);
            $this->mangas->set_manga(
                $messagePDF['file_id'],
                $messageZIP['file_id'],
                $manga_q['crawler'],
                $manga_q['manga'],
                $manga_q['chapter'],
                time()
            );
        }

        if (!$this->queue_model->update_queue($manga_q['id'], "finished")) {
            $this->queue_model->update_queue($manga_q['id'], "error");
            return $this->error_function(
                "Problem in complete queue Q id : " .
                    $manga_q['id'] .
                    " ERROR : " .
                    json_encode($this->queue_model->get_error()),
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
