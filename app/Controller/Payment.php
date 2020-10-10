<?php

namespace App\Controller;

use App\Model\Payments;

class Payment
{
    /**
     *
     * @var array
     */
    private $error = [];
    private $pay;

    public function __construct()
    {
        $this->pay = new Payments();
    }

    public function payment_make()
    {
        echo "payment_make";
    }

    public function payment_validation()
    {
        echo "payment_validation";
    }

    public function payment_get()
    {
        echo "peyment_get";
    }

    public function payment()
    {
        echo "<h1>here must the session and redirect shit stuff right ?</h1>";
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
