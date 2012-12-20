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
		$update['academiccalendar'] = $this->getAcademicCalendarHash();
		$update['teachingtimetables'] = $this->getTeachingTimetablesHash();
		$update['examinationtimetables'] = $this->getExaminationTimetablesHash();
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

	private function getAcademicCalendarHash() {
		$table = $this->getModuleVar('academiccalendar','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table limit 1";
		$result = $db->query($sql);
		$row = $result->fetch();
		if($row == false) {
			$this->raiseError(0);
		} else {
			return $this->hash($row['timemodify']);
		}
	}

	private function getTeachingTimetablesHash() {
		$table = $this->getModuleVar('teachingtimetables','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table";
		$result = $db->query($sql);
		$entries = $result->fetchAll();
		if($entries == false) {
			$this->raiseError(1);
		} else {
			return $this->hash($entries);
		}
	}

	private function getExaminationTimetablesHash() {
		$table = $this->getModuleVar('examinationtimetables','db_tables');
		$db = new db();
		$sql = "SELECT timemodify FROM $table";
		$result = $db->query($sql);
		$entries = $result->fetchAll();
		if($entries == false) {
			$this->raiseError(2);
		} else {
			return $this->hash($entries);
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
			$this->raiseError(3);
		} else {
			return $this->hash($row['timemodify']);
		}
	}


	public function raiseError($code) {
		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Updates: Academic Calendar';
				$error->message = 'Getting the hash check on Academic Calendar failed.';
				break;
			case 1:
				$error->title = 'Updates: Teaching Timetables';
				$error->message = 'Getting the hash check on Teaching Timetables failed.';
				break;
			case 2:
				$error->title = 'Updates: Examination Timetables';
				$error->message = 'Getting the hash check on Examination Timetables failed.';
				break;

			case 3:
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
