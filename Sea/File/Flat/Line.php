<?php
/**
 * Gestion des lignes d'un fichier plat
 * 
 * @author julien.houvion@businessdecision.com
 */


class Sea_File_Flat_Line {
	
	/**
	 * contenu de la ligne
	 * 
	 * @var $_content unknown_type
	 */
	protected $_content = '';
	
	
	/**
	 * Constructeur
	 * 
	 */
	public function __construct() {}
	
	/**
	 * Remise a 0 du contenu
	 * 
	 */
	public function reset() {$this->_content = '';}
	
	/**
	 * test si la ligne possede un contenu
	 * 
	 * @return Boolean
	 */
	public function isEmpty() {return empty($this->_content);}
	
	/**
	 * Ajoute du contenu a la ligne
	 * 
	 * @param String $content
	 * @param Integer $size
	 * @param Boolean $padright
	 * @param $fill
	 */
	public function add($content, $size = false, $fill = ' ', $padright = true) {
		
		// traitement de la taille
		$size = $size ? intval($size) : strlen($content);
		
		$this->_content .= str_pad( substr ( $content, 0, $size ), $size, $fill, ($padright ? STR_PAD_RIGHT : STR_PAD_LEFT) );
		return $this;
	}
	
	
	/**
	 * Renvoie la construction de la ligne courante
	 * 
	 */
	public function render() {return $this->_content;}
	
	
	/**
	 * Renvoie la construction de la ligne courante
	 * 
	 */
	public function __toString() {return $this-> render();}
}