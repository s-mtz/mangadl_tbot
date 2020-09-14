<?php

class UserMeta
{
    private $conn;

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
            $this->error["message"] = "couldnt send connect to database";
            return false;
        }
    }

    /**
     * [add_meta description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_key      [$_key description]
     * @param   [type]  $_value    [$_value description]
     *
     * @return  [type]             [return description]
     */
    public function add_meta(string $_chat_id, string $_key, $_value)
    {
        $sql = "INSERT INTO user_meta (chat_id, key, value) 
        VALUES ($_chat_id, $_key, $_value)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send new_user query to database";
            return false;
        }
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
        $sql = "SELECT chat_id, key FROM user_meta WHERE chat_id = $_chat_id and key = $_key";
        if ($this->conn->query($sql) === true) {
            if (empty($this->conn->query($sql)->fetch_all())) {
                $this->error["message"] = "couldnt find the user in database";
                return false;
            }
            return json_encode($this->conn->query($sql)->fetch_all());
        } else {
            $this->error["message"] = "couldnt send get_value query to database";
            return false;
        }
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
