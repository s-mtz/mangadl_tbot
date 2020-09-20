<?php
use PHPUnit\Framework\TestCase;
use App\Controller\Message;

include __DIR__ . "/../../bootstrap/env.php";

class MessageTest extends TestCase
{
    private $bot = '{"update_id":575525054,"message":{"message_id":147,"from":{"id":476080724,"is_bot":false,"first_name":"MorTezA","username":"lord_MATATA","language_code":"en"},"chat":{"id":476080724,"first_name":"MorTezA","username":"lord_MATATA","type":"private"},"date":1600437048,"text":"6"}}';

    public function testSetManga()
    {
        $sm = new Message();
        $this->bot = json_decode($this->bot, true);
        $result = $sm->listen($this->bot['message']);
        var_dump($sm->get_error());
        $this->assertTrue($result);
    }
}
