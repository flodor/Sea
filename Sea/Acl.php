<?php

require_once 'Zend/Auth.php';

/** 
 * @author jhouvion
 * 
 * 
 */
abstract class Sea_Acl  extends Zend_Auth {
	
	/**
	 * 
	 * Controller de redirection probleme d'acces
	 * 
	 * @var String
	 */
	protected $_controller = 'index';
	
	/**
	 * Action de redirection si probleme d'acces
	 * 
	 * @var unknown_type
	 */
	protected $_action = 'login';
	
	/**
	 * Navigation
	 * 
	 * @var Zend_Navigation
	 */
	protected $_navigation = null;

	/**
	 * constructeur
	 * 
	 */
	protected function __construct() {
	
		// méthode a surcharger pour creation adapter
		$this->init();
	}
	
	abstract public function init();
	
	
	/**
	 * authentifaction
	 * 
	 * @param String $username
	 * @param String $password
	 */
	abstract public function process($username, $password);
	
	/**
     * coinstrcuteur du singleton
     * 
     * @return Sea_Application_Model_ACL_Abstract
     */
    static function getInstance() {
        if (!isset(self::$_instance)) {  
        	$class = get_called_class();// récuperation de la class a créer
        	self::$_instance = new $class();
        }
        return self::$_instance;
    }
	
	
	/**
	 * verification d'acces ou redirection vers la page de login
	 * 
	 */
	public function route(){
		if (!$this->hasIdentity()) {
			$front = Zend_Controller_Front::getInstance();
			$request = $front->getRequest();	
			$request->setControllerName($this->_controller);
			$request->setActionName($this->_action);
		}
	}
	/**
	 * @return the $_navigation
	 */
	public function getNavigation() {
		return $this->_navigation;
	}

	/**
	 * @param Zend_Navigation $_navigation
	 */
	public function setNavigation($_navigation) {
		$this->_navigation = $_navigation;
	}
	/**
	 * @return the $_controller
	 */
	public function getController() {
		return $this->_controller;
	}

	/**
	 * @return the $_action
	 */
	public function getAction() {
		return $this->_action;
	}

	/**
	 * @param String $_controller
	 */
	public function setController($_controller) {
		$this->_controller = $_controller;
	}

	/**
	 * @param unknown_type $_action
	 */
	public function setAction($_action) {
		$this->_action = $_action;
	}
	
	/**
	 * recupère un attribut de la session
	 * 
	 * @param mixed $key
	 */
	public function getAttr($key) {
		
		// recuperarion de la session
		$session = (array) $this->getStorage()->read();

		//r envoie la valeur associé
		return array_key_exists($key, $session) ? $session[$key] : false;
	}
}

?>