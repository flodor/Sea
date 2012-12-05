<?php
/**
 * Class d'exploration et de gestion de fichiers
 *
 * @author Julien Houvion
 * @since 07/07/2009
 *
 */
require_once 'Sea/Explorer/DirectoryIterator.php';

class Sea_Explorer {

	/**
	 * repertoire racine
	 *
	 * @var unknown_type
	 */
	protected $_root;

	/**
	 * constructeur
	 *
	 * @param unknown_type $root
	 */
	function __construct($root = "") {
		$this->setRootDirectory($root);
	}

	/**
	 * setter du repertoire racine
	 *
	 * @param unknown_type $s
	 * @return unknown
	 */
	public function  setRootDirectory($s) {
		$this->_root = is_dir($s) ? $s : false;
		return $this;
	}


	/**
	 * getter pour le repertoire racine
	 *
	 * @return unknown
	 */
	public function getRootDirectory() {
		return $this->_root;
	}
	
	/**
	 * list les repertoire
	 * 
	 * @param unknown_type $bRecursive
	 * @param unknown_type $root
	 */
	public function listDirectories($bRecursive = false, $root = null) {
		
		$directories = array();
		$root = is_null($root) ? $this->getRootDirectory() : $root;
		$search = new Sea_Explorer_DirectoryIterator($root);

		/* récuperation des fichier du repertoire cournant */
		$findDir = $search->getDirectories();
		foreach ((array) $findDir as $d){if (!empty($d))$directories [] = $root.'/'.$d;}

		/* recursivité */
		if ($bRecursive) {
			foreach ((array) $search->getDirectories() as $d) {
				$findDir = $this->listDirectories(true, $root.'/'.$d);
				foreach ($findDir as $d){if (!empty($d))$directories [] = $d;}
			}
		}

		return $directories;
	}

	/**
	 * renvoie une liste de tout les fichiers contenue dns le dossir de recherche
	 *
	 * @param unknown_type $bRecursive
	 * @param unknown_type $root
	 * @return unknown
	 */
	public function listFiles($bRecursive = false, $filter = false,  $root = null) {

		$files = array();
		$root = is_null($root) ? $this->getRootDirectory() : $root;
		$search = new Sea_Explorer_DirectoryIterator($root);

		/* récuperation des fichier du repertoire cournant */
		$findFiles = $search->getFiles($filter);
		foreach ((array) $findFiles as $f){if (!empty($f))$files [] = $root.'/'.$f;}

		/* recursivité */
		if ($bRecursive) {
			foreach ((array) $search->getDirectories() as $d) {
				$findFiles = $this->listFiles(true, $filter, $root.'/'.$d);
				foreach ($findFiles as $f){if (!empty($f))$files [] = $f;}
			}
		}

		return $files;
	}

}