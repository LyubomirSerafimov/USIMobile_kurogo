<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UpdatesAPIModule extends APIModule {

	protected $id='updates';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='updates', $command='', $args=array()) {
		$module = new UpdatesAPIModule();
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
			case 'check':
				$updates = $this->getUpdates();
				$this->setResponse($updates);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getUpdates(){ 
		$update = array();
		$mensa = $this->getMensaUpdate();
		$update['menumensa'] = $mensa['timemodify'];
		return $update;
	}

	private function getMensaUpdate(){
		$table = $this->getModuleVar('menumensa','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table limit 1";
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
				$error->title = 'Updates: Menu mensa';
				$error->message = 'Getting the menu failed. No information available in the database.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}

}
