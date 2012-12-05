<?php 
/**
 * gestion ftp
 * 
 * @author jhouvion
 *
 */
class Sea_Ftp2  {
	
	/**
	 * Pointeur de connexion
	 * 
	 * @var unknown_type
	 */
	protected $_connect;
	
	/**
	 * host de la connexion
	 * 
	 * @var unknown_type
	 */
	protected $_host;
	
	/**
	 * port de la connexion
	 * 
	 * @var unknown_type
	 */
	protected $_port;
	
	
	/**
	 * timeout de la connexion
	 * 
	 * @var unknown_type
	 */
	protected $_timeout;
	
	/**
	 * 
	 * identifiant de la connexion
	 * 
	 * @var unknown_type
	 */
	protected $_login = false;
	
	/**
	 * Mot de passe de la connexion
	 * 
	 * 
	 */
	protected $_password = false;

	/**
 	 * construicteur
 	 * 
 	 * @param unknown_type $host
 	 * @param unknown_type $port
 	 * @param unknown_type $timeout
 	 */
    public function __construct($host, $port = 21, $ssl = false, $timeout = 90) {
    	
    	// attribuition des informations de connexion
    	$this->_host = $host;
    	$this->_port = $port;
    	$this->_timeout = $timeout;
    
    	if(!function_exists('ftp_connect')){throw new Sea_Exception('L\'extension ftp est manquante');}
    
		// on verifie qu'lon peux charger la classe
		if ($ssl) {
	        if (!($this->_connect = @ftp_ssl_connect($host, $port, $timeout))) {throw new Sea_Exception('Impossible de se connecter au serveur : %s', $host );        }
		} else {
	        if (!($this->_connect = @ftp_connect($host, $port, $timeout))) {throw new Sea_Exception('Impossible de se connecter au serveur : %s', $host );        }
		}
    }
    
    /**
     * constructeur avec login / password
     * 
     * @param unknown_type $host
     * @param unknown_type $login
     * @param unknown_type $password
     * @param unknown_type $port
     * @param unknown_type $timeout
     * @throws Sea_Exception
     * @return Sea_Ftp2
     */
    static function connectWithCredential($host, $login, $password, $port = 21, $ssl = false, $timeout = 90) {
        
        $c = __CLASS__;// recuperation nom de la classe
        $instance  = new $c($host, $port, $timeout);// creation de l'objet
        
        // attribuition des informations de connexion
        $instance->setLogin($login);
        $instance->setPassword($password);

        // authentification
        if (!$instance->login($login, $password)) {throw new Sea_Exception('Erreur d\'authentification pour l\'utilisateur : %s', $login);}
        
        // on retourne l'objet
        return $instance;
    }
    
    /**
	 * @param unknown_type $_login
	 */
	public function setLogin($_login) {
		$this->_login = $_login;
		return $this;
	}

	/**
	 * @param boolean $_password
	 */
	public function setPassword($_password) {
		$this->_password = $_password;
		return $this;
	}

	/**
     * upload d'un arobrescence (repertoire ou fichier)
     * 
     * @param unknown_type 
     */
    public function upload($queue, $resume = false) {
        
        $pwd = $this->pwd();// on recupere le repertoire de depart
        
        // on force le format de l'upload
       	$queue  = (array) $queue;

        // on parcours tout les fichier
       	foreach($queue as $dir) {
       	    
       	    // Cas du repertoire
	        if (is_dir($dir)) {
	
	            $directory = basename($dir);// recuperation du nom de repertoire
	            
	            // si le dossier n'existe pas on tente de le créer
				if (!array_search($directory, $this->nlist())) {
					if (!$this->mkdir($directory)) {throw new Sea_Exception('Impossible de créer le repertoire %s dans %s', $directory, $pwd);}
	            }
	            
	         	// on tente de naviguer vers le repertoire
	            if (!@$this->chdir($directory)) {throw new Sea_Exception('le repertoire existe pas : %s', $directory);}
	            
	            // on recupere les fichier contenu dans le repertoire
	         	$iterator = new DirectoryIterator($dir);
	         	$child = array();
			    foreach ($iterator as $fileinfo) {if ($fileinfo->isDot()){continue;}$child[] = $fileinfo->getPathname();}
			   
			    // si le repertoire est vide, on zappe
			    if (empty($child)) {continue;}
			    
			    // on lance l'upload
			    $this->upload($child);
	            
	        // cas du fichier   
	        } else if (is_file($dir)) {
	        	$basename = basename($dir);// recuperation du nom du fichier
	        	
	        	// si on veux finir d'uploader le fichier (resume)
	        	if($resume && $this->nlist($basename)) {
	        		
	        		// recuperation de la taille du fichier distant
        			if(!($size = $this->size($basename))){throw new Sea_Exception('Impossible de récuperer la taille du fichier : % ', $basename);}
        			$localsize = filesize($dir);// recuperation de la taille du fichier local
        			
        			// si la taille du fichier source est inferieur on supprime la destination
        			if ($localsize < $size) {$this->delete($basename); $size = 0;}
        			elseif ($localsize != $size) {
	        			$file = fopen($dir, 'r');// ouverture du fichier source
	        			fseek($file, $size);// on deplace le pointeur 
	        			//on continue l'upload du fichier
	        			if(!$this->fput($basename, $file, FTP_BINARY, $size)){throw new Sea_Exception('Erreur sur l\'upload du fichier (resume) : %s', $basename);}
        			}
	        	
        		// sinon on tente l'upload du fichier par ecrasement
	        	} else {if (!$this->put($basename, $dir, FTP_BINARY)) {throw new Sea_Exception('Erreur sur l\'upload du fichier : %s', $basename);}}

	        // en cas d'erreur
	        } else {throw new Sea_Exception('la ressource %s n\'est ni un repertoire ni un fichier');}
	        
	       	// on remonte dans le dossier initial
	        if (!@$this->chdir($pwd)) {throw new Sea_Exception('Impossible de revenir dans le repertoire initial : %s', $pwd);}
       	}
    }
    
