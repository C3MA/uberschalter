<?php 
require_once 'C3MALight.php';

class C3MAWrapperRGB{

	private $lightController;
	
	public function __construct(){
		$this->lightController = new C3MALight(LightType::RGB);
	}

	public function __destruct() {
		unset($lightController);
	}
}

class C3MAWrapperBinary{

	private $lightController;
	
	public function __construct(){
		$this->lightController = new C3MALight(LightType::BINARY);
	}

	public function __destruct() {
		unset($lightController);
	}
	
	public function get($id) {
		$this->isValidID($id);
		return json_encode(array( $id => $this->lightController->getBinary($id)));
	}
	
	public function set($id,$value) {
		$this->isValidID($id);
		switch($value){
			case Status::ENABLED:
			case 1:
			case true:
			case "true":
				$this->lightController->setBinary($id, Status::ENABLED);
				break;
			case Status::DISABLED:
			case 0:
			case false:
			case "false":
				$this->lightController->setBinary($id, Status::DISABLED);
				break;
			default:
				return new Exception("You are an idiot",406);
		}
	}
	
	public function isValidID($id) {
		return (is_int($id) && $id >= 1 && $id <= 6)? true : new Exception("Lamp must be between 1 and 6",406);
	}
}