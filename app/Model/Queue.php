<?php

namespace app\Model;

use mysqli;

class Queue
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
     * [set_queue description]
     *
     * @return  [type]  [return description]
     */
    public function set_queue(
        string $_chat_id,
        string $_crawler,
        string $_manga,
        int $_chapter,
        int $_type,
        int $_time,
        string $_status
    ) {
        $sql = "INSERT INTO queue (chat_id, crawler, manga, chapter, type, time, status) 
        VALUES ('{$_chat_id}', '{$_crawler}', '{$_manga}', $_chapter, $_type, $_time, '{$_status}')";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send set_queue query to database";
            return false;
        }
        return true;
    }

    /**
     * [get_queue description]
     *
     * @return  [type]  [return description]
     */
    public function get_queue()
    {
        // need ro be fixed i dont get how to set the get updates its so confusing po

        $sql = "SELECT chat_id, crawler, manga, chapter, type, status  FROM queue 
                ORDER BY type DESC LIMIT 1";

        if ($this->conn->query($sql)->num_rows > 0) {
            $data = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the queue in database";
                return false;
            }
            return $data[0];
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    /**
     * [update_queue description]
     *
     * @param   string  $_chat_id     [$_chat_id description]
     * @param   string  $_old_status  [$_old_status description]
     * @param   string  $_new_status  [$_new_status description]
     *
     * @return  [type]                [return description]
     */
    public function update_queue(string $_chat_id, string $_old_status, string $_new_status)
    {
        $sql = "UPDATE queue SET status = '{$_new_status}' 
                WHERE chat_id = '{$_chat_id}' and status = '{$_old_status}'
                ORDER BY id DESC LIMIT 1";

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
