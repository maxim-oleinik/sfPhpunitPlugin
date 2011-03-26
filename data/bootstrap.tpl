<?php
require_once(__DIR__.'/../../config/ProjectConfiguration.class.php');

// Autoload + Init app once
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', $debug = true);
sfContext::createInstance($configuration);
