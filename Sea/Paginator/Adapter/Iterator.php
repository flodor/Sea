<?php

/**
 * class de gestion de la pagination des colection 
 * 
 * @author Sylvain Cahot
 * @since 19/01/2009
 */

class Sea_Paginator_Adapter_Iterator extends Zend_Paginator_Adapter_Iterator {
	
	public function toArray($m) {
        return $m;
	}
}