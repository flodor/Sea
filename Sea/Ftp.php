<?php 

class Sea_Ftp  {
	
	/**
	 * Pointeur de connexion
	 * 
	 * @var unknown_type
	 */
	protected $_connect;

	/**
	 * constructeur
	 * 
	 * @param unknown_type $host
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	public function __construct($host, $username = '', $password = '', $port = '21') {
		
		// connexion au site
		if (!@$this->_connect = ftp_connect($host, $port, 90)) {
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Connexion impossible au serveur : ' . $host);	
		}
		
		// identification 
		if (!empty($username)) {
			if (!@ftp_login($this->_connect, $username, $password)) {
				require_once 'Zend/Exception.php';
				throw new Zend_Exception('Mauvaise identification pour le serveur : ' . $host .  ' et l\'utilisateur : ' . $username);	
			}
		}
		
	}
	
	/**
	 * Telechargement d'un fichier
	 * 
	 */
	public function download($source, $destination = '') {
	
		// on met le nom du fichier si la destionation est un repertoire
		if (substr($destination, -1, 1) == '/' || empty($destination)) {$destination .= array_pop(explode('/', $source));}
		
		// on verifie l'acces au fichier
		if (!$handle = fopen($destination, 'w')) {
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Impossible de créer ou d\'acceder au fichier  ' . $destination);	
		}
		
		// Tente de téléchargement le fichier $remote_file et de le sauvegarder dans $handle
		if (!ftp_fget($this->_connect, $handle, $source, FTP_BINARY)) {
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Erreur lors du telechargement du fichier  ' . $source);		
		}
		
		return true;
	}
	
	
	/**
	 * creation recursive de dossier
	 * 
	 * @param unknown_type $path
	 * @param unknown_type $mode
	 */
	public function rmkdir($path, $mode = 0777 ) {
		
		$dir= preg_split("/\//", $path);
		$path="";
	
	    for ($i=0;$i<count($dir);$i++) {
	        
	    	$path.="/".$dir[$i];
	        if(!@ftp_chdir($this->_connect,$path)) {
	        	
	            @ftp_chdir($this->_connect,"/");
	            if(!@ftp_mkdir($this->_connect,$path)) {return false;} 
	            else {@ftp_chmod($this->_connect, $mode, $path);}
			}
		}
		
		 @ftp_chdir($this->_connect,"/");
		 
	    return true;
	}
	
	
	
	
	/**
	 * upload d'un fichier
	 * 
	 * @param unknown_type $source
	 * @param unknown_type $destination
	 * @throws Zend_Exception
	 */
	public function upload($source, $destination = '') {
		
                // on met le nom du fichier si la destionation est un repertoire
                if (substr($destination, -1, 1) == '/' || empty($destination)) {$destination .= array_pop(explode('/', $source));}

                // creation de la base et deplacement dedans
                if($base_destination = substr($destination,  0, strrpos($destination, '/'))) {
                        if(!$this->rmkdir($base_destination)) {
                                require_once 'Zend/Exception.php';
                                throw new Zend_Exception('Impossible d\'acceder et ou de créer le dossier  ' . $destination);
                        }

                        // on se deplace dans le fichier
                        ftp_chdir($this->_connect, $base_destination);
                }

                // liste les fichier a uploader
                require_once 'Sea/Explorer.php';

                // recuperation de la base de la source
                $base_source = dirname($source);

                $explorer = new Sea_Explorer();
                if (is_dir($source)) {

                        // ons e place a la racine du telechargement
                        $explorer->setRootDirectory($source);

                        // reation de le liste des elemnt a uploader
                        $files = array();
                        foreach($explorer->listFiles(true) as $file) {

                                if (array_search(dirname($file), $files) === false) {$files[] = dirname($file);}
                                $files[] = $file;
                        }
                }
                elseif (is_file($source)) {$files[] = $source;}
                else {throw new Exception('La source n\'est pas valide');}

                // on upload
                foreach($files as $file) {

                        $dest = str_replace($base_source, $base_destination, $file);

                        if (is_dir($file)) {
                                if(!$this->rmkdir($dest)) {
                                        require_once 'Zend/Exception.php';
                                        throw new Zend_Exception('Impossible d\'acceder et ou de créer le dossier  ' . $destination);
                                }

                                // on se deplace dans le fichier
                                ftp_chdir($this->_connect, $dest);
                        } else {

                                if (!file_exists($file)) {
                                        require_once 'Zend/Exception.php';
                                        throw new Zend_Exception('Le fichier n\'existe pas : ' . $file);
                                }

                                if(!$handle = fopen($file, 'r')) {
                                        require_once 'Zend/Exception.php';
                                        throw new Zend_Exception('Impossible d\'acceder au fichier  ' . $file);
                                }

                                // definition de la position initial
                                $position = str_replace($base_source, $base_destination, dirname($file));
                                $position = empty($position) ? '/' : $position;

                                if (ftp_pwd($this->_connect) != $position) {
                                        require_once 'Zend/Exception.php';
                                        throw new Zend_Exception('Pointeur mal placé.');
                                }

                                // Upload un fichier
                                if (!ftp_fput($this->_connect, basename($file), $handle, FTP_BINARY)) {
                                        require_once 'Zend/Exception.php';die;
                                        throw new Zend_Exception('Erreur lors de l\'upload du fichier  ' . $file);
                                }
                        }
                }
                                        
	}
}