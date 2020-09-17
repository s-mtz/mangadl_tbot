<?php
use PHPUnit\Framework\TestCase;
use App\Model\Messages;

use function PHPUnit\Framework\assertTrue;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(["MYSQL_HOST"]);
$dotenv->required(["MYSQL_USER"]);
$dotenv->required(["MYSQL_DATABASE"]);
$dotenv->required(["MYSQL_PASSWORD"]);

class MessagesTest extends TestCase
{
    public function testSetMessages()
    {
        $sm = new Messages();
        $result = $sm->set_messages("4700001", "hello world", "crawler", 2501354);
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testGetLastMessages()
    {
        $sm = new Messages();
        $result = $sm->get_last_messages("4700001", "crawler");
        var_dump($sm->get_error());

        assertTrue(is_array($result));
    }

    public function testFinish()
    {
        $sm = new Messages();
        $result = $sm->finish("4700001");
        var_dump($sm->get_error());

        assertTrue($result);
    }
}
