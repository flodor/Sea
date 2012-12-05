<?php

/**
 * Contrôleur de données d'entrée et de sortie pour Sea_Replace
 * Il faut s'en servir comme un itérateur.
 * 
 * @author Tibor Vass
 *
 */
abstract class Sea_Replace_Data_Abstract extends ArrayIterator {
	
	/**
	 * Lit la chaîne de caractères courante à remplacer
	 * 
	 */
	public function read() {
		
	}

	/**
	 * Sauvegarde les substitutions effectuées
	 * 
	 */
	public function write($modified) {
		
	}
}
