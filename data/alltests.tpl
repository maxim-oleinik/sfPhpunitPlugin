<?php
require_once __DIR__.'/bootstrap/all.php';


/**
 * AllTests
 */
class AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * TestSuite
     */
    public static function suite()
    {
        $suite = new AllTests('PHPUnit');

        $files = sfFinder::type('file')->name('*Test.php')->in(array(
            __DIR__.'/unit',
            __DIR__.'/functional',
        ));

        foreach ($files as $file) {
            $suite->addTestFile($file);
        }

        return $suite;
    }

}
