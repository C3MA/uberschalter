<?php 
/**
 * This is the main web application.
 * Use as less JavaScript as possible style \o/
 */

require_once 'smarty/Smarty.class.php';
require_once 'C3MALight.php';

$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__.'/templates/');
$smarty->setCompileDir(__DIR__.'/templates_c/');
$smarty->setConfigDir(__DIR__.'/config/');
$smarty->setCacheDir(__DIR__.'/cache/');

$binary = new C3MALight(LightType::BINARY);

//@FIXME Everthing is broken and makes the C3MA a dark room (if you do not enable the lighttiles
$binary->setBinary(1,(isset($_GET["b1"])) ? Status::ENABLED : Status::DISABLED);
$binary->setBinary(2,(isset($_GET["b2"])) ? Status::ENABLED : Status::DISABLED);
$binary->setBinary(3,(isset($_GET["b3"])) ? Status::ENABLED : Status::DISABLED);
$binary->setBinary(4,(isset($_GET["b4"])) ? Status::ENABLED : Status::DISABLED);
$binary->setBinary(5,(isset($_GET["b5"])) ? Status::ENABLED : Status::DISABLED);
$binary->setBinary(6,(isset($_GET["b6"])) ? Status::ENABLED : Status::DISABLED);

if ($binary->getBinary(1)) $smarty->assign('bin1','checked');
if ($binary->getBinary(2)) $smarty->assign('bin2','checked');
if ($binary->getBinary(3)) $smarty->assign('bin3','checked');
if ($binary->getBinary(4)) $smarty->assign('bin4','checked');
if ($binary->getBinary(5)) $smarty->assign('bin5','checked');
if ($binary->getBinary(6)) $smarty->assign('bin6','checked');
unset($binary);

$smarty->display('index.tpl');

