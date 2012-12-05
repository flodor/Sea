<?php
require_once ('Zend/Search/Lucene.php');
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Lucene extends Zend_Search_Lucene
{
	/**
	 * repertoir de l'index
	 * 
	 * @var unknown_type
	 */
	protected $_directory;
   
	/**
	 * constrcuteur
	 * 
	 * @param unknown_type $filename
	 */
	public function __construct() {
		
		// rfécuperere les information passé en paramètre
		$args = func_get_args();
		
		//on apelle la function init avec les paramètres passé au constructeur
		call_user_func_array(array($this, 'init'), $args);
		
		//récuperation de l'index ou création de celui ci s'il n'existe pas
		parent::__construct($this->_directory, !is_dir($this->_directory));
	}
	
	/**
	 * Reconstruit un index vierge
	 * 
	 */
	public function flush() {
		
		// suppresion du repertoire
		rrmdir($this->_directory);
		
		// reconstruction de l'index
		parent::__construct($this->_directory, !is_dir($this->_directory));
	}
	
	/**
	 * function a surcharger
	 * 
	 */
	public function init($_directory) {$this->_directory = empty($_directory) ? $this->_directory : $_directory;}
	
	
}
?>