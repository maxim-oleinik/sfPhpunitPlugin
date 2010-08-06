<?php
/**
 * Extended tester for request
 *
 * @author Max <maxim.olenik@gmail.com>
 */

class sfPHPUnitFunctionalTesterRequest extends sfTesterRequest
{
    /**
     * Check: module and action
     *
     * @param string $module
     * @param string $module
     */
    public function checkModuleAction($module, $action)
    {
        $this->isParameter('module', $module);
        $this->isParameter('action', $action);

        return $this->getObjectToReturn();
    }

}
