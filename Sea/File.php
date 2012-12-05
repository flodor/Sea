<?php
/** 
 * 
 * classe de gestion des fchier externe a l'application
 * 
 * @author jhouvion
 * @since 17/02/2010
 * 
 * @author pierre-yves.aillet@businessdecision.com
 * @version 05/03/2010
 * @desc Ajout du paramétre forceDownload à la méthode download ( #97 )
 * 
 */
class Sea_File {
	
	/**
	 * Contenu du fichier
	 * 
	 * @var String
	 */
	var $content;
	
	/**
	 *  Tableau contenant les information sur le fichier de destination
	 * 
	 * @var String
	 */
	protected $_destination = array(	'mime' => '',
										'dirname' => '',
										'basename' => '',
										'extension' => '',
										'filename' => '',
										'size' => '');

	
	/**
	 * Tableau contenant les information sur le fichier chargé
	 * 
	 * @var unknown_type
	 */
	protected $_source = array(	'mime' => '',
								'dirname' => '',
								'basename' => '',
								'extension' => '',
								'filename' => '',
								'size' => '');
	
	/**
	 * Constructeur
	 * 
	 * @param $config
	 */
	function __construct($config = NULL) {
		
		// on charge la configuration si elle existe
		if ($config) {
			// si fichier source			
			if (is_string($config) && $filename = realpath($config)) {$this->read($filename);}
			
			// si fichier de config
			else if ($config instanceof Zend_Config) {$this->loadConfig($config);}	
			else {
				require_once 'Sea/File/Exception.php';
				throw new Sea_File_Exception('Erreur sur les argument passés au constructeur', E_USER_ERROR);
			}
		}
	}
	
	/**
	 * Charge une configuration
	 * 
	 * @param Zend_Config $config
	 * @return Sea_File
	 */
	public function loadConfig(Zend_Config $config) {
		
		//TODO
		return $this;
	}
	
	/**
	 * Setter pour le contenu du fichier
	 * 
	 * @param unknown_type $s
	 * @return Sea_File
	 */
	public function setContent($s) {
		$this->content = $s;
		return $this;
	}
	
	/**
	 * on charge les infromations du fichier
	 * 
	 * @param String_type $s Chemin du fichier
	 * @param String $destination source || destination
	 */
	protected function _load($s, $destination) {
		
		// verification et formatage de la destionation
		if ($destination != 'source' && $destination != 'destination') {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Le paramètre de destination n\est pas correct', E_USER_ERROR);
		} else {$destination = '_'.$destination;}
		
		// on verifie que le fichier n'est pas vide
		$filesize = filesize($s);
		if (empty($filesize)) {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Le fichier à charger :"'.$s.'" est vide', E_USER_ERROR);
		}
		
		// chargement du mime du fichier
		eval('$this->'.$destination.'["mime"] = mime_content_type($s);');
		eval('$this->'.$destination.'["size"] = $filesize;');
		
		// recuperation des information sur le chemin du fichier
		$this->$destination = array_merge((array)$this->$destination, (array) pathinfo($s));
	}
		
	/**
	 * Charge le contenu du fichier
	 * 
	 * @param String $s chemin du fichier
	 * @return Sea_File
	 */
	public function read($s) {
		
		// récuperation du chemin absolue
		$s = realpath($s);
		
		// on verifie que le fichier existe et que l'on a les droit de lecture dessus
		if (!file_exists($s) || !is_readable($s)) {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Le fichier source : "'.$s.'" n\'existe pas ou ne peux étre lu', E_USER_ERROR);
		}
		
		//charge les information de la source
		$this->_load($s, 'source');
		
		// chargement du contenu du fichier
		$handle = fopen($s, "rb");
		$contents = fread($handle, $this->_source['size']);
		fclose($handle);
		
		// chargement du contenu du fichier
		$this->setContent($contents);
		
		return $this;
	}
	
	/**
	 * 
	 * Ecrit le fichier
	 * 
	 * @param String $s chemin du fichier
	 * @return Sea_File
	 */
	public function write($s) {
		
		if (!(strlen($this->content) > 0)) {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Aucun contenu n\est associé a l\'ecriture', E_USER_ERROR);
		}
		
		// Ecriture du fichier
		try {
			file_put_contents($s, $this->content);
			$this->_load($s, 'destination');
		} catch (Exception $e) {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Ecriture impossible dans le fichier : "'.$s.'"', E_USER_ERROR);
		}
		
		return $this;
	}
	
	protected function _echo($forceDownload = true, $name = "") {
		
		// verifie que l'on a un contenu
		if (empty($this->content)) {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Aucun contenu n\'est spécifié', E_USER_ERROR);
		}
		
		//nom du fichier
		$contentDisposition = ($name == "") ? $this->_source['basename'] : $name;
		
		// inscription du mime
		if ($forceDownload) {
			$contentType = !empty($this->_source['mime']) ? $this->_source['mime'] : 'application/octet-stream';
			header("Content-Type: $contentType");
		    header("Content-disposition: attachment; filename=\"".$contentDisposition."\"");
		    header('Content-Transfer-Encoding: binary');
		} else { 
		   $contentType = !empty($this->_source['mime']) ? $this->_source['mime'] : 'text/html';
		   header("Content-Type: $contentType ");
		   header("Content-disposition: inline; filename=\"".$contentDisposition."\"");
		}
		
		header("Content-length: ".strlen($this->content));// on inscrit la taille du fichier
		header("Cache-control: private");// compatibilité ie
        header('Pragma: cache');// compatibilité ie
		header("Expires: 0");// on supprime le cache
		
		flush();// on vide le cache
		
		// affichage du contenu du fichier
		echo $this->content;
		
		// on coupe le script
		exit;
	}
	
	/**
	 * Telecharge le fichier 
	 * (pas de chemin de fichier si contenu virtuel)
	 * 
	 * @param String $s
	 */
	public function download($name = "") {
	    $this->_echo(true, $name);
	}
	
	/**
	 * Affiche le fichier
	 * (pas de chemin de fichier si contenu virtuel)
	 * 
	 * @param String $s
	 */
	public function show( $name = "") {
	    $this->_echo( false, $name);
	}
	
	/**
	 * Deplace un fichier avec une lecture ecriture
	 * 
	 * @param String $source
	 * @param String $destination
	 * @return Zend_File
	 */
	public function move($source, $destination) {
		
		$source = realpath($source);
		
		if (file_exists($source) && is_writable($source)) {
		
			$this->read($source);// lecture
			$this->write($destination);// ecriture
			unlink($source);
		} else {
			require_once 'Sea/File/Exception.php';
			throw new Sea_File_Exception('Le fichier "'.$source.'" n\'existe pas ou ne peux être détruit', E_USER_ERROR);
		}
		
		return $this;
	}
	
	/**
	 * Retourne l'url de téléchargement d'un fichier
	 * cette methode devra être surcharger pour les besoin specifiques
	 * 
	 * @param string $s chemin du fichier à télécharger
	 * @return string url à appeler pour télécharger le fichier
	 * 
	 */
	public function url($s) {
		return $s;
	}
}
