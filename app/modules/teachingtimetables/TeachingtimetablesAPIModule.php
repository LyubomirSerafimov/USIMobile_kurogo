<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class TeachingTimetablesAPIModule extends APIModule {

	protected $id='teachingtimetables';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='teachingtimetables', $command='', $args=array()) {
		$module = new TeachingTimetablesAPIModule();
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
				$timetables = $this->getTeachingTimetables();
				$this->setResponse($timetables);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getTeachingTimetables(){ 
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
				$error->title = 'Teaching Timetables';
				$error->message = 'Getting the teaching timetables failed. No information available in the database.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}
}
