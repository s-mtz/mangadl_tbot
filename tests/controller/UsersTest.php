<?php
use PHPUnit\Framework\TestCase;
use App\Controller\Users;

include __DIR__ . "/../../bootstrap/env.php";

class UsersTest extends TestCase
{
    public function testCanFillMetas()
    {
        $user = new Users();
        $user->set_meta($_ENV["ADMIN_ID"], "vip", "2");
        $user->set_meta($_ENV["ADMIN_ID"], "limit", "50");
        $meta = $user->get_meta($_ENV["ADMIN_ID"], "vip");
        $this->assertEquals($meta, "2");
    }
}
