<?php

require_once 'Zend/Mail.php';

/**
 * Ne fonctionne que dans un environement MVC
 * 
 * 
 * @author jhouvion
 *
 */
class Sea_Mail extends Zend_Mail {

	/**
	 * 
	 * Charset par default
	 * 
	 * @var unknown_type
	 */
	protected $_charset = 'UTF-8';
	
	
	/**
	 * Constructeur pour un Mail en UTF-8 par défaut 
	 * @param string $charset
	 */
	public function __construct() {
		
		parent::__construct();
		
		// rfécuperere les information passé en paramètre
		$args = func_get_args();
		
		//on apelle la function init avec les paramètres passé au constructeur
		call_user_func_array(array($this, 'init'), $args);
	}

	/**
	 * Ajout d'un piece jointe
	 * 
	 * @param unknown_type $filename
	 * @return self
	 */
	public function addAttachementFile($filename, $name = false) {
		
		// on verifie l'existance du fichier
		if (!file_exists($filename) || !is_readable($filename)) {throw new Exception('Impossible de charger le fichier');}
		$this->createAttachment(file_get_contents($filename), mime_content_type($filename), Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, ($name ? $name : basename($filename)));
		return $this; 
	}
	
	/**
	 * process de constuction  du mail
	 * 
	 */
	public function init(){null;}
}
