<?php

require_once 'Abstract.php';

class Sea_Excel_Table_Column_Value extends Sea_Excel_Table_Column_Abstract {
	
	
	/**
	 * identifiant de la colonne
	 * correspond a la cle du tableau contenant la donnée a prendre
	 * 
	 * @var $_id unknown_type
	 */
	protected $_id;
	
	
	/**
	 * Constructeur
	 * 
	 * @param $label
	 * @param $id
	 */
	function __construct($label,$id = '') { 
		$this->_id = $id; 
		$this->_label = $label;
	}
	
	/**
	 * Calcul le rendu de la ligne
	 * 
	 * @param Interger $l
	 * @param Char $c
	 * @param Multi $c
	 */
	public function render($sheet, $l, $c, $data) {
		
		// on verifie que l'on a bien une donnée a inserer
		if (!array_key_exists($this->_id, $data)) {throw new Zend_Exception('Aucune donnée disponible pour l\'index : "'.$this->_id.'"');}
		
		// insertion de la cellule
		//Modification Martial RICHARD 28/05/2010 Ticket Redmine 426
		//Ajout d'un utf8_encode pour le problème d'accent
		if (mb_detect_encoding($data[$this->_id], array("UTF-8", "ISO-8859-1", "ASCII")) != "UTF-8") {
			$data[$this->_id] = utf8_encode($data[$this->_id]);
		}
		
		$sheet->setCellValue($c.$l, $data[$this->_id]);
	}
}

?>