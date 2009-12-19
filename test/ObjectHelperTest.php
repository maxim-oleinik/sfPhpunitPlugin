<?php
/**
 * TestObjectHelper test
 *
 * @author Maxim Oleinik <maxim.oleinik@gmail.com>
 */
require_once(dirname(__FILE__).'/_init.php');


/**
 * Test
 */
class sfPHPUnitPlugin_ObjectHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get unique counter
     */
    public function testGetUniqueCounter()
    {
        $helper = new sfPHPUnitObjectHelper;
        $this->assertGreaterThan($helper->getUniqueCounter(), $c1 = $helper->getUniqueCounter());

        $helper = new sfPHPUnitObjectHelper;
        $this->assertGreaterThan($c1, $c2 = $helper->getUniqueCounter());

        $helper = new sfPHPUnitPlugin_TestHelper;
        $this->assertGreaterThan($c2, $helper->getUniqueCounter());
    }


    /**
     * Make text
     */
    public function testMakeText()
    {
        $helper = new sfPHPUnitObjectHelper;
        $this->assertEquals(
            sprintf('<span %s>text %04d</span>', sfPHPUnitObjectHelper::XSS_TOKEN, $helper->getUniqueCounter()+1),
            $helper->makeText('text')
        );


        $helper = new sfPHPUnitObjectHelper;
        $this->assertEquals(
            sprintf('text %04d', $helper->getUniqueCounter()+1),
            $helper->makeText('text', $xss = false),
            'Expected no XSS mark'
        );
    }

}
