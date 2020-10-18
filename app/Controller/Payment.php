<?php

namespace App\Controller;

use GuzzleHttp\Client;
use App\Model\Payments;

class Payment
{
    /**
     *
     * @var array
     */
    private $error = [];
    private $payment;

    public function __construct()
    {
        $this->payment = new Payments();
    }

    public function make_payment(
        int $_price,
        string $_currency,
        string $_chat_id,
        string $_type,
        int $_limit
    ) {
        $params = [
            "order_id" => $this->payment->last_id() + 1,
            "amount" => $_price,
            "name" => $_chat_id,
            "desc" => "خرید $_limit عدد چپتر برای ربات @mangadl_tbot به ازای $_price $_currency",
            "callback" => "manga.test/payment",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
            'X-SANDBOX: 1',
        ]);

        $result = curl_exec($ch);
        if (!$result) {
            $this->error["message"] = "couldnt send the post to idpay";
            return false;
        }
        curl_close($ch);

        $result = json_decode($result, true);

        if ($result["id"]) {
            $this->payment->set_payment(
                $_chat_id,
                $_limit,
                $_price,
                $_currency,
                $_type,
                "pending",
                time(),
                $result["id"]
            );
            return $result["link"];
        }
        $this->error["message"] = $this->payment->get_error();
        return false;
    }

    /**
     * [get_error description]
     *
     * @return  [type]  [return description]
     */
    public function get_error()
    {
        if (empty($this->error)) {
            return false;
        }
        return $this->error;
    }
}
