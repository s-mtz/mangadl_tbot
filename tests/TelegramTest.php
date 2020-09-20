<?php
use PHPUnit\Framework\TestCase;
use Lib\Telegram;

include __DIR__ . "/../../bootstrap/env.php";

class TelegramTest extends TestCase
{
    public function testGetUpdate()
    {
        $tg = new Telegram();
        $request = $tg->proccess_request();
        $this->assertTrue(is_array($request));
    }

    public function testSendMessage()
    {
        $tg = new Telegram();
        $request = $tg->send_message_request(476080724, 'Welcome to mangadl_tbot');
        $this->assertTrue($request);
    }

    public function testUploadFile()
    {
        $tg = new Telegram();
        $request = $tg->send_file_request(
            476080724,
            '/home/smtz/Php_projects/mangadl_tbot/bleach 2.pdf'
        );
        $this->assertTrue($request);
    }
}
