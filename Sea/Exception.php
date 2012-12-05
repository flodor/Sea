<?php

require_once ('Zend/Exception.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Exception extends Zend_Exception {
	
	
	/**
	 * surcharge du constructeur
	 * 
	 * @param unknown_type $message
	 */
	public function __construct($message) {
		parent::__construct(call_user_func_array('sprintf', func_get_args()));
	}
}

?>