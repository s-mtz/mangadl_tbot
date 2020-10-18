<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class Payments extends ModelAbstract
{
    private $error = [];

    /**
     * [make_payment description]
     *
     * @return  [type]  [return description]
     */
    public function set_payment(
        string $_chat_id,
        int $_limit,
        int $_price,
        string $_currency,
        string $_type,
        string $_status,
        int $_time,
        string $_token
    ) {
        $sql = "INSERT INTO payment (chat_id, `limit`, price, currency, `type`, `status`, `time`, token) 
        VALUES ('{$_chat_id}', $_limit, $_price, '{$_currency}', '{$_type}', '{$_status}', $_time, '{$_token}')";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send set_queue query to database";
            return false;
        }
        return true;
    }

    public function get_payment(string $_chat_id)
    {
        $sql = "SELECT * FROM payment 
                WHERE chat_id = '{$_chat_id}'
                ORDER BY id DESC LIMIT 1";

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

    public function update_payment(int $_chat_id, string $_field, string $_new_field)
    {
        $sql = "UPDATE payment SET '{$_field}' = '{$_new_field}' 
                WHERE  chat_id = $_chat_id
                ORDER BY id DESC LIMIT 1";

        if ($this->conn->query($sql) === true) {
            return true;
        }
        $this->error["message"] = "there is nothing in database";
        return false;
    }

    public function last_id()
    {
        $sql = "SELECT * FROM payment 
                ORDER BY id DESC LIMIT 1";

        $query = $this->conn->query($sql);
        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the request in database";
                return false;
            }
            return $data[0]["id"];
        }
        return 0;
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
