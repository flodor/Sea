<?php
/**
 * Explorateur de fichier et repertoire
 *
 * @author Julien Houvion
 * @since 07/07/2009
 *
 */


class Sea_Explorer_DirectoryIterator extends DirectoryIterator{

	/**
	 * repertoire trouvé lors de la recherche
	 *
	 * @var array
	 */
	protected $_directories;

	/**
	 * fichier trouvé lors de la recherche
	 *
	 * @var array
	 */
	protected $_files;


	/**
	 * constructeur
	 *
	 * @param unknown_type $root
	 */
	function __construct($root){
		parent::__construct($root);

		foreach ($this as $info) {

			$name = $info->getFilename();

			if ($info->isDir() && $name != '.' && $name != '..' ){$this->_directories[] = $name;}
			if ($info->isFile()){$this->_files[] = $name;}
		}
	}

	/**
	 * Getter pour le repertoire
	 *
	 * @return unknown
	 */
	public function getDirectories() {
		return $this->_directories;
	}

	/**
	 * getter pour le fichiers
	 *
	 * @return unknown
	 */
	public function getFiles($filter = false) {

		if (!$filter) return $this->_files;

		$file  = array();
		foreach ((array) $this->_files as $f) {
			$xf = explode('.', $f);
			if(count($xf) > 1 && in_array(strtolower(end($xf)), $filter)) {$file[] = $f;}

		}

		return $file;
	}
}