<?php

require_once 'Sea/Decorator/HtmlTag.php';

/**
 * 
 * Elements Html pour Sea_Datagrid
 * @author tibor
 *
 */
class Sea_Datagrid_Html_Element extends Sea_Decorator_HtmlTag {
	
	static function factory(Sea_Datagrid_Html_TemplateElement $el, $data = null) {
		
		$class = new self($el->getTag());
		if (is_array($data)) {foreach($data as $key => $value) {$class->$key = (object) $value;}} 
		else {$class->data = (object) $data;}
		
		return $class;
	}
	
}