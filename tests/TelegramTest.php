<?php
use PHPUnit\Framework\TestCase;
use Lib\Telegram;

include __DIR__ . "/../bootstrap/env.php";

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
            $_ENV["ADMIN_ID"],
            ABSPATH . 'upload/mangapanda/bleach/5/1.jpg',
            'Fuck'
        );
        var_dump($request);
        $this->assertTrue(is_array($request));
    }

    public function testUploadFileId()
    {
        $tg = new Telegram();
        $request = $tg->send_file_id_request_pdf(
            $_ENV["ADMIN_ID"],
            "BQACAgQAAxkDAAIgQ197QQN4zuVKMnQ26kBVYym6nWcxAAJQBgACaufgU1DpWo25JZo-GwQ",
            "fuckit"
        );
        var_dump($request);
        $this->assertTrue($request);
    }
}
