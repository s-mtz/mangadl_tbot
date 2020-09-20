<?php
use PHPUnit\Framework\TestCase;
use App\Controller\Queue;

include __DIR__ . "/../../bootstrap/env.php";

class QueueTest extends TestCase
{
    public function testSetManga()
    {
        $sm = new Queue();
        $result = $sm->get_mesage_threrade($_ENV["ADMIN_ID"]);
        var_dump($sm->get_error());
        $this->assertTrue($result);
    }

    public function testRunQueue()
    {
        $sm = new Queue();
        $result = $sm->run_queue();
        var_dump($sm->get_error());
        $this->assertTrue($result);
    }
}
