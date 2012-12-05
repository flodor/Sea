<?php

require_once ('Zend/View/Helper/Abstract.php');

class Sea_View_Helper_HtmlAttrib extends Zend_View_Helper_Abstract {
	
	/**
	 * rendu d'atribut html
	 * @param unknown_type $attribs
	 * @return string
	 */
	public function htmlAttrib($attribs) {
		
		$xhtml = '';
		foreach ($attribs as $key => $val) {if (strpos($val, '"') !== false) {$xhtml .= " $key='$val'";} else {$xhtml .= " $key=\"$val\"";}}
		return $xhtml;
	}

}

?>