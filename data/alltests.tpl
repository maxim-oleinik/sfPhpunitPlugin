<?php
require_once(dirname(__FILE__).'/bootstrap/all.php');


class AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * TestSuite
     */
    public static function suite()
    {
        $suite = new AllTests('PHPUnit');

        $base  = dirname(__FILE__);
        $files = sfFinder::type('file')->name('*Test.php')->in(array(
            $base.'/plugins/sfPhpunitPlugin/test',
            $base.'/unit',
            $base.'/functional',
        ));

        foreach ($files as $file) {
            $suite->addTestFile($file);
        }

        return $suite;
    }

}
