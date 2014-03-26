<?php 
require_once 'control.php';
 
$type = $_REQUEST["type"];
$id = $_REQUEST["id"];

if (isset($type) && isset($id))
{
	print "You MUST set 'type' and id'";
	return;
}

print "Type: " . $type . " id: " . $id . "\n";

$c3ma = new C3MALight(LightType::RGB);
var_dump($c3ma->getRGB(1));

for ($i = 0; $i < 6; $i++)
{
	$c3ma->setRGB($i, 0, 0, 255);
}

var_dump($c3ma->getRGB(2));