<?php

require_once 'Sea/Datagrid/Html/Column.php';

/**
 * colonne de type text
 * 
 * @author jhouvion
 *
 */
class Sea_Datagrid_Html_Column_Text extends Sea_Datagrid_Html_Column {
	
	/**
	 * initialisation
	 * 
	 * @throws Exception
	 */
	public function init() {if (count($this->_indexes) != 1) {throw new Exception("Text column needs exactly one index");}}
	
	/**
	 * rendu
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		$key = current($this->_indexes);
		if (!property_exists($row, $key)) {throw new Exception("Index '$key' not found in the row");}
		return htmlspecialchars($row->$key);
	}
}