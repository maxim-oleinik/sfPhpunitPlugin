<?php
require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

// Autoload + Init app once
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', $debug = true);
sfContext::createInstance($configuration);
