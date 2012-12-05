<?php 
/**
 * Gestion des fichier plat
 * 
 * @author julien.houvion@businessdecision.com
 */


require_once 'Sea/File.php';
require_once 'Sea/File/Flat/Line.php';

class Sea_File_Flat extends Sea_File {
	
	protected $_line = 'Sea_File_Flat_Line';
	
	/**
	 * Création d'une nouvelle ligne
	 * 
	 * @return Sea_File_Flat_Line
	 */
	function line() {return new $this->_line();}
	
	/**
	 * Ajoute le contenu d'une ligne au ccontenu de l'objet courant
	 * 
	 * 
	 * @param Sea_File_Flat_Line $l
	 * @param Boolean $eol$
	 * @return self
	 */
	public function add(Sea_File_Flat_Line $l) {
		
		if (!$l->isEmpty()) {
			$this->content .= $l->render();
			$this->newline();
		}
		
		return $this;
	}
	
	/**
	 * insere un retour chariot
	 * 
	 */
	public function newline() {
		$this->content .= PHP_EOL;
		return $this;
	}
}


