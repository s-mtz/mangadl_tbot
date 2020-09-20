<?php

namespace App\Controller;
use App\Model\Messages;
use Lib\Telegram;

use MangaCrawlers\Validator;

class Message
{
    private $error = [];
    private $request;
    private $db;
    private $tg;
    private $arr = ["start" => "/start", "help" => "/help"];

    public function __construct()
    {
        $this->request = new Validator();
        $this->db = new Messages();
        $this->tg = new Telegram();
    }

    public function listen($_bot)
    {
        if (in_array($_bot['text'], $this->arr)) {
            $this->Start_Helper($_bot, $this->arr);
            return true;
        }

        $result = $this->db->get_last_messages($_bot['from']['id']);

        if (!$result) {
            return $this->crawler_check($_bot);
        } elseif ($result['type'] == 'crawler') {
            return $this->manga_check($_bot);
        } elseif ($result['type'] == 'manga') {
            return $this->chapter_start_check($_bot);
        } elseif ($result['type'] == 'chapter_start') {
            return $this->chapter_finish_check($_bot);
        }
    }

    private function Start_Helper($_bot, $_arr)
    {
        if (array_search($_bot['text'], $_arr) == "start") {
            $this->tg->send_message_request($_bot['from']['id'], "welcome to the bot");
            return true;
        }
        if (array_search($_bot['text'], $_arr) == "help") {
            $this->tg->send_message_request($_bot['from']['id'], "send your request");
            return true;
        }
    }

    private function crawler_check($_bot)
    {
        if ($this->request->check_crawler($_bot['text'])) {
            $this->db->set_messages($_bot['from']['id'], $_bot['text'], 'crawler', $_bot['date']);
            $this->tg->send_message_request(
                $_bot['from']['id'],
                "the crawler has been set secsusfully"
            );
            return true;
        }
        $this->tg->send_message_request($_bot['from']['id'], "please send the crawler correctly");
        $this->error["message"] = "didnt recive the right crawler name";
        return false;
    }

    private function manga_check($_bot)
    {
        if (
            $this->request->check_manga(
                $this->db->get_last_messages($_bot['from']['id'], "crawler")['content'],
                $_bot['text']
            )
        ) {
            $this->db->set_messages($_bot['from']['id'], $_bot['text'], 'manga', $_bot['date']);
            $this->tg->send_message_request(
                $_bot['from']['id'],
                "the manga has been set secsusfully"
            );
            return true;
        }
        $this->tg->send_message_request($_bot['from']['id'], "please send the manga correctly");
        $this->error["message"] = "didnt recive the right manga name";
        return false;
    }

    private function chapter_start_check($_bot)
    {
        if (
            $this->request->check_chapter(
                $this->db->get_last_messages($_bot['from']['id'], "crawler")['content'],
                $this->db->get_last_messages($_bot['from']['id'], "manga")['content'],
                intval($_bot['text'])
            )
        ) {
            $this->db->set_messages(
                $_bot['from']['id'],
                $_bot['text'],
                'chapter_start',
                $_bot['date']
            );
            $this->tg->send_message_request(
                $_bot['from']['id'],
                "the starting chpter has been set"
            );
            return true;
        }
        $this->tg->send_message_request(
            $_bot['from']['id'],
            "please send the starting chapter correctly"
        );
        $this->error["message"] = "didnt recive the right starting chapter";
        return false;
    }

    private function chapter_finish_check($_bot)
    {
        if (
            $this->request->check_chapter(
                $this->db->get_last_messages($_bot['from']['id'], "crawler")['content'],
                $this->db->get_last_messages($_bot['from']['id'], "manga")['content'],
                intval($_bot['text'])
            )
        ) {
            $this->db->set_messages(
                $_bot['from']['id'],
                $_bot['text'],
                'chapter_finish',
                $_bot['date']
            );
            $this->tg->send_message_request(
                $_bot['from']['id'],
                "the finishing chpter has been set"
            );
            return true;
        }
        $this->tg->send_message_request(
            $_bot['from']['id'],
            "please send the starting chapter correctly"
        );
        $this->error["message"] = "didnt recive the right finishing chapter";
        return false;
    }

    public function get_error()
    {
        if (empty($this->error)) {
            return false;
        }
        return $this->error;
    }
}