<?php
use PHPUnit\Framework\TestCase;
use App\Model\Queues;

use function PHPUnit\Framework\assertTrue;

include __DIR__ . "/../../bootstrap/env.php";

class QueuesTest extends TestCase
{
    public function testSetQueue()
    {
        $sm = new Queues();
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
        $sm = new Queues();
        $result = $sm->get_queue();
        var_dump($result);
        var_dump($sm->get_error());

        assertTrue(is_array($result));
    }

    public function testUpdateQueue()
    {
        $sm = new Queues();
        $result = $sm->update_queue(1, "4700001", "not done yet", "complited");
        var_dump($sm->get_error());

        assertTrue($result);
    }

    public function testCanGetProcessing()
    {
        $sm = new Queues();
        $result = $sm->get_processing_count();
        var_dump($sm->get_error());
        var_dump($result);

        $this->assertIsNumeric($result);
    }
}
