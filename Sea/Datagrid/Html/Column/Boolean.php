<?php

class Sea_Datagrid_Html_Column_Boolean extends Sea_Datagrid_Html_Column {
	
	/**
	 * rendu
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Html_Column::render()
	 */
	public function render($row, $view = null) {
		
		$key = current($this->_indexes);
		if (!property_exists($row, $key)) {throw new Exception("Index '$key' not found in the row");}
		
		return sprintf('<div %s/>', empty($row->$key) ? '' : 'class="ui-icon ui-icon-circle-check mAuto"');// on encode pour que ca soit du text et pas de l'html
	}

}

?>