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

	public function __construct(){
		$this->configuration = parse_ini_file(self::$config,TRUE);
	}

	/**
	 * Returns a socket connection to a specific type of lightning
	 * infrastrcuture.
	 *
	 * @param $type LightType object 
	 * @return Socket or throw exception
	 */
	private function getConnection($type) {
		$section = $this->configuration[$type];
		$fp = fsockopen($section["host"], $section["port"], $errno, $errstr, 3);
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
	private function closeConnection($fp) {
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
	public function getBinaryAll(){
		$connection = $this->getConnection(LightType::BINARY);
		fwrite($connection, "ollpera\n");
		$response = "";
		do{
			$response .= fgets($connection);
		}while(!strstr($response,"ACK"));
		$this->closeConnection($connection);
		$result = explode(' ',strstr(str_replace(array("\n"), ' ',$response),"states"));
		return $result[1];
	}

	public function getBinary($number){
		$result = $this->getBinaryAll();
		return $result{$number-1};
	}

	public function setBinary($number, $status) {
		$connection = $this->getConnection(LightType::BINARY);
		fwrite($connection, "ollpew".$number.$status."\n");
		$this->closeConnection($connection);
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

}

$c3ma = new C3MALight();
var_dump($c3ma->getBinary(1));


$c3ma->setBinary(1, Status::ENABLED);
