<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class UsersMeta extends ModelAbstract
{
    private $error = [];

    /**
     * [add_meta description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_key      [$_key description]
     * @param   string  $_value    [$_value description]
     *
     * @return  [type]             [return description]
     */
    public function add_meta(string $_chat_id, string $_key, string $_value)
    {
        $sql = "INSERT INTO user_meta (`chat_id`, `key`, `value`) 
                VALUES ('{$_chat_id}', '{$_key}', '{$_value}')";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send query to database";
            return false;
        }
        return true;
    }

    /**
     * [update_meta description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_key      [$_key description]
     * @param   string  $_value    [$_value description]
     *
     * @return  [type]             [return description]
     */
    public function update_meta(string $_chat_id, string $_key, string $_value)
    {
        $sql = "UPDATE user_meta SET `value`='{$_value}'
        WHERE `chat_id`='{$_chat_id}' AND `key`='{$_key}'";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send query to database";
            return false;
        }
        return true;
    }

    /**
     * [get_value description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_key      [$_key description]
     *
     * @return  [type]             [return description]
     */
    public function get_value(string $_chat_id, string $_key)
    {
        $sql = "SELECT * FROM user_meta 
                WHERE chat_id = '{$_chat_id}' and `key` = '{$_key}' 
                ORDER BY id DESC LIMIT 1";

        if (!($query = $this->conn->query($sql))) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }

        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            return $data[0]['value'];
        }
        return false;
    }

    /**
     *
     *
     * @param [type] $_chat_id
     * @return void
     */
    public function get_all_users(string $_chat_id)
    {
        $sql = "SELECT * FROM user_meta 
        WHERE chat_id = '{$_chat_id}'";

        $query = $this->conn->query($sql);
        if (!$query) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }
        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            return $data;
        }
        return null;
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
