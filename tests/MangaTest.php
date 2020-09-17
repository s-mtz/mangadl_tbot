<?php
use PHPUnit\Framework\TestCase;
use App\Model\Manga;

use function PHPUnit\Framework\assertTrue;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(["MYSQL_HOST"]);
$dotenv->required(["MYSQL_USER"]);
$dotenv->required(["MYSQL_DATABASE"]);
$dotenv->required(["MYSQL_PASSWORD"]);

class MangaTest extends TestCase
{
    public function testSetManga()
    {
        $sm = new Manga();
        $result = $sm->set_manga("here", "mangapanda", "bleach", 6, 252033);
        var_dump($sm->get_error());
        assertTrue($result);
    }

    public function testGetManga()
    {
        $sm = new Manga();
        $result = $sm->get_manga("mangapanda", "bleach", 6);
        var_dump($sm->get_error());
        assertTrue(is_array($result));
    }
}
