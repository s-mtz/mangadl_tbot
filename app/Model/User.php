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
    public function new_user(string $_chat_id, int $_time)
    {
        $sql = "INSERT INTO user (chat_id, time) 
        VALUES ('{$_chat_id}', $_time)";

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
    public function find_user(string $_chat_id)
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
