<?php
use PHPUnit\Framework\TestCase;
use App\Controller\User;

include __DIR__ . "/../../bootstrap/env.php";

class UserTest extends TestCase
{
    public function testSetVip()
    {
        $sm = new User();
        $result = $sm->set_vip("4770001");
        var_dump($sm->get_error());
        $this->assertTrue($result);
    }

    public function testIsVip()
    {
        $sm = new User();
        $result = $sm->is_vip("4770001");
        var_dump($sm->get_error());
        $this->assertTrue($result);
    }
}
