<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsidbAPIModule extends APIModule {

	protected $id='usidb';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usidb', $command='', $args=array()) {
		$module = new UsidbAPIModule();
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
			case 'get_courses':
				$courses = $this->getCourses();
				$this->setResponse($courses);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function getDBConfig(){
		return array(
			'DB_TYPE'   => $this->getModuleVar('DB_TYPE', 'database'),
			'DB_HOST' 	=> $this->getModuleVar('DB_HOST', 'database'),
			'DB_USER' 	=> $this->getModuleVar('DB_USER', 'database'),
			'DB_PASS'   => $this->getModuleVar('DB_PASS', 'database'),
			'DB_DBNAME' => $this->getModuleVar('DB_DBNAME', 'database'),
			'DB_CREATE' => $this->getModuleVar('DB_CREATE', 'database'),
			'DB_FILE'   => $this->getModuleVar('DB_FILE', 'database'),
			'DB_DEBUG'  => $this->getModuleVar('DB_DEBUG', 'database'),
		);
	}

	private function getCourses(){
		try {
			$config = $this->getDBConfig();
			$db = new db($config);
			$sql = "SELECT * FROM Corsi";
			$result = $db->query($sql);
			$row = $result->fetch();
			return $row;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			//$this->raiseError(0);
		}
	}

	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Usidb: Menu mensa';
				$error->message = 'Getting the menu failed. No information available in the database.';
				break;
			default:
				$error->title = 'Unknown error';
				$error->message = 'Unknown error occured';
		}
		$this->throwError($error);
	}

}
