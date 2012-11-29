<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class MensaAPIModule extends APIModule {

	protected $id='mensa';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='mensa', $command='', $args=array()) {
		$module = new MensaAPIModule();
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
			case 'get_menu':
				$info = $this->getInfo();
				$this->setResponse($info);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getInfo(){ 
		$table = $this->getModuleVar('table','db');
		$db = new db();
		$sql = "SELECT * FROM $table limit 1";
		$result = $db->query($sql);
		$row = $result->fetch();
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
				$error->title = 'Menu mensa info';
				$error->message = 'Getting the menu failed. No information available in the database.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}

}
