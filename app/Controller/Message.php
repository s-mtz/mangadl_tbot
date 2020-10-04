<?php

namespace App\Controller;
use App\Model\Messages;
use App\Model\User;
use App\Controller\Users;
use Lib\Telegram;
use App\Controller\Queue;
use App\Model\Queues;
use I18n;
use MangaCrawlers\Validator;

class Message
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    private $error = [];
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $request;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $queue_model;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $user_meta;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $db;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $user;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $tg;
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $Q;
    /**
     * Undocumented variable
     *
     * @var array
     */
    private $arr = [
        "start" => "/start",
        "help" => "/help",
        "English" => "/english",
        "Persian" => "/persian",
        "Cancel" => "/cancel",
    ];

    public function __construct()
    {
        $this->request = new Validator();
        $this->db = new Messages();
        $this->tg = new Telegram();
        $this->user = new User();
        $this->user_meta = new Users();
        $this->Q = new Queue();
        $this->queue_model = new Queues();
    }

    public function listen($_bot)
    {
        $language = $this->user_meta->get_meta($_bot['from']['id'], "language");

        if ($language === false) {
            I18n::set_language("En_us");
        }
        if ($language == "Fa_ir") {
            I18n::set_language("Fa_ir");
        }
        if ($language == "En_us") {
            I18n::set_language("En_us");
        }

        if (!$this->user->find_user($_bot['from']['id'])) {
            if (!$this->user->new_user($_bot['from']['id'], $_bot['date'])) {
                $this->error["message"] = "couldnt add the new user to database";
                return false;
            }
        }

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
            $messages = $this->chapter_finish_check($_bot);
            if ($messages) {
                $set_queue_result = $this->Q->get_mesage_threrade($_bot['from']['id']);
                if (!$set_queue_result) {
                    $this->error["queue"] = $this->Q->get_error();
                }
                return $set_queue_result;
            }
            return false;
        }
    }

    private function Start_Helper($_bot, $_arr)
    {
        if (array_search($_bot['text'], $_arr) == "start") {
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("start"));
            return true;
        } elseif (array_search($_bot['text'], $_arr) == "help") {
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("help"));
            return true;
        } elseif (array_search($_bot['text'], $_arr) == "English") {
            $this->user_meta->set_meta($_bot['from']['id'], "language", "En_us");
            I18n::set_language('En_us');
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("English"));
            return true;
        } elseif (array_search($_bot['text'], $_arr) == "Persian") {
            $this->user_meta->set_meta($_bot['from']['id'], "language", "Fa_ir");
            I18n::set_language('Fa_ir');
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("Persian"));
            return true;
        } elseif (array_search($_bot['text'], $_arr) == "Cancel") {
            $this->queue_model->cancel($_bot['from']['id']);
            return true;
        }
    }

    private function crawler_check($_bot)
    {
        if ($this->request->check_crawler($_bot['text'])) {
            $this->db->set_messages($_bot['from']['id'], $_bot['text'], 'crawler', $_bot['date']);
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("Crawler_success"));
            return true;
        }
        $this->tg->send_message_request($_bot['from']['id'], I18n::get("Crawler_error"));
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
            $this->tg->send_message_request($_bot['from']['id'], I18n::get("Manga_success"));
            return true;
        }
        $this->tg->send_message_request($_bot['from']['id'], I18n::get("Manga_error"));
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
                I18n::get("Starting_chapter_success")
            );
            return true;
        }
        $this->tg->send_message_request($_bot['from']['id'], I18n::get("Starting_chapter_error"));
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
            if ($this->user_meta->get_meta($_bot['from']['id'], "vip")) {
                $this->tg->send_message_request(
                    $_bot['from']['id'],
                    I18n::get("Finishing_chapter_success_VIP")
                );
                return true;
            } else {
                $this->tg->send_message_request(
                    $_bot['from']['id'],
                    I18n::get("Finishing_chapter_success_NORMAL")
                );
                return true;
            }
        }
        $this->tg->send_message_request($_bot['from']['id'], I18n::get("Finishing_chapter_error"));
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
