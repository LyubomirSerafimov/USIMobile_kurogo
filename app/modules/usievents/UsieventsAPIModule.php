<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsieventsAPIModule extends APIModule {

	protected $id='usievents';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usievents', $command='', $args=array()) {
		$module = new UsieventsAPIModule();
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
			case 'get_list':
				$usievents = new USIevents();
				$result = $usievents->getList();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'get_item':
				$usievents = new USIevents();
				$result = $usievents->getDetails($this->args['id']);
				// check for errors
				if(KurogoError::isError($result)) { $this->throwError($result); }
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}
}
