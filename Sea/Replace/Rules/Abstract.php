<?php

/**
 * Chargeur de règles pour Sea_Replace
 * Il faut s'en servir comme un itérateur.
 *   
 * @author Tibor Vass
 *
 */
abstract class Sea_Replace_Rules_Abstract extends ArrayIterator {
	
	/**
	 * Charge les règles.
	 * Méthode à surcharger dans les classes étendues
	 * 
	 */
	protected function _load() {
		
	}
	
}
