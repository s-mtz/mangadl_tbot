<?php

namespace app\Model;

use Lib\Core\Model as ModelAbstract;

class Mangas extends ModelAbstract
{
    private $error = [];

    /**
     * [set_manga description]
     *
     * @return  [type]  [return description]
     */
    public function set_manga(
        string $_dir,
        string $_crawler,
        string $_manga,
        int $_chapter,
        int $_time
    ) {
        $sql = "INSERT INTO manga (dir, crawler, manga, chapter, time) 
                VALUES ('{$_dir}', '{$_crawler}', '{$_manga}', $_chapter, $_time)";

        if ($this->conn->query($sql) === false) {
            $this->error["message"] = "couldnt send set_manga query to database";
            return false;
        }
        return true;
    }

    /**
     * [get_manga description]
     *
     * @param   string  $_manga    [$_manga description]
     * @param   int     $_chapter  [$_chapter description]
     * @param   string  $_crawler  [$_crawler description]
     *
     * @return  [type]             [return description]
     */
    public function get_manga(string $_manga, int $_chapter, string $_crawler = '')
    {
        $where = "";
        $where .= empty($_crawler) ? "" : "crawler = '{$_crawler}'  and";

        $sql = "SELECT * FROM manga 
                WHERE {$where} manga = '{$_manga}' and chapter = $_chapter";

        $query = $this->conn->query($sql);

        if ($query->num_rows > 0) {
            $data = $query->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the manga in database";
                return false;
            }
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
            return null;
        }
        return $this->error;
    }
}
