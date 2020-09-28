<?php

namespace App\Controller;

use App\Model\UsersMeta;

class User
{
    private $error = [];

    private $UM;

    public function __construct()
    {
        $this->UM = new UsersMeta();
    }

    public function set_vip(string $_chat_id)
    {
        if (!$this->UM->add_meta($_chat_id, "vip", strtotime('+1 month', time()))) {
            $this->error["message"] = "couldnt connect set_vip to usermeta database";
            return false;
        }
        return true;
    }

    public function is_vip(string $_chat_id)
    {
        $value = $this->UM->get_value($_chat_id, "vip");
        if (!$value) {
            return false;
        }
        $expired_time = $value['value'];
        $flag = (bool) (time() <= $expired_time);
        return $flag;
    }

    public function get_error()
    {
        if (empty($this->error)) {
            return false;
        }
        return $this->error;
    }
}
