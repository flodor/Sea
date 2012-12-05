<?php

/** 
 * Contient les information de génération d'un tableau Excel.
 * 
 * @author jhouvion
 * 
 * 
 */
class Sea_Excel_Table {
	
	/**
	 * ligne de demmarage du rendu du tableau
	 * 
	 * @var Integer
	 */
	protected $_start_line = 1;
	
	
	/**
	 * colonne du demarrage du rendu du tableau
	 * 
	 * @var String
	 */
	protected $_start_column = 'A';
	
	/**
	 * Donnée a populer dans les colonnes
	 * 
	 * @var Array
	 */
	protected $_data = array();
	
	/**
	 * colonne
	 * 
	 * @var unknown_type
	 */
	protected $_column;
	
	/**
	 * Defini si un affichage du header sera fait
	 * 
	 * @var unknown_type
	 */
	protected $_renderHeader = TRUE;
	
	
	/**
	 * contient les informations de mise en page de l'entete
	 * 
	 * @var Array
	 */
	public $headerStyle = array('font'    => array(
									'size' => '10',
									'bold'      => true,
									'color' => array('rgb' =>'FFFFFF')
								),
								'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'fill' => array('type'       => PHPExcel_Style_Fill::FILL_SOLID,	  			
						 						'startcolor' => array('rgb' => '000080')
						 		)
							);
	
	
	function __construct() {}
	
	
	/**
	 * GETTER AND SETTER
	 * 
	 * 
	 */
	
	/**
	 * Setter pour les données
	 * 
	 * @param $d
	 */
	function setData($d) {
		$this->_data = $d;
		return $this;
	}
	
	/**
	 * getter pour la ligne de depart
	 * 
	 */
	public function getStartLine() {
		return $this->_start_line;
	}
	
	/**
	 * getter pour la colonne de depart
	 * 
	 */
	public function getStartColumn() {
		return $this->_start_column;
	}
	
	/**
	 * setter pour la ligne de depart
	 * 
	 * @param unknown_type $l
	 */
	public function setStartLine($l) {
		$this->_start_line = intval($l);
		return $this;
	}
	
	/**
	 * setter pour la colonne de depart
	 * 
	 * @param unknown_type $c
	 */
	public function setStartColumn($c) {
		$this->_start_column = strtoupper($c);
		return $this;
	}

	/**
	 * renvoie le nombre de ligne du tableau
	 * 
	 * @param unknown_type $c
	 */
	public function count() {
		return count($this->_data);
	}
	
	/**
	 * renvoie si l'on affche l'en tete
	 */
	public function isRenderHeader(){
		return $this->_renderHeader;
	}
	
	/**
	 * AUTRE METHODE
	 * 
	 * 
	 * 
	 */

	/**
	 * Ajout d'une colonne a la table
	 * 
	 * @param $c
	 * @return Sea_Excel_Table
	 */
	function add(Sea_Excel_Table_Column_Abstract  $c) {$this->_column[] = $c;return $this;}
	
	/**
	 * test si le tableau est vide
	 * 
	 */
	public function isEmpty() {
		return empty($this->_data) || empty($this->_column);
	}
	
	/**
	 * retrouve le nom d'une colonne a partir de sa position
	 * 
	 * @param $index
	 */
	protected function _getColumnFromPosition($index) {
		
		$column = '';//initialisation
		$t = base_convert($index, 10, 26);// recuperation au format d'une base 26
		$a = str_split(strtoupper($t));// decoupage du nom de la colonne
		
		// convertion de la base 26 en nom de colonne
		for ($i = 0; $i < count($a) ; $i++) {
			$retenue = (count($a)> 1 && ($i + 1) < count($a)) ? -1 : 0;// gestion de la retenu
			$column .= chr(base_convert($a[$i], 26, 10) + $retenue + ord('A'));
		}
		
		return $column;
	}
	
	/**
	 * Transforme un nom de colonne en index numeric
	 * 
	 * @param unknown_type $c
	 */
	protected function _getPositionFromColumn($column) {

		$index = 0; //initialisation de l'index
		$a = str_split(strtoupper($column));// decoupage du nom de la colonne
		$a = array_reverse($a);// inversement de l'ordre des charactere
		
		// transformation de la valeur de la colonne en entier
		for ($i = 0; $i < count($a) ; $i++) {
			$retenue = (($i + 1) < count($a) || count($a) == 1) ? 0 : 1;// gestion de la retenu
			$t = ord($a[$i]) + $retenue - ord('A'); // recuperation de la valeur en base 26
			$index += bcpow(26,$i) * $t; // incrémentation
		}
		
		return $index;
	}
	
	/**
	 * Renvoie la prochaine la lettre de la prochaine colone
	 * 
	 * @param $c
	 */
	protected function _getColumnFromIncrement($origColumn, $increment = 1) {
	
		if (!preg_match('/^[[:alpha:]]{1,}/', $origColumn) || !is_int($increment)) {
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Erreur de format des paramètre');	
		}
		
		$index = $this->_getPositionFromColumn($origColumn);// récupération de l'index
		$index += $increment;// incrémentation voulue
		return $this->_getColumnFromPosition($index);// retour de la valeur de la nouvellle colonne
	}
	
	/**
	 * renvoie le code a integrer
	 */
	function render($sheet) {
		
		if (count($this->_column) == 0) {
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Aucune colonne n\'est spécifié');	
		}
		
		// initialisation
		$c = $this->_start_column;
		$l = $this->_start_line;
		
		// rendu de l'en-tete
		if ($this->isRenderHeader()) {
			foreach ($this->_column as $column) {
				$sheet->setCellValue($c.$l, $column->getLabel());
				$sheet->getStyle($c.$l)->applyFromArray($this->headerStyle);
				$c = $this->_getColumnFromIncrement($c);
			}
			$c = $this->_start_column;
			$l++;
		}
		
		// rendu du contenu
		foreach ($this->_data as $row) {
			foreach ($this->_column as $column) {
				$column->render($sheet, $l, $c, $row);
				$c = $this->_getColumnFromIncrement($c);
			}
			$l++;// incrémentation de la ligne
			$c = $this->_start_column;// retour a la premiere colonne
		}
	}
}

?>
