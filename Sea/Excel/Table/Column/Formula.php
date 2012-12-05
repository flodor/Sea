<?php

require_once 'Abstract.php';

/**
 * classe de gestion de colonne contenant des formule
 * pour tabelau de fichier excel
 * 
 * @author jhouvion
 *
 */
class Sea_Excel_Table_Column_Formula extends Sea_Excel_Table_Column_Abstract {

	/**
	 * Formule Excel pour la colone.
	 * 
	 * @var String $_formula
	 */
	protected $_formula;
	
	/**
	 * Constructeur
	 * 
	 * @param String $label
	 * @param String $formula
	 */
	function __construct($label,$formula) { 
		$this->_formula = $formula; 
		$this->_label = $label;
	}
	
	
	/**
	 * Calcul le rendu de la ligne
	 * 
	 * @param Interger $l
	 * @param Char $c
	 * @param Multi $c
	 * @param  Donnée de la ligne $Data (non utilisé)
	 */
	public function render($sheet, $l, $c, $data) {
		
		$formula = str_replace('%l', $l, $this->_formula);// on parse la formule pour y inclure le numero de la ligne
		$formula = str_replace('%c', $c, $formula);//on parse la formule pour y inclure la  colonne

		// insertion de la cellule
		$sheet->setCellValue($c.$l, $formula);
	}
}

?>