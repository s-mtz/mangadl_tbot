<?php

namespace app\Model;

use mysqli;

class UserMeta
{
    private $conn;
    private $error = [];

    /**
     * [__construct description]
     *
     * @return  [type]  [return description]
     */
    public function __construct()
    {
        $this->conn = new mysqli(
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD'],
            $_ENV['MYSQL_DATABASE']
        );
        if ($this->conn->connect_error) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }
    }

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

        if ($this->conn->query($sql)->num_rows > 0) {
            $data = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            return $data[0];
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
