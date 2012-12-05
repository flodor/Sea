<?php

require_once 'Abstract.php';

/**
 * Contrôleur de données d'entrée et de sortie pour une arborescence de fichiers
 * Le remplacement sera effectué DANS les fichiers lus.
 * 
 * Les fichiers sont lus avec file_get_contents() et sauvegardés avec file_put_contents()
 * 
 * @author Tibor Vass
 *
 */
class Sea_Replace_Data_File extends Sea_Replace_Data_Abstract {
	
	/**
	 * Constructeur qui prend un nom de dossier et une expression rationnelle
	 * pour filtrer le nom des fichiers à parcourir.
	 * 
	 * @param string $directoryPath
	 * @param string $regexFilter
	 */
	public function __construct($directoryPath, $regexFilter = '/.*/') {
		
		// si c'est une liste de fichier
		if (is_array($directoryPath)) {$this->_loadFileFromArray($directoryPath);}
		else {$this->_load($directoryPath, $regexFilter);}
	}
	
	/**
	 * chargement de fichier depuis un tableau
	 * 
	 * @param unknown_type $files
	 */
	protected function _loadFileFromArray($files) {
		
		// on verifie que le fichier existe avant de l'ajouter
		foreach($files as $file) {if ($file = realpath($file)) {$this->append(realpath($file));}}
	}
	
	/**
	 * chargement d'un fichier depuis un repertoire
	 * 
	 * @param unknown_type $directoryPath
	 * @param unknown_type $regexFilter
	 */
	protected function _load($directoryPath, $regexFilter) {
		
		// Vérifier si le répertoire $directoryPath existe
		if (!file_exists($directoryPath)) {
			throw new Sea_Replace_Exception("Cannot access directory $directoryPath", 1);
		}
		
		try {
			$Directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath), RecursiveIteratorIterator::SELF_FIRST);
		} catch (Exception $e) {
			throw new Sea_Replace_Exception("RecursiveDirectoryIterator: " . $e->getMessage(), 2);
		}
		
		
		foreach($Directory as $name => $obj) {
			
			// Filtre les noms de fichiers avec le regexp $regexFilter
			$preg = preg_match($regexFilter, $name);
			if ($preg === FALSE) {throw new Sea_Replace_Exception("preg_match returned with the error code: " . strval(preg_last_error()), 3);}
			if (!$obj->isFile() || $preg == 0) {continue;}
			$this->append(realpath($name));
		}
	}
	
	public function read() {
		if (!is_readable($this->current())) {
			throw new Sea_Replace_Exception("Cannot read file $this->current()", 5);
		}
		$result = file_get_contents($this->current());
		return $result;
	}
	
	public function write($modified) {
		if (!is_writable($this->current())) {
			throw new Sea_Replace_Exception("Cannot write into file ". $this->current(), 6);
		}
		
		// inscription dans le fichier si modification
		if (file_get_contents($this->current()) != $modified) {file_put_contents($this->current(), $modified);}
	}
}
