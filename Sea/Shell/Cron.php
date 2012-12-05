<?php
require_once 'Sea/Shell.php';

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Shell_Cron extends Sea_Shell {
	
	/**
	 * initialisation
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Shell::init()
	 */
	public function init($controller, $action, $token = false) {
		
		//paramètrage
		$this->addArg('-c', $controller);// controller
		$this->addArg('-a', $action);// action
		$this->_command = APPLICATION_PATH . '/cron_launcher';// executable
		$this->addArg('-e', APPLICATION_ENV);// environnement
		
		// gestion du token -- permet de retrouver le processus pour echange avec client
		$this->addArg('-t', $token ? $token : base64_encode($controller . $action . date('U')));
		
		// gestion des paramètres supplementaires
		if (func_num_args() > 3) {foreach((array) array_slice( func_get_args(), 3) as $key => $value) {$this->addArg($key, $value);}}
	}
}

?>