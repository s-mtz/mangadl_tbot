<?php
use PHPUnit\Framework\TestCase;
use App\Model\User;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

class UserTest extends TestCase
{
    public function testNewUser()
    {
        $sm = new User();
        $result = $sm->new_user("470001", "permuim", 2300025);
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testGetUser()
    {
        $sm = new User();
        $result = $sm->get_user("470001");
        var_dump($sm->get_error());

        assertTrue(is_array($result));
    }

    public function testUpdateType()
    {
        $sm = new User();
        $result = $sm->update_type("470001", "permuim", "super_perium");
        var_dump($sm->get_error());

        assertTrue($result);
    }
}
