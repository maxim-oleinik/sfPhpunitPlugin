<?php

/**
 * Base test class for all functional tests
 */
abstract class {baseTestClassName} extends sfPHPUnitFunctionalTestCase
{
    /**
     * Inject your own functional testers
     *
     * @see sfTestFunctionalBase::setTesters()
     *
     * @return array
     */
    protected function getFunctionalTesters()
    {
        return array(
            'request'  => 'sfPHPUnitFunctionalTesterRequest',
            'response' => 'sfPHPUnitFunctionalTesterResponse',
            'user'     => 'sfTesterUser',
            'model'    => 'sfTesterDoctrine',
            'mail'     => 'sfPHPUnitFunctionalTesterMail',
        );
    }

}
