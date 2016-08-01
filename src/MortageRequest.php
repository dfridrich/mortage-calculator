<?php

namespace Defr;

/**
 * Class MortageRequest.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class MortageRequest
{
    /**
     * @var float
     */
    private $salePrice;

    /**
     * @var float
     */
    private $mortgageInterestPercent;

    /**
     * @var int
     */
    private $yearTerm;

    /**
     * @var int
     */
    private $monthTerm;

    /**
     * Mortage constructor.
     *
     * @param int $salePrice
     * @param int $mortgageInterestPercent
     * @param int $yearTerm
     */
    public function __construct(
        $salePrice = 0,
        $mortgageInterestPercent = 0,
        $yearTerm = 0
    ) {
        $this->salePrice = (float) $salePrice;
        $this->mortgageInterestPercent = (float) $mortgageInterestPercent;
        $this->yearTerm = (int) $yearTerm;
        $this->monthTerm = (int) $this->yearTerm * 12;
    }

    /**
     * @return MortageResult
     */
    public function calculate()
    {
        if ($this->salePrice <= 0) {
            throw new \InvalidArgumentException('Sale price must not be lower than 0.');
        }
        if ($this->mortgageInterestPercent <= 0) {
            throw new \InvalidArgumentException('Mortage interest rate must not be lower than 0.');
        }
        if ($this->yearTerm <= 0) {
            throw new \InvalidArgumentException('Year term must not be lower than 0.');
        }

        $annualInterestRate = $this->mortgageInterestPercent / 100;
        $monthlyInterestRate = $annualInterestRate / 12;

        $monthlyPayment = $this->salePrice / $this->getInterestFactor($this->yearTerm, $monthlyInterestRate);

        $result = new MortageResult(
            $this,
            $annualInterestRate,
            $monthlyInterestRate,
            $monthlyPayment
        );

        return $result;
    }

    /**
     * @param float $salePrice
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = (float) $salePrice;
    }

    /**
     * @param float $mortgageInterestPercent
     */
    public function setMortgageInterestPercent($mortgageInterestPercent)
    {
        $this->mortgageInterestPercent = (float) $mortgageInterestPercent;
    }

    /**
     * @param int $yearTerm
     */
    public function setYearTerm($yearTerm)
    {
        $this->yearTerm = (int) $yearTerm;
    }

    /**
     * @param bool $showProgress
     */
    public function setShowProgress($showProgress)
    {
        $this->showProgress = (bool) $showProgress;
    }

    /**
     * @param $year_term
     * @param $monthly_interest_rate
     *
     * @return float|int
     */
    private function getInterestFactor($year_term, $monthly_interest_rate)
    {
        $factor = 0;
        $baseRate = 1 + $monthly_interest_rate;
        $denominator = $baseRate;
        for ($i = 0; $i < ($year_term * 12); ++$i) {
            $factor += (1 / $denominator);
            $denominator *= $baseRate;
        }

        return $factor;
    }

    /**
     * @return int
     */
    public function getMonthTerm()
    {
        return $this->monthTerm;
    }

    /**
     * @return float
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @return float
     */
    public function getMortgageInterestPercent()
    {
        return $this->mortgageInterestPercent;
    }

    /**
     * @return int
     */
    public function getYearTerm()
    {
        return $this->yearTerm;
    }
}
