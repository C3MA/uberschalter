<?php 
/**
 * This is the main web application.
 * Use as less JavaScript as possible style \o/
 */

require_once 'smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__.'/templates/');
$smarty->setCompileDir(__DIR__.'/templates_c/');
$smarty->setConfigDir(__DIR__.'/config/');
$smarty->setCacheDir(__DIR__.'/cache/');

