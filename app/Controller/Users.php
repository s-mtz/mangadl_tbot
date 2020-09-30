<?php

namespace App\Controller;

use App\Model\UsersMeta;

class Users
{
    private $error = [];

    private $user_meta;

    /**
     * [set_vip description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     *
     * @return  [type]             [return description]
     */
    public function set_meta(string $_chat_id, string $_key, string $_value)
    {
        $this->user_meta = new UsersMeta();

        if (!is_string($this->user_meta->get_value($_chat_id, $_key))) {
            if (!$this->user_meta->add_meta($_chat_id, $_key, $_value)) {
                $this->error["message"] = "couldnt connect set_meta to usermeta database";
                return false;
            }
            return true;
        }
        if (!$this->user_meta->update_meta($_chat_id, $_key, $_value)) {
            $this->error["message"] = "couldnt connect set_meta to usermeta database";
            return false;
        }
        return true;
    }

    // $flag = (bool) (time() <= $expired_time);

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
