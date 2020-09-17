<?php
use PHPUnit\Framework\TestCase;
use App\Model\UserMeta;

use function PHPUnit\Framework\assertTrue;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(["MYSQL_HOST"]);
$dotenv->required(["MYSQL_USER"]);
$dotenv->required(["MYSQL_DATABASE"]);
$dotenv->required(["MYSQL_PASSWORD"]);

class UserMetaTest extends TestCase
{
    public function testAddMeta()
    {
        $sm = new UserMeta();
        $result = $sm->add_meta("470001", "hello", "2");
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testGetValue()
    {
        $sm = new UserMeta();
        $result = $sm->get_value("470001", "hello");
        var_dump($sm->get_error());

        assertTrue(is_string($result));
    }
}
