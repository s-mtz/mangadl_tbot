<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class Queues extends ModelAbstract
{
    private $error = [];

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
    public function get_queue($_status)
    {
        $sql = "SELECT * FROM queue WHERE status = '{$_status}'
                ORDER BY type DESC LIMIT 1";

        if (!($query = $this->conn->query($sql))) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }

        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
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
    public function update_queue(int $_id, string $_new_status)
    {
        $sql = "UPDATE queue SET status = '{$_new_status}' 
                WHERE  id = $_id";

        if ($this->conn->query($sql) === true) {
            return true;
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    public function get_processing_count()
    {
        $sql = "SELECT COUNT(*) FROM `queue` WHERE `status`='processing'";
        if (!($query = $this->conn->query($sql))) {
            $this->error["message"] = "couldnt connect to database";
            return false;
        }

        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the queue in database";
                return false;
            }
            return $data[0]["COUNT(*)"];
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    /**
     *
     * @param string $_chat_id
     * @return boolean
     */
    public function cancel(string $_chat_id)
    {
        $sql = "UPDATE queue SET `status`='canceled' 
        WHERE  `chat_id`={$_chat_id} AND `status`='pending'";

        if ($this->conn->query($sql) === true) {
            return true;
        }
        $this->error["message"] = $this->conn->error;
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
