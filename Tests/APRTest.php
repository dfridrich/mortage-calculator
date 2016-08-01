<?php

use Defr\APR;

/**
 * Class APRTest
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class APRTest extends PHPUnit_Framework_TestCase
{
    public function testAPR()
    {
        $apr = APR::APR_Simple_Annuity(1000, 100, 12);
        $this->assertEquals(41.3, round($apr, 1));
    }
}