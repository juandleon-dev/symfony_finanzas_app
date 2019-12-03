<?php

namespace App\Factory\Payments;

use App\Entity\CreditCard\CreditCardConsume;
use App\Entity\CreditCard\CreditCardPayment;
use App\Service\CreditCard\CardConsumeManager;

class CreditCardPaymentFactory implements PaymentInterface
{

    public static function create(
        CreditCardConsume $cardConsume,
        float $amount,
        float $capitalAmount,
        float $realCapitalAmount,
        float $interestAmount,
        float $monthPayed,
        bool $legalDue = true
    ): CreditCardPayment
    {
        $payment = new CreditCardPayment();
        $payment->setCreditConsume($cardConsume);
        $payment->setAmount($amount);
        $payment->setCapitalAmount($capitalAmount);
        $payment->setRealCapitalAmount($realCapitalAmount);
        $payment->setInterestAmount($interestAmount);
        $payment->setMonthPayed($monthPayed);
        $payment->setLegalDue($legalDue);

        return $payment;
    }

    public function createPayment()
    {
        // TODO: Implement createPayment() method.
    }
}