<?php

use Defr\MortageRequest;

/**
 * Class FileAsResponseTest
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
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongInputs() {
        $mortage = new MortageRequest(0, 0, 0);
        $mortage->calculate();
    }
}