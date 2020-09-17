<?php

namespace app\Model;

use mysqli;

class Manga
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
     * @param   string  $_crawler  [$_crawler description]
     * @param   string  $_manga    [$_manga description]
     * @param   int     $_chapter  [$_chapter description]
     *
     * @return  [type]             [return description]
     */
    public function get_manga(string $_crawler, string $_manga, int $_chapter)
    {
        $sql = "SELECT crawler, manga, chapter FROM manga 
                WHERE crawler = '{$_crawler}' and manga = '{$_manga}' and chapter = $_chapter";

        if ($this->conn->query($sql)->num_rows > 0) {
            $data = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            if (empty($data)) {
                $this->error["message"] = "couldnt find the manga in database";
                return false;
            }
            return $data;
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
