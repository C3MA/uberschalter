<?php

/**
 * Abstract class which serves as an enum to allow nice switch-case
 * statements or decisions like if($foo == LightType::RGB)
 */
abstract class LightType{
	const RGB = "C3MARGB";
	const BINARY = "C3MABinary";
}


abstract class Status{
	const ENABLED = "h";
	const DISABLED = "l";
}

class C3MALight{

	/**
	 * @var holds the PATH to the configuration
	 */
	private static $config = "config.ini";

	/**
	 * @var holds the actual configuration after construction
 	 */
	private $configuration;
	
	private $connection;

	public function __construct($type) {
		$this->configuration = parse_ini_file(self::$config,TRUE);
		$this->connection = $this->getConnection($type);
	}
	
	public function __destruct() {
		$this->closeConnection($this->connection);
	}

	/**
	 * Returns a socket connection to a specific type of lightning
	 * infrastrcuture.
	 *
	 * @param $type LightType object 
	 * @return Socket or throw exception
	 */
	public function getConnection($type) {
		$section = $this->configuration[$type];
		$fp = fsockopen($section["host"], $section["port"], $errno, $errstr, 1);
		if(!$fp){
			throw new Exception($errno." : ".$errstr);
		}else{
			return $fp;
		}
	}


	/**
	 * Closes a socket passed as parameter
	 * 
	 * @param $fp Socket
	 */
	public function closeConnection($fp) {
		fclose($fp);
	}

/**
 * for binary switched lights the following commands apply:
 * ollpera | read actual status of all lamps reponse looks like:
 *
 * inputSize: 8
 * receiced: ollpera
 * states 11001111
 * ACK
 *
 * ollpew<n><h|l> | write status <l> (low, off) or <h> (high, on) 
 * 		  | to lamp with id <n>. Response same as ollpera
 */
	
	public function getBinaryAll() {
		fwrite($this->connection, "ollpera\n");
		$response = "";
		do{
			$response .= fgets($this->connection);
		}while(!strstr($response,"ACK"));
		
		$result = explode(' ',strstr(str_replace(array("\n"), ' ',$response),"states"));
		return $result[1];
	}

	public function setBinary($number, $status) {
		fwrite($this->connection, "ollpew".$number.$status."\n");
	}
	


/**
 * for rgb switched lights the following commands apply:
 * dmx write <channel> <value> | where channel starts with 1 and
 * 			       | and value is numeric uint_8
 * dmx fill <start> <end> <value> | fills everything with a value
 * 
 * Return values of each command: not relevant, "ch>" indicates
 * that new command can be send
 * 
 * dmx show | returns actual status of all values (channels) as HEX
 * values. 
 */

	/**
	 * @param number	index of the lamp (starting with zero)
	 * @param red		0 - 255
	 * @param green		0 - 255
	 * @param blue		0 - 255
	 */
	public function setRGB($number, $red, $green, $blue) {
		$offsetRed = ($number * 3) + 1;
		$offsetGreen = ($number * 3) + 2;
		$offsetBlue = ($number * 3) + 3;
	
		fwrite($this->connection, "dmx write ".$offsetRed." ".$red."\r\r");
		$response = "";
		do{
			$response .= fgets($this->connection);
		}while(!strstr($response,"ch>"));
/*		print($response);*/

		fwrite($this->connection, "dmx write ".$offsetGreen." ".$green."\r\r");
		$response = "";
		do{
			$response .= fgets($this->connection);
		}while(!strstr($response,"ch>"));
/*		print($response);*/

		fwrite($this->connection, "dmx write ".$offsetBlue." ".$blue."\r\r");
		$response = "";
		do{
			$response .= fgets($this->connection);
		}while(!strstr($response,"ch>"));
/*		print($response);*/
	}
	
	/**
	 * @param number	index of the lamp (starting with zero)
	 */
	public function getRGB($number) {
		fwrite($this->connection, "\r\rdmx show\r\r");
        $response = "";
        do {
        	$response .= fgets($this->connection);
		} while($response == NULL || !strstr($response,"ch>"));
		/*print($response); flush(); ob_flush();*/ 
		
		/* extract the dmx buffer */
		preg_match('/[0-9A-F]+/', $response, $matches, PREG_OFFSET_CAPTURE, 32);
		if ( intval( count($matches, COUNT_RECURSIVE) ) <= 0) {
			print("too tiny\n");
			print("Got: " . $response . "\n");
			return null;
		}

		/* extract the dmx buffer */
		$completeDMX = $matches[0][0];

		preg_match('/[0-9A-F]{6}/', $completeDMX, $lampGroups, PREG_OFFSET_CAPTURE, $number * 6);
		if ( intval( count($lampGroups, COUNT_RECURSIVE) ) <= 0) {
			print("too tiny part 2\n");
			print("Got: " . $response . "\n");
			return null;
		}

		$lampstatus = $lampGroups[0][0];
		/* Send the output to the user */
		return "{\n".
			"red : ".hexdec(substr($lampstatus, 0, 2)).",\n".
			"green : ".hexdec(substr($lampstatus, 2, 2)).",\n".
			"blue : ".hexdec(substr($lampstatus, 4, 2))."\n".
			"}\n";
	}
}

$c3ma = new C3MALight(LightType::RGB);
var_dump($c3ma->getRGB(1));
echo( "Got state for the first time\n" ); /*flush(); ob_flush();*/

for ($i = 0; $i < 6; $i++)
{
	$c3ma->setRGB($i, 0, 0, 0);
}

print("Updated state\n"); /*flush(); ob_flush();*/
var_dump($c3ma->getRGB(1));

echo( "Got state for the second time\n" ); /*flush(); ob_flush();*/
//var_dump($c3ma->getBinary(1));


/*
$c3ma = new C3MALight();
var_dump($c3ma->getBinary(1));
$c3ma->setBinary(4, Status::ENABLED);
*/