    /**
     * suppression recursive
     * 
     * 
     */
    public function delete($queue) {
    	
    	$pwd = $this->pwd();// on recupere le repertoire de depart
        
        // on force le format de l'upload
       	$queue  = (array) $queue;

        // on parcours tout les fichier
       	foreach($queue as $dir) {
       		
       		// si le fichier n'est pas dans le repertoire courant, on se se deplace
       		if (($start = strrpos($dir, '/')) > 0) {
       			
       			// identifiation du repertoire
       			$directory = substr($dir, 0, $start);
       			$dir = substr($dir, $start + 1);
       			
       			// on tente le deplacement
       			if (!@$this->chdir($directory)) {throw new Sea_Exception('Impossible d\'aller dans le repertoire: %s', $directory);}
       		}
       		
       		// recuperation de la liste du contenu
       		$list = $this->nlist($dir);
       		
       		// si c'est un dossier non le supprime car il devrais etre vide
       		if (current($list) == '.') {
       			// suppresion recursive
	       		foreach($list as $file) {if ($file == '.' || $file == '..') {continue;}$this->delete($dir . DIRECTORY_SEPARATOR . $file);}
       			if (!$this->rmdir($dir)){throw new Sea_Exception('Impossible de supprimer le repertoire : %s', $dir);}
       		
       		// si fichier, on l'efface
       		} else {ftp_delete($this->getResource(), $dir);}
       		
       		// on remonte dans le dossier initial
	        if (!@$this->chdir($pwd)) {throw new Sea_Exception('Impossible de revenir dans le repertoire initial : %s', $pwd);}
       	}
    }
    
    /**
     * destruction de l'objet
     * 
     */
    public function __destruct() {
        @$this->close();
    }

    /**
     * surcharge de la fonction de liste d'un repertoire
     * 
     * @param unknown_type $directory
     */
    public function nlist($directory = '.') {
        return ftp_nlist($this->getResource(), $directory);
    }
    
    /**
     * renvoie si une connexion est active
     * 
     * @return boolean
     */
    public function isActive() {
    	return is_resource($this->_connect);
    }
    
    /**
     * renvoie la connexion acrtive
     * 
     * @throws Sea_Exception
     * @return resource
     */
    public function getResource() {
    	
     	// on verifie qu'une connexion est active
        if(!$this->isActive()) {throw new Sea_Exception('Aucune connexion active');}
        
        return $this->_connect;
    }

    /**
     * utilisation de toute les fonction _ftp 
     * 
     * @param unknown_type $function
     * @param unknown_type $arguments
     * @return mixed
     */
    public function __call($function, $arguments) {
        
        // Prepend the ftp resource to the arguments array
        array_unshift($arguments, $this->getResource());
        
        // on verifie qu'une connexion est active
        if(!$this->isActive()) {throw new Sea_Exception('Aucune connexion active');}
        
        if (!function_exists($function = 'ftp_' . $function)) {throw new Sea_Exception('la fonction %s n\'existe pas', $function);}
        
        // Call the PHP function
        return call_user_func_array($function, $arguments);
    }
}