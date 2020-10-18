<?php
use PHPUnit\Framework\TestCase;
use App\Controller\Payment;

include __DIR__ . "/../../bootstrap/env.php";

class PaymentTest extends TestCase
{
    public function testredirect()
    {
        $sm = new Payment();
        $result = $sm->make_payment(10000, "IRR", "470004728", "idpay", 50);
        var_dump($sm->get_error());
        var_dump($result);
        $this->assertTrue(is_string($result));
    }
}
