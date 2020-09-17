<?php
use PHPUnit\Framework\TestCase;
use App\Model\User;

use function PHPUnit\Framework\assertTrue;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(["MYSQL_HOST"]);
$dotenv->required(["MYSQL_USER"]);
$dotenv->required(["MYSQL_DATABASE"]);
$dotenv->required(["MYSQL_PASSWORD"]);

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
