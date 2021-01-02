<?php
use PHPUnit\Framework\TestCase;
use App\Model\Manga;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

class MysqlTest extends TestCase
{
    public function testSetManga()
    {
        // $conn = new \mysqli("127.0.0.1", 'geeksesi', "javadkhof", "mangadl", 3306);
        var_dump($_ENV['MYSQL_HOST'] . " - ".
        $_ENV['MYSQL_USER'] . " - ".
        $_ENV['MYSQL_PASSWORD'] . " - ".
        $_ENV['MYSQL_DATABASE'] . " - ".
        $_ENV['MYSQL_PORT']);
        $conn = new \mysqli(
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD'],
            $_ENV['MYSQL_DATABASE'],
            $_ENV['MYSQL_PORT']
        );
        var_dump("FUCK");
        if ($conn->connect_error) {
            var_dump("HOOOO");
        }
        var_dump("BOOOO");
        return true;
    }

}
