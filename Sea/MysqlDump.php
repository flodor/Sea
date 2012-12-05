<?php

require_once ('Zend/Db/Adapter/Mysqli.php');

/**
 * Classe de gestion de mysqldump
 * Seuleemnt pour system linux
 * 
 * @author jhouvion
 *
 */
class Sea_MysqlDump extends Zend_Db_Adapter_Mysqli {
	
	/**
	 * compression par default
	 */
	protected $_compress = true;
	
	/**
	 * Defini si lors du process, on renvoie le contenu du fichier généré
	 * 
	 * @var bool
	 */
	protected $_download = false;
	
	/**
	 * repertoir dans le quel sera placé le dump
	 * 
	 * @var unknown_type
	 */
	protected $_directory = '/tmp/';
	
	/**
	 * chemin du fichier de sortie
	 * 
	 * @var String
	 */
	protected $_file;
	
	/**
	 * Nom de l'executable
	 * 
	 * @var String
	 */
	protected $_executable = 'mysqldump';
	
	
	/**
	 * Parametre du mysql dump
	 * 
	 * @var Array
	 */
	protected $_param = array();
	
	/**
	 * surcharge du constrcteur
	 * .
	 * @param unknown_type $config
	 * @param unknown_type $param
	 */
	public function __construct($config, $param = array())  {
		parent::__construct($config);
		
		$this->setParam($param);
	}
	
	/**
	 * generation de la commande mysqldump
	 * 
	 * @return String
	 */
	protected function _generate() {
		
		// recuperation de l'executable
		$cmd = $this->getExecutable();
		
		// gestion des paramètres
		foreach ($this->getParam() as $param => $value) {$cmd .= ' --' . (!is_numeric($param) ? $param  . '=' : '') . $value;}
		
		// connexion
		foreach (array('u ' => 'username', 'p' => 'password', 'dbname') as $prefix => $key) {
			if ($value = array_find($key, $this->getConfig())) {$cmd .= ' ' . (!is_numeric($prefix) ? '-' . $prefix : '')  . $value;}
			else { 
				require_once 'Zend/Exception.php';
				throw new Zend_Exception('Erreur paramètre de connexion : ' . $key);
			}
		}
		
		//construction de l'url du fichier
		if (!strlen($this->getFile())) {  $this->setFile($this->getDirectory() . '/mysqldump_'  . array_find('dbname', $this->getConfig()) . '_' . time() .'.sql' . ($this->getCompress() ? '.bz2' : '')); }
		
		// ajoute la fonction pour la compression
		$cmd .= $this->getCompress() ? ' | bzip2 --stdout --quiet --best ' : '';
		
		// inscirption de l'url
		$cmd .= ' > ' . $this->getFile();

		return $cmd;
	}
	
	/**
	 * lance le processus
	 * 
	 */
	public function dump() {
		
		// Execution de la commande
		if ( !is_null($output = shell_exec($this->_generate()))) {			
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Erreur lors du dump :\n' . $output);
		}
		
		//renvoie le conteznu du fichier ou le fichier		
		return $this->getDownload() ? file_get_contents($this->getFile()) : $this->getFile();
	}
	
	/**
	 * @return the $_param
	 */
	public function getParam() {
		return $this->_param;
	}

	/**
	 * @param Array $_param
	 */
	public function setParam($_param) {
		$this->_param = $_param;
		return $this;
	}
	/**
	 * @return the $_executable
	 */
	public function getExecutable() {
		return $this->_executable;
	}

	/**
	 * @param String $_executable
	 */
	public function setExecutable($_executable) {
		$this->_executable = $_executable;
	}
	/**
	 * @return the $_download
	 */
	public function getDownload() {
		return $this->_download;
	}

	/**
	 * @return the $_file
	 */
	public function getFile() {
		return $this->_file;
	}

	/**
	 * @param bool $_download
	 */
	public function setDownload($_download) {
		$this->_download = $_download;
	}

	/**
	 * @param String $_file
	 */
	public function setFile($_file) {
		$this->_file = $_file;
	}
	/**
	 * @return the $_compress
	 */
	public function getCompress() {
		return $this->_compress;
	}

	/**
	 * @param field_type $_compress
	 */
	public function setCompress($_compress) {
		$this->_compress = $_compress;
	}
	/**
	 * @return the $_directory
	 */
	public function getDirectory() {
		return $this->_directory;
	}

	/**
	 * @param unknown_type $_directory
	 */
	public function setDirectory($_directory) {
		$this->_directory = $_directory;
		return $this;
	}
}

?>