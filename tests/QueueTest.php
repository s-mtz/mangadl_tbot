<?php
use PHPUnit\Framework\TestCase;
use App\Model\Queue;

use function PHPUnit\Framework\assertTrue;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(["MYSQL_HOST"]);
$dotenv->required(["MYSQL_USER"]);
$dotenv->required(["MYSQL_DATABASE"]);
$dotenv->required(["MYSQL_PASSWORD"]);

class QueueTest extends TestCase
{
    public function testSetQueue()
    {
        $sm = new Queue();
        $result = $sm->set_queue(
            "4700001",
            "mangapanda",
            "bleach",
            2501354,
            1,
            230007,
            "not done yet"
        );
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testGetQueue()
    {
        $sm = new Queue();
        $result = $sm->get_queue();
        var_dump($sm->get_error());

        assertTrue(is_array($result));
    }

    public function testUpdateQueue()
    {
        $sm = new Queue();
        $result = $sm->update_queue("4700001", "not done yet", "complited");
        var_dump($sm->get_error());

        assertTrue($result);
    }
}
