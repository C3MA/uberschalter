<?php 
require_once 'control.php';
 
$types = array( "bin" , "rgb" );

if (isset ($_REQUEST["type"]) && isset($_REQUEST["id"]) 
	&& is_int($_REQUEST["id"]) && in_array($_REQUEST["type"], $types) )
{
	$type = $_REQUEST["type"];
	$id = $_REQUEST["id"];
}

if (! isset($type) && ! isset($id))
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