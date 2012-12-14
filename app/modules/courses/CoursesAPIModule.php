<?php
error_reporting(E_ERROR);

class CoursesAPIModule extends APIModule {

	protected $id='courses';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='courses', $command='', $args=array()) {
		$module = new CoursesAPIModule();
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
				$usidb = new USIdb();
				$result = $usidb->getCourses();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
