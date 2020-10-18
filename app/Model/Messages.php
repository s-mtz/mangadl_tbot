<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class Messages extends ModelAbstract
{
    private $error = [];

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
        $_content = filter_var($_content, FILTER_SANITIZE_STRING);

        $sql = "INSERT INTO messages (chat_id, content, `type`, `time`) 
                VALUES ('{$_chat_id}', '{$_content}', '{$_type}', $_time)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send set_messages query to database";
            return false;
        }
        return true;
    }

    /**
     * [get_last_messages description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     * @param   string  $_type     [$_type description]
     *
     * @return  [type]             [return description]
     */
    public function get_last_messages(string $_chat_id, string $_type = null)
    {
        if ($_type === null) {
            $sql = "SELECT * FROM messages 
                    WHERE chat_id = '{$_chat_id}' 
                    ORDER BY id DESC LIMIT 1";
        } else {
            $sql = "SELECT * FROM messages 
                    WHERE chat_id = '{$_chat_id}' and type = '{$_type}' 
                    ORDER BY id DESC LIMIT 1";
        }
        $query = $this->conn->query($sql);
        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the request in database";
                return false;
            }
            return $data[0];
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    /**
     * [get_all_messages description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     *
     * @return  array   $data      [0] -> crawler, [1] -> manga, [2] -> chapter_start, [3] -> chapter_finish ]
     */
    public function get_all_messages(string $_chat_id)
    {
        $sql = "SELECT content FROM messages 
                WHERE chat_id='{$_chat_id}'";
        $query = $this->conn->query($sql);
        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the request in database";
                return false;
            }
            return $data;
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    /**
     * [finish description]
     *
     * @param   string  $_chat_id  [$_chat_id description]
     *
     * @return  [type]             [return description]
     */
    public function finish(string $_chat_id)
    {
        $sql = "DELETE FROM messages WHERE chat_id = '{$_chat_id}'";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send get_last_messages query to database";
            return false;
        }
        return true;
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
