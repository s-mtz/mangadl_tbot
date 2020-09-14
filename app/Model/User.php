<?php

class User
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
        VALUES ($_chat_id, $_type, $_time)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send new_user query to database";
            return false;
        }
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
        $sql = "SELECT chat_id FROM user WHERE chat_id = $_chat_id";
        if ($this->conn->query($sql) === true) {
            if (empty($this->conn->query($sql)->fetch_all())) {
                $this->error["message"] = "couldnt find the user in database";
                return false;
            }
            return json_encode($this->conn->query($sql)->fetch_all());
        } else {
            $this->error["message"] = "couldnt send get_user query to database";
            return false;
        }
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
    public function uddate_type(string $_chat_id, string $_old_type, string $_new_type)
    {
        $sql = "UPDATE user SET type = $_new_type 
                WHERE chat_id = $_chat_id and type = $_old_type";

        if ($this->conn->query($sql) === true) {
            if (empty($this->conn->query($sql)->fetch_all())) {
                $this->error["message"] = "couldnt find the user in database";
                return false;
            }
            return json_encode($this->conn->query($sql)->fetch_all());
        } else {
            $this->error["message"] = "couldnt send uddate_type query to database";
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
