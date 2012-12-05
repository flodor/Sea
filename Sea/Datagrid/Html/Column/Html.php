<?php

class Sea_Datagrid_Html_Column_Html extends Sea_Datagrid_Html_Column_Text {
	
/**
	 * rendu
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		$key = current($this->_indexes);
		if (!property_exists($row, $key)) {throw new Exception("Index '$key' not found in the row");}
		return $row->$key;
	}

}

?>