<?php

namespace App\Controller;

use App\Model\UsersMeta;

class Users
{
    /**
     *
     * @var array
     */
    private $error = [];
    /**
     *
     * @var array
     */
    protected $meta = [];
    /**
     *
     * @var  App\Model\UsersMeta
     */
    private $user_meta;

    public function __construct()
    {
        $this->user_meta = new UsersMeta();
    }

    /**
     * [set_vip description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     *
     * @return  [type]             [return description]
     */
    public function set_meta(string $_chat_id, string $_key, string $_value)
    {
        if (is_string($this->get_meta($_chat_id, $_key))) {
            if (!$this->user_meta->update_meta($_chat_id, $_key, $_value)) {
                $this->error["message"] = "couldnt connect set_meta to usermeta database";
                return false;
            }
        } elseif (!$this->user_meta->add_meta($_chat_id, $_key, $_value)) {
            $this->error["message"] = "couldnt connect set_meta to usermeta database";
            return false;
        }
        $this->meta[$_chat_id][$_key] = $_value;
        return true;
    }

    /**
     * Undocumented function
     *
     * @param string $_chat_id
     * @param string $_key
     * @return void
     */
    public function get_meta(string $_chat_id, string $_key)
    {
        if (!isset($this->meta[$_chat_id])) {
            $this->fill_metas($_chat_id);
        }
        if (!isset($this->meta[$_chat_id][$_key])) {
            return false;
        }
        return $this->meta[$_chat_id][$_key];
    }

    /**
     * Undocumented function
     *
     * @param string $_chat_id
     * @return void
     */
    private function fill_metas(string $_chat_id)
    {
        $user_metas = $this->user_meta->get_all_users($_chat_id);
        if (!$user_metas) {
            $this->error = $this->user_meta->get_error();
            return false;
        }
        if (is_null($user_metas)) {
            return false;
        }
        $tmp = [];
        foreach ($user_metas as $key => $value) {
            $tmp[$value["key"]] = filter_var($value["value"], FILTER_SANITIZE_STRING);
        }
        $this->meta[$_chat_id] = $tmp;
    }

    /**
     * Undocumented function
     *
     * @param string $_chat_id
     * @param integer $_value
     * @return void
     */
    public function update_limit(string $_chat_id, int $_value)
    {
        $last = $this->get_meta($_chat_id, "limit");
        if (!$last) {
            return false;
        }
        $current = $last + $_value;
        return $this->set_meta($_chat_id, "limit", $current);
    }

    /**
     * [get_error description]
     *
     * @return  [type]  [return description]
     */
    public function get_error()
    {
        if (empty($this->error)) {
            return false;
        }
        return $this->error;
    }
}
