<?php
error_reporting(E_ERROR);

class UsipeopleAPIModule extends APIModule {

	protected $id='usipeople';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usipeople', $command='', $args=array()) {
		$module = new UsipeopleAPIModule();
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
				//$usidb = new USIdb();
				//$result = $usidb->getPeople();
				$usiSearch = new USISearch();
				$result = $usiSearch->getPeopleList();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result->data);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
