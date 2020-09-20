<?php
use PHPUnit\Framework\TestCase;
use App\Model\Messages;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

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
        $result = $sm->get_all_messages("476080724");
        var_dump($sm->get_error());
        var_dump($result);
        assertTrue(is_array($result));
    }

    public function testGetAllMessages()
    {
        $sm = new Messages();
        $result = $sm->get_last_messages("4700001");
        var_dump($sm->get_error());
        var_dump($result);
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
