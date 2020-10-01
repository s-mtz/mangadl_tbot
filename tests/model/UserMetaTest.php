<?php
use PHPUnit\Framework\TestCase;
use App\Model\UsersMeta;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

class UserMetaTest extends TestCase
{
    public function testAddMeta()
    {
        $sm = new UsersMeta();
        $result = $sm->add_meta("4770007", "vip", "2");
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testGetValue()
    {
        $sm = new UsersMeta();
        $result = $sm->get_value("4770001", "vip");
        var_dump($result);
        var_dump($sm->get_error());

        assertTrue(is_string($result));
    }

    public function testUpdate()
    {
        $sm = new UsersMeta();
        $result = $sm->update_meta("4770001", "vip", 5);
        var_dump($result);
        var_dump($sm->get_error());

        assertTrue($result);
    }
}
