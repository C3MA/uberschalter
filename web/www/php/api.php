<?php 
require_once 'C3MALight.php';
require_once 'C3MAWrapper.php';
 
$types = array( "bin" , "rgb" );

if (isset ($_REQUEST["type"]) && isset($_REQUEST["id"]) 
	&& is_numeric($_REQUEST["id"]) && in_array($_REQUEST["type"], $types) )

{
	$type = $_REQUEST["type"];
	$id = $_REQUEST["id"];
	
	try{
		switch($type){
			case "bin":
				$b = new C3MAWrapperBinary();
				if(isset ($_REQUEST["v"]) ){
					$b->set($id,$_REQUEST["v"]);
				}
				print $b->get($id);
				break;
			case "rgb":
				
				break;
			default:
				new Exception("Bad request",400);
		}
	}catch(Exception $e){
		print json_encode(array( "error" => $e->getCode(), "message" => $e->getMessage()));
	}
	
}else{
	print json_encode(array( "error" => "400", "message" => "type and id not specified"));	
}

// print "Type: " . $type . " id: " . $id . "\n";

// $c3ma = new C3MALight(LightType::RGB);
// var_dump($c3ma->getRGB(1));

// for ($i = 0; $i < 6; $i++)
// {
// 	$c3ma->setRGB($i, 0, 0, 255);
// }

// var_dump($c3ma->getRGB(2));
