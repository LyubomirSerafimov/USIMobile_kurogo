<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class ServicesAPIModule extends APIModule {

	protected $id='services';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='services', $command='', $args=array()) {
		$module = new ServicesAPIModule();
		$module->init($command, $args);
		return $module;
	}

	public function initializeForCommand() {  
		switch ($this->command) {
			case 'hello':
				$response = array(
					'salute'=>'hi there'
				);
				$this->setResponse($response);
				$this->setResponseVersion(1);
				break;
			case 'get':
				$services = $this->getServices();
				$this->setResponse($services);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getServices(){ 
		$table = $this->getModuleVar('table','db');
		$db = new db();
		$sql = "SELECT * FROM $table";
		$result = $db->query($sql);
		$row = $result->fetchAll();
		if($row == false){
			$this->raiseError(0);
		} else {
			return $row;
		}
	}

	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Services';
				$error->message = 'Getting the Services info failed. No information available in the database.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}
}
