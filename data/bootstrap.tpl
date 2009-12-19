<?php
require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

// Autoload + Init app once
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', $debug = true);
sfContext::createInstance($configuration);

// Clear logs
sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));

// Rebuild DB
$task = new sfDoctrineRebuildDbTask(new sfEventDispatcher, new sfFormatter);
$task->run($args = array(), $options = array(
    '--env=test',
    '--no-confirmation',
));
