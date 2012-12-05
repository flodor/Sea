<?php
require_once 'xajax/xajax_core/xajax.inc.php';

class Sea_Xajax extends xajax {
	
	/**
	 * Emplacement des fichier contenat les focntions
	 * 
	 * @var String
	 */
	protected $_directory;
	
	/**
	 * 
	 * contient l'instance de l'objet ( singleton)
	 * 
	 * @var Sea_Xajax
	 */
	protected static  $_instance;
	
	
	/**
	 * 
	 * contient le nom des fonction chargé
	 * 
	 * @var unknown_type
	 */
	protected $_function = array();
	
  	/**
     * constructeur du singleton
     * 
     * @return Sea_Xajax
     */
    static function getInstance() {
        
        if (!isset(self::$_instance)) {
        	$class = get_called_class();// récuperation de la class a créer
        	self::$_instance = new $class();
        }
        return self::$_instance;
    }
    
	/**
	 * renvoie le fichier censé contenir la fonction
	 * 
	 * @param String $function
	 * @return String
	 */
    protected function _formatFunctionFile($function) {
    	return $this->getDirectory() . '/' . str_replace('_', '/', $function) . '.php';
    }
    
    /**
     * Enregistrer une fonction dans l'instance
     * 
     * @param String $function
     * @return xajaxUserFunction
     */
    public function registerFunction($function) {
    	
		// on regarde si la fonction n'a pas deja ete chargé
    	if (array_search($function, $this->_function) === false) {
    		
    		$this->_function[] = $function;
    	
			// on charge le fichier contenant la fonction
			if (!file_exists($filename = $this->_formatFunctionFile($function))) {throw new Exception(sprintf('Le fichier n\'existe pas : %s', $filename));}
	    	require_once $this->_formatFunctionFile($function);
	
	   		if (!function_exists($function)) {throw new Exception('La function xajax n\'existe pas : ' . $function);}
	   		   	
	    	$this->register(XAJAX_FUNCTION, $function);
    	}
    	return $this;
    }
    
    /**
     * check si une fonction a été chargé
     * 
     */
    public function isEmpty() {
    	return empty($this->_function);
    }
	
	/**
	 * @return the $_directory
	 */
	public function getDirectory() {
		return $this->_directory;
	}

	/**
	 * @param String $_directory
	 */
	public function setDirectory($_directory) {
		$this->_directory = $_directory;
		return $this;
	}
}

?>