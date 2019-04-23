<?php
/**
 * Created by PhpStorm.
 * User: JLEON
 * Date: 21/12/2018
 * Time: 3:59 PM
 */

namespace App\Service\CreditCard;


class CreditCalculations
{
    /**
     * @param array $payments
     * @param int $amount
     * @return int
     */
    public function calculateActualCreditCardConsumeDebt(int $amount, array $payments = []): int
    {
        return $amount - array_sum($payments);
    }

    public function calculateNextInterestAmount($actualDebt, $interest)
    {
        return ( $actualDebt * $interest ) / 100;
    }

    public function calculateNextCapitalAmount($actualDebt, $pendingDues)
    {
        return $actualDebt / $pendingDues;
    }

    public function calculateNextPaymentAmount($nextCapitalAmount, $nextInterestAmount)
    {
        return $nextCapitalAmount + $nextInterestAmount;
    }
    
    public function calculateNumberOfPendingDues(int $totalDues, int $payedDues = 0): int
    {
        return $totalDues - $payedDues;
    }

    public function calculateActualDueToPay(int $totalDues, int $pendingDues): int
    {
        return $totalDues - $pendingDues + 1;
    }

    /**
     * Return the list of payment that have to pay
     * @param int $actualDebt
     * @param int $interest
     * @param int $pendingDues
     * @param int $actualDueNumber
     * @return array
     */
    public function calculatePendingPayments(
        int $actualDebt,
        int $interest,
        int $pendingDues,
        int $actualDueNumber = 1
    ): array
    {
        $capitalMonthlyAmount = $actualDebt / $pendingDues;

        $duesToPay = [];
        for ( $i = $actualDueNumber; $i <= $pendingDues;  $i++ ){
            $interestToPay = ( ( $actualDebt * $interest ) / 100 );
            $duesToPay[ $i ] = array(
                'capital_amount' => $capitalMonthlyAmount,
                'interest' => $interestToPay,
                'total_to_pay' =>  $capitalMonthlyAmount + $interestToPay
            );
            $actualDebt -= $capitalMonthlyAmount;
        }

        return $duesToPay;
    }

    public function sumArrayValues(array $values = [])
    {
        return array_sum( $values );
    }

    /**
     * @param $paymentMonth
     * @return string
     */
    public function calculateNextPaymentDate($paymentMonth = null)
    {
        $actualMonth = $paymentMonth ?? date('m-Y');

        return date('j') < 15 ? $actualMonth : date("m-Y",strtotime($actualMonth."+ 1 Month"));
    }
}

