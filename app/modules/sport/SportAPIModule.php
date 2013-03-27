<?php
error_reporting(E_ERROR);
class SportAPIModule extends APIModule {

	protected $id='sport';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='sport', $command='', $args=array()) {
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
				$result = $usidb->getSportActivities();
				// check for errors
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'check_membership':
				$usisport = new USIsportSOAP();
				$result = $usisport->checkMembership($this->args['username'], $this->args['password']);
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'check_subscription':
				$usisport = new USIsportSOAP();
				$result = $usisport->checkSubscription($this->args['username'], $this->args['password'], $this->args['activity']);
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'subscribe':
				$usisport = new USIsportSOAP();
				$result = $usisport->subscribe($this->args['username'], $this->args['password'], $this->args['activity']);
				if(KurogoError::isError($result)) {
					$this->throwError($result);
				}
				$this->setResponse($result);
				$this->setResponseVersion(1);
				break;
			case 'unsubscribe':
				$usisport = new USIsportSOAP();
				$result = $usisport->unsubscribe($this->args['username'], $this->args['password'], $this->args['activity']);
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
