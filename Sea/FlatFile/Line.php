<?php

/**
 * Gère les lignes de la FlatFile
 * 
 * @author Ahmed ZERZERI
 * @since 15/03/2010
 *
 */
class Sea_FlatFile_Line {
	
	/**
	 * Numéro de la ligne
	 * 
	 * @var integer
	 */
	protected $_number;
	
	/**
	 * Contenu de la ligne
	 * 
	 * @var string
	 */
	protected $_content;
	
	/**
	 * Getter pour le numéro de la ligne
	 * 
	 * @return the $_numeber
	 */
	public function getNumber() {
		return $this->_number;
	}
	
	/**
	 * Getter pour le contenu de la ligne
	 * 
	 * @return the $_content
	 */
	public function getContent() {
		return $this->_content;
	}
	
	/**
	 * Setter pour le contenu de la ligne
	 * 
	 * @param $_content the $_content to set
	 */
	public function setContent($_content) {
		$this->_content = $_content;
	}
	
	/**
	 * Constructeur de la classe Line
	 * 
	 * @param integer $lineNumber
	 */
	function __construct($lineNumber = 0) {
		$this->_number = $lineNumber;
		$this->_content = "";
	}
	
	/**
	 * Ajoute une chaîne de caractères à la ligne
	 * 
	 * @param string $_content
	 */
	function addContent($_content) {
		$this->_content .= $_content;
	}

}