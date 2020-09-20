<?php
use PHPUnit\Framework\TestCase;
use App\Model\UserMeta;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

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
