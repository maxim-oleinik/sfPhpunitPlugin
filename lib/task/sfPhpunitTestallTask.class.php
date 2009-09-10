<?php
/**
 * Task for running all PHPUnit tests
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Pablo Godel <pgodel@gmail.com>
 */
class sfPhpunitTestallTask extends sfBaseTask
{
    /**
     * @see sfTask
     */
    protected function configure()
    {
        $this->namespace = 'phpunit';
        $this->name = 'testall';
        $this->briefDescription = 'Runs PHPUnit AllTests';

        $this->addOptions(array(
            new sfCommandOption('kind',    'k', sfCommandOption::PARAMETER_OPTIONAL, 'The folder to test', ''),
            new sfCommandOption('name',    'a', sfCommandOption::PARAMETER_REQUIRED, 'The test file name', 'AllPhpunitTests'),
            new sfCommandOption('isolate', 'i',  sfCommandOption::PARAMETER_OPTIONAL, 'Run tests in separate (parallel) processes', 'off'),
        ));

        $this->detailedDescription = <<<EOF
The [phpunit:testall] task Runs PHPUnit AllTests
EOF;
    }


    /**
     * @see sfTask
     */
    protected function execute($arguments = array(), $options = array())
    {
        $isolate = ('on' == $options['isolate']) ? '--process-isolation' : '';
        $file    = $options['kind'] . $options['name'] . '.php';

        chdir('test/phpunit');
        passthru("phpunit {$isolate} {$file}");
    }

}