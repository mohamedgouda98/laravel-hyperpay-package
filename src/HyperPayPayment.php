<?php


namespace Gouda\LaravelHyperpay;



use Gouda\LaravelHyperpay\Models\Transaction;
use Gouda\LaravelHyperpay\Traits\PaymentTrait;

class HyperPayPayment
{
    use PaymentTrait;

    public $hyperpay_token;
    public $hyperpay_entity_id;
    public $hyperpay_url;

    public function __construct()
    {
        $this->hyperpay_token = config('hyperPay.HyperPayToken');
        $this->hyperpay_entity_id = config('hyperPay.HyperPayEntityID');
        $this->hyperpay_url = config('hyperPay.HyperPayURL');
    }

    public function payWithOutUser($card_name,$amount,$cardNumber,$expirationdate,$securitycode)
    {
        $cardNumber = $this->refactorCardNumber($cardNumber);

        $data = "entityId=" . $this->hyperpay_entity_id .
            "&amount=$amount" .
            "&currency=SAR" .
            "&paymentBrand=VISA" .
            "&paymentType=DB" .
            "&card.number=$cardNumber" .
            "&card.holder=$card_name" .
            "&card.expiryMonth=$expirationdate[0]" .
            "&card.expiryYear=20$expirationdate[1]" .
            "&card.cvv=$securitycode".
            "&shopperResultUrl=https://wordpresshyperpay.docs.oppwa.com/tutorials/server-to-server";;

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->hyperpay_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization:Bearer ' . $this->hyperpay_token));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseData = curl_exec($ch);
                if (curl_errno($ch)) {
                    return curl_error($ch);
                }
                curl_close($ch);
                return $this->saveTransaction(json_decode($responseData), $amount);
            }catch (\Exception $e)
            {
                return $e->getMessage();
            }

    }


    public function saveTransaction($transaction, $amount)
    {
        if(in_array($transaction->result->code, ["000.200.000", "000.200.200", "200.200.000", "200.200.200"]) )
        {
            $cardName = $transaction->card->holder;
            $status = $transaction->result->description;
            $paymentId = $transaction->id;
            Transaction::create([
                'card_name' => $cardName,
                'amount' => $amount,
                'status' => $status,
                'payment_id' => $paymentId
            ]);
            return $paymentId;
        }
        return false;
    }


    public function PaymentStatus()
    {
        // todo
    }


    public function updatePayment()
    {
        // todo
    }


    public function deletePayment()
    {
        // todo
    }

    public function getPayments()
    {
        // todo
    }



}