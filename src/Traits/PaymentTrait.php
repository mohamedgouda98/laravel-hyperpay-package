<?php

namespace Gouda\LaravelHyperpay\Traits;

trait PaymentTrait{

    public function refactorCardNumber($cardNumber)
    {
        return str_replace(' ', '', $cardNumber);
    }


    public function explodExpirationdate($expirationDate)
    {
        $expirationDate = explode('/',$expirationDate);

        if(count($expirationDate) == 2)
        {
            return [
                'month' => $expirationDate[0],
                'year' => $expirationDate[1]
            ];
        }
       return 'invalid value';
    }


}