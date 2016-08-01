<?php

use Defr\MortageRequest;

/**
 * Class MortageTest
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class MortageTest extends PHPUnit_Framework_TestCase
{
    public function testMortage()
    {
        $mortage = new MortageRequest(1000000, 1.89, 20);
        $result = $mortage->calculate();
        $this->assertEquals(5007, ceil($result->getMonthlyPayment()));
        $this->assertEquals(1201657, ceil($result->getTotalPayed()));
        $this->assertEquals(1.9, round($result->getApr(), 1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongInputs() {
        $mortage = new MortageRequest(0, 0, 0);
        $mortage->calculate();
    }
}