<?php

namespace Defr;

/**
 * Class MortageResult.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class MortageResult
{
    /**
     * @var MortageRequest
     */
    private $mortageRequest;

    /**
     * @var float
     */
    private $annualInterestRate;

    /**
     * @var float
     */
    private $monthlyInterestRate;

    /**
     * @var float
     */
    private $monthlyPayment;

    /**
     * @var float
     */
    private $totalPayed;

    /**
     * @var float
     */
    private $apr;

    /**
     * MortageResult constructor.
     *
     * @param MortageRequest $mortageRequest
     * @param float          $annualInterestRate
     * @param float          $monthlyInterestRate
     * @param float          $monthlyPayment
     */
    public function __construct(
        MortageRequest $mortageRequest,
        $annualInterestRate,
        $monthlyInterestRate,
        $monthlyPayment
    ) {
        $this->mortageRequest = $mortageRequest;
        $this->annualInterestRate = (float)$annualInterestRate;
        $this->monthlyInterestRate = (float)$monthlyInterestRate;
        $this->monthlyPayment = (float)$monthlyPayment;
        $this->totalPayed = (float)$monthlyPayment * $mortageRequest->getMonthTerm();
        $this->apr = APR::APR_Simple_Annuity(
            $mortageRequest->getSalePrice(),
            $this->monthlyPayment,
            $mortageRequest->getMonthTerm()
        );
    }

    /**
     * @return MortageRequest
     */
    public function getMortageRequest()
    {
        return $this->mortageRequest;
    }

    /**
     * @return float
     */
    public function getAnnualInterestRate()
    {
        return $this->annualInterestRate;
    }

    /**
     * @return float
     */
    public function getMonthlyInterestRate()
    {
        return $this->monthlyInterestRate;
    }

    /**
     * @return float
     */
    public function getMonthlyPayment()
    {
        return $this->monthlyPayment;
    }

    /**
     * @return float
     */
    public function getTotalPayed()
    {
        return $this->totalPayed;
    }

    /**
     * @return float
     */
    public function getApr()
    {
        return $this->apr;
    }

}
