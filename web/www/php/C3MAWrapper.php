<?php 
require_once 'C3MALight.php';

class C3MAWrapperRGB{

	private $lightController;
	
	public function __construct(){
		$this->lightController = new C3MALight(LightType::RGB);
	}

	public function __destruct() {
		$lightController->closeConnection($this->connection);
	}
}

class C3MAWrapperBinary{

	private $lightController;
	
	public function __construct(){
		$this->lightController = new C3MALight(LightType::BINARY);
	}

	public function __destruct() {
		$lightController->closeConnection($this->connection);
	}
}