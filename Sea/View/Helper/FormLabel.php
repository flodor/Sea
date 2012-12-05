<?php
/**
 * constrcution d'un label
 * 
 * @author jhouvion
 *
 */
class Sea_View_Helper_FormLabel extends Zend_View_Helper_FormElement {
	
	public function formLabel($name, $value = null, $attribs = null) {

		$info = $this->_getInfo ( $name, $value, $attribs );
		extract ( $info ); // name, value, attribs, options, listsep, disable
	
		$xhtml  = '<span '. $this->_htmlAttribs ( $attribs ) .' >' . $this->view->escape ( $value ) . '</span>';
		
		return $xhtml;
	}
}
