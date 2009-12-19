<?php
/**
 * Base TestCase test
 *
 * @author  Maxim Oleinik <maxim.oleinik@gmail.com>
 */
require_once(dirname(__FILE__).'/_init.php');


/**
 * Test
 */
class sfPHPUnitPlugin_UnitTestCaseTest extends sfPHPUnitTestCase
{
    protected function _reset()
    {
    }

    /**
     * Make test helper
     */
    protected function makeHelper()
    {
        return new sfPHPUnitPlugin_TestHelper;
    }


    /**
     * Init helper
     */
    public function testInitHelper()
    {
        $this->assertEquals($this->makeHelper(), $this->helper);
    }

}
