<?php

require_once 'Abstract.php';

/**
 * Contrôleur de données d'entrée et de sortie pour une simple chaîne de caractères
 * 
 * @author Tibor Vass
 *
 */
class Sea_Replace_Data_String extends Sea_Replace_Data_Abstract {
	
	/**
	 * Constructeur prenant le texte en input, et une référence de chaîne de caractères en output
	 * 
	 * @param string $text
	 * @param string &$result
	 * 
	 */
	public function __construct($text, &$result) {
		 $this->_load($text);
		 $this->_result = &$result;
	}
	
	public function read() {
		return $this->current();
	}
	
	public function write($modifiedText) {
		$this->_result = $modifiedText;
	}

	protected function _load($text) {
		$this->append($text);
	}
	
	protected $_result;
	
}
