<?php

namespace Defr;

use Defr\APRPayment;

/**
 *    APR class for calculating the EU APR
 *    Author: tc
 *    Last Modified: 06/12/2011
 *    Downloads and Documentation can be found at: http://tcsoftware.net/products/apr/php/
 *
 *    Copyright (C) 2011  tc software (http://tcsoftware.net)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class APR
{
    /**
     * These are the default boundaries for the binary search algorithm.
     */
    const APR_ABS_MIN = -0.999999999999;
    const DEFAULT_MIN_APR = self::APR_ABS_MIN;
    const DEFAULT_MAX_APR = PHP_INT_MAX;
    const DEFAULT_PRECISION = 5;
    /**
     * Failsafe, prevents the APR algorithm running away in an infinate loop
     */
    const MAX_ATTEMPTS = 1000;

    /**
     * The balance to finance
     * @var double
     */
    public $Principle = null;
    /**
     * Payments made
     * @var APRPayment[]
     */
    public $PaymentSchedule = [];
    /**
     * Compounding periods per year
     * @var int
     */
    public $CompoundingPeriods = 12;
    /**
     * This determines the accuracy of the overall APR value, unless you need to show more than 2 decimal places
     * it is recommended you leave this at its default, otherwise increase its value
     * @var int
     */
    public $Precision = self::DEFAULT_PRECISION;

    /**
     *
     * @param float $principle
     * @param float $compoundingPeriods
     */
    public function __construct($principle = null, $compoundingPeriods = null)
    {
        if ($principle) {
            $this->Principle = $principle;
        }
        if ($compoundingPeriods) {
            $this->CompoundingPeriods = $compoundingPeriods;
        }
    }

    /**
     * Adds a payment to the Payment Schedule
     *
     * @param float $PaymentNo
     * @param float $Amount
     */
    public function AddPayment($PaymentNo, $Amount)
    {
        array_push($this->PaymentSchedule, new APRPayment($PaymentNo, $Amount));
    }

    /**
     * Resolves the APR value for a loan
     *
     * @param double $principle Balance to finance
     * @param double $repayment Amount repayed per month
     * @param int    $term Number of payments
     * @param double $setupFee Additional fee charged on the first month
     * @param double $backendFee Additional fee charged on the last payment
     * @param int    $compoundingPeriods Number of payments made per year
     *
     * @return double APR Value or FALSE if it could not be determined
     */
    public static function APR_Simple_Annuity(
        $principle,
        $repayment,
        $term,
        $setupFee = 0,
        $backendFee = 0,
        $compoundingPeriods = null
    ) {
        $apr = self::GenerateAnnuity($principle, $repayment, $term, $setupFee, $backendFee, $compoundingPeriods);
        if ($apr) {
            try {
                return self::ResolveAPR($apr);
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Determines the APR for a loan, it will throw an exception if the APR cannot be resolved.
     * On 32 bit systems the maximum APR this algorithm can genertate is 214,000,000,000% the minimum
     * is -100%
     *
     * @param APR $loan
     *
     * @return float
     */
    public static function ResolveAPR(APR $loan)
    {
        // This routine uses a binary search to determine the APR value

        $repaymentTotal = 0;
        $bsMinB = self::DEFAULT_MIN_APR;
        $bsMaxB = self::DEFAULT_MAX_APR;
        $APRRate = 1;
        $attempts = 0;

        while (round($repaymentTotal, $loan->Precision) != $loan->Principle) {
            if ($attempts >= self::MAX_ATTEMPTS || (($bsMaxB == $bsMinB) && ($bsMaxB == $APRRate))) {
                throw new Exception ('APR value could not be resolved', 1);
            }
            $attempts++;

            if ($attempts > 1) {
                if ($repaymentTotal < $loan->Principle) {
                    $bsMaxB = $APRRate;
                } else {
                    $bsMinB = $APRRate;
                }
                $APRRate = $bsMaxB - ($bsMaxB - $bsMinB) / 2;
            }

            $repaymentTotal = 0;
            // Calculate repayments for the schedule using the approximated rate
            foreach ($loan->PaymentSchedule as $payment) {
                $rt = pow((float)1 + $APRRate, (float)$payment->PaymentNumber / $loan->CompoundingPeriods);
                $repaymentTotal += $payment->Amount / $rt;
            }
        }

        return round($APRRate * 100, self::DEFAULT_PRECISION);
    }

    /**
     * Generates a simple Annuity
     *
     * @param double $principle Balance to finance
     * @param double $repayment Amount repayed per month
     * @param int    $term Number of payments
     * @param double $setupFee Additional fee charged on the first month
     * @param double $backendFee Additional fee charged on the last payment
     * @param int    $compoundingPeriods Number of payments made per year
     *
     * @return APR
     */
    public static function GenerateAnnuity(
        $principle,
        $repayment,
        $term,
        $setupFee = 0,
        $backendFee = 0,
        $compoundingPeriods = null
    ) {
        if ($principle > 0 && $repayment > 0 && $term > 0) {
            $loan = new APR($principle, $compoundingPeriods);
            for ($i = 1; $i <= $term; $i++) {
                $amount = $repayment;
                if ($i == 1) {
                    $amount += $setupFee;
                }
                if ($i == $term) {
                    $amount += $backendFee;
                }
                array_push($loan->PaymentSchedule, new APRPayment($i, $amount));
            }

            return $loan;
        }

        return false;
    }

}