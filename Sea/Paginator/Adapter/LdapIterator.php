<?php

/**
 * class de gestion de la pagination des colection 
 * 
 * @author Sylvain Cahot
 * @since 19/01/2009
 */

class Sea_Paginator_Adapter_LdapIterator extends Zend_Paginator_Adapter_Iterator {
	
    public function getIterator() {
        return $this->_iterator;
    }
    
	public function toArray($m) {
		$new = array();
		foreach($m as $key => $value) {
		    $new[$key] = $value[0];
		}
		return $new;
	}
}