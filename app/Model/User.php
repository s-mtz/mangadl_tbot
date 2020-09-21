<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class User extends ModelAbstract
{
    private $error = [];

    /**
     * [new_user description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_type     [$_type description]
     * @param   int     $_time     [$_time description]
     *
     * @return  [type]             [return description]
     */
    public function new_user(string $_chat_id, string $_type, int $_time)
    {
        $sql = "INSERT INTO user (chat_id, type, time) 
        VALUES ('{$_chat_id}', '{$_type}', $_time)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send new_user query to database";
            return false;
        }
        return true;
    }

    /**
     * [get_user description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     *
     * @return  [type]             [return description]
     */
    public function get_user(string $_chat_id)
    {
        $sql = "SELECT * FROM user 
        WHERE chat_id = '{$_chat_id}'";

        if (!($query = $this->conn->query($sql))) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }

        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the user in database";
                return false;
            }
            return $data[0];
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    /**
     * [uddate_type description]
     *
     * @param   string  $_chat_id   [$_chat_id description]
     * @param   string  $_old_type  [$_old_type description]
     * @param   string  $_new_type  [$_new_type description]
     *
     * @return  [type]              [return description]
     */
    public function update_type(string $_chat_id, string $_old_type, string $_new_type)
    {
        $sql = "UPDATE user SET type = '{$_new_type}' 
        WHERE chat_id = '{$_chat_id}' and type = '{$_old_type}'";

        if ($this->conn->query($sql) === true) {
            return true;
        }
        $this->error["message"] = "there is nothing in database";
        return false;
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
