<?php

class Messages
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
     * [set_messages description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_content  [$_content description]
     * @param   string  $_type     [$_type description]
     * @param   int     $_time     [$_time description]
     *
     * @return  [type]             [return description]
     */
    public function set_messages(string $_chat_id, string $_content, string $_type, int $_time)
    {
        $sql = "INSERT INTO messages (chat_id, content, type, time) 
        VALUES ($_chat_id, $_content, $_type, $_time)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send set_messages query to database";
            return false;
        }
    }

    /**
     * [get_last_messages description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_type     [$_type description]
     *
     * @return  [type]             [return description]
     */
    public function get_last_messages(string $_chat_id, string $_type)
    {
        $sql = "SELECT chat_id, type FROM messages WHERE chat_id = $_chat_id and type = $_type ORDER BY id DESC LIMIT 1";
        if ($this->conn->query($sql) === true) {
            if (empty($this->conn->query($sql)->fetch_all())) {
                $this->error["message"] = "couldnt find the request in database";
                return false;
            }
            return json_encode($this->conn->query($sql)->fetch_all());
        } else {
            $this->error["message"] = "couldnt send get_last_messages query to database";
            return false;
        }
    }

    /**
     * [finish description]
     *
     * @return  [type]  [return description]
     */
    public function finish()
    {
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
