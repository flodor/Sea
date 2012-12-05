<?php

require_once ('Zend/CodeGenerator/Php/Class.php');

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_CodeGenerator_Php_DatabaseClass extends Zend_CodeGenerator_Php_Class {
	
	/**
	 * chemin du fichier de class
	 * 
	 * @var unknown_type
	 */
	protected $_outClassPath = 'Database/';
	
	/**
	 * nom de la classe etendu
	 * 
	 * @var unknown_type
	 */
	protected $_extendedClass = 'Zend_Db_Table';
	
	/**
	 * Nom de la table
	 * 
	 * @var unknown_type
	 */
	protected $_tableName = array(	'name'         => '_name',
	            					'visibility'   => 'protected',
	            					'defaultValue' => '');
	
	/**
	 *  cle étrangere de la table
	 *  
	 * @var unknown_type
	 */
	protected $_referenceMap = array(	'name'         => '_referenceMap',
            							'visibility'   => 'protected',
            							'defaultValue' => '');
	
	/**
	 * constructeur
	 * 
	 * @param $tableName
	 */
	function __construct($tableName, $referenceMap) {
		parent::__construct();
		
		// formatage du nom de la classe
		$this->setName(self::formatName($tableName, $this->getOutClassPath()));
		
		//formatage des informations
		$this	->setTableName($tableName)
				->setReferenceMap($referenceMap);

		// attribution des propriété
		$this->setProperties(array($this->_tableName, $this->_referenceMap));
	}
	
	/**
	 * renvoie e nom d'un objet pour qu'il soit comptibla avec Zend
	 * 
	 * @param $s
	 */
	static function formatName($name, $path = '' ) {
		$return = preg_replace('/\//', '_', $path);// construction du path
		foreach(preg_split('/_/', $name) as $s) {$return .= ucfirst($s);} // construction du nom de la class
		return $return;
	}
	
	/**
	 * @return the $_outClassPath
	 */
	public function getOutClassPath() {
		return $this->_outClassPath;
	}
	
	/**
	 * @param $_outClassPath the $_outClassPath to set
	 */
	public function setOutClassPath($_outClassPath) {
		$this->_outClassPath = $_outClassPath;
		return $this;
	}


	/**
	 * @param $_name the $_name to set
	 */
	protected function setTableName($_name) {
		$this->_tableName['defaultValue'] = $_name;
		return $this;
	}

	/**
	 * @param $_referenceMap the $_referenceMap to set
	 */
	protected function setReferenceMap($rows) {
		//formatage des table de reference
		$_referenceMap = array();
		foreach ($rows as $row) {$_referenceMap[self::formatName($row['refTableClass'], $this->getOutClassPath())] = $row;}
		$this->_referenceMap['defaultValue'] = $_referenceMap;
		return $this;
	}
	
	
	/**
	 * Genere le chemin de sortie du fichier
	 * 
	 * @return String
	 */
	public function generateOutputDirectory() {
		
		$out = '';//initialisation de la sortie
		$fc = Zend_Controller_Front::getInstance();// recuperation du controller proincipale
		$out = $fc->getModuleDirectory().'/'.preg_replace('/_/', '/', $this->getName()).'.php';// generation du chemin du fichier
		return $out;
	}
	
	
	/**
	 * ecriture du fichier
	 * 
	 * @param unknown_type $filePath
	 */
	public function write($filePath = false) {
		
		// recuperation du fichier de sortie
		$filePath = !$filePath ? $this->generateOutputDirectory() : $filePath;
		
		try {
			$generator = new Zend_CodeGenerator_Php_File();// réation du fichier
			$generator->setBody($this->generate());// ecriture de la classe dans le fichier
			file_put_contents($filePath, $generator->generate());// ecriture du fichier
			$bReturn = true;
		} catch(Exception $e) {$bReturn = false;}
		
		return $bReturn;
	}
}

?>