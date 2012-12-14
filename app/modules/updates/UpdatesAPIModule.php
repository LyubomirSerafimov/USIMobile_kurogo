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

	private function getUpdates() {
		$update = array();
		$update['courses'] = $this->getCoursesHash();
		$update['teachingtimetables'] = $this->getTeachingTimetablesHash();
		$update['usinews'] = $this->getUSINewsHash();
		$update['menumensa'] = $this->getMenuMensaHash();
		return $update;
	}

	private function hash($arg) {
		if(is_array($arg) || is_object($arg)) {
			$content = '';
			foreach($arg as $key => $value) {
				if(is_array($value) || is_object($value)) {
					$content.=$this->hash($value);
				} else {
					$content.=$value;
				}
			}
			return md5($content);
		} else if(is_string($arg) or is_numeric($arg)) {
			return md5($arg);
		} else {
			return false;
		}
	}

	private function getCoursesHash(){
		$usidb = new USIdb();
		$result = $usidb->getCourses();
		// check for errors
		if(KurogoError::isError($result)) {
			$this->throwError($result);
		}
		return $this->hash($result);
	}

	private function getTeachingTimetablesHash() {
		$table = $this->getModuleVar('teachingtimetables','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table";
		$result = $db->query($sql);
		$row = $result->fetchAll();
		if($row == false) {
			$this->raiseError(0);
		} else {
			return $this->hash($row);
		}
	}

	private function getUSINewsHash() {
		$usinews = new USInews();
		$result = $usinews->getList();
		// check for errors
		if(KurogoError::isError($result)) {
			$this->throwError($result);
		} else {
			return $this->hash($result);
		}
	}

	private function getMenuMensaHash() {
		$table = $this->getModuleVar('menumensa','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table limit 1";
		$result = $db->query($sql);
		$row = $result->fetch();
		if($row == false) {
			$this->raiseError(1);
		} else {
			return $this->hash($row['timemodify']);
		}
	}


	public function raiseError($code) {
		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Updates: Teaching Timetables';
				$error->message = 'Getting the hash check on Teaching Timetables failed.';
				break;
			case 1:
				$error->title = 'Updates: Menu mensa';
				$error->message = 'Getting the hash check on Mensa Menu failed.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}
}
