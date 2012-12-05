<?php

/**
 * class de gesrion de la pagination des colection 
 * 
 * @author Julien Houvion
 * @since 16/06/2009
 */

class Sea_Paginator_Adapter_DbSelect extends Zend_Paginator_Adapter_DbSelect {
	
	function toArray($m) {
		
		return $m;
	}
}