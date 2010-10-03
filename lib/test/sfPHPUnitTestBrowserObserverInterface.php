<?php

/**
 * sfPHPUnitTestBrowser observer interface
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
interface sfPHPUnitTestBrowserObserverInterface
{
    public function notify($method, sfBrowser $browser);
}
