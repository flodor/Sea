<?php
/**
 * 
 * gestion de cache simplifié utilisant zend_core
 * 
 * @author jhouvion
 * @link http://akrabat.com/php/notes-on-zend_core/
 */

require_once 'Zend/Cache.php';

class Sea_Cache {
	
    /**
     * @var boolean
     */
    static protected $_enabled = true;
    
    /**
     * instance du singleton
     * 
     * @var Sea_Cache
     */
    static protected $_instances = array();
    
    /**
     * nom du cache
     * 
     * @var String
     */
    protected $_name;
    
    protected $_backend = 'File';
    
	protected $_frontend = 'Core' ;
	
	protected $_frontendOptions = array();
	
	protected $_backendOptions = array();

	protected $_customFrontendNaming = false;

	protected $_customBackendNaming = false;

	protected $_autoload = false;
	
    /**
     * @var Zend_core_Core
     */
    static protected $_core;
    
    /**
     * contient le contenu du cache
     * 
     * @var mixed
     */
    protected $_cache = false;
    
    /**
     * constructeur
     * 
     */
    protected function __construct() {
    	
    	$this->init();
    	
        if(self::$_enabled) {
            self::$_core = Zend_Cache::factory(	$this->getFrontend(), 
            									$this->getBackend(), 
            									$this->getFrontendOptions(), 
            									$this->getBackendOptions(), 
            									$this->getCustomFrontendNaming(), 
            									$this->getCustomBackendNaming(),
            									$this->getAutoload());
        }
    }
    
    /**
     * configuration de l'objet
     * 
     */
    protected function init() {
    	
    	// recuperation de la configuration
    	$cache_dir = APPLICATION_PATH . "/../tmp/cache";
		
    	$_frontendOptions =  array('lifetime' => null, 'automatic_serialization' => true);
    	$this->setFrontendOptions($_frontendOptions);
		$_backendOptions = array( 'cache_dir' => realpath($cache_dir));
		$this->setBackendOptions($_backendOptions);
    }
    
    
    /**
     * function qui doit être surchargé
     * 
     */
    protected function process() { return 'no process action defined';}
    
    /**
     * coinstrcuteur du singleton
     * 
     * @return Sea_Cache
     */
    static function getInstance() {
        
      	$class = get_called_class();

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }
    
    public function load() {
        if(self::$_enabled === false) {return false;}
        return self::$_core->load($this->getName());
    }
    
    public function save($dataToStore) {
        if(self::$_enabled == false) {return true;}
        return self::$_core->save($dataToStore, $this->getName());
    }

    
    public function clean() {
        if(self::$_enabled == false) {return;}    
        self::$_core->clean();   
    }
    
	/**
	 * @return the $_enabled
	 */
	static public function getEnabled() {
		return self::$_enabled;
	}

	/**
	 * @return the $_backend
	 */
	public function getBackend() {
		return $this->_backend;
	}

	/**
	 * @return the $_frontend
	 */
	public function getFrontend() {
		return $this->_frontend;
	}

	/**
	 * @return the $_frontendOptions
	 */
	public function getFrontendOptions() {
		return $this->_frontendOptions;
	}

	/**
	 * @return the $_backendOptions
	 */
	public function getBackendOptions() {
		return $this->_backendOptions;
	}

	/**
	 * @return the $_customFrontendNaming
	 */
	public function getCustomFrontendNaming() {
		return $this->_customFrontendNaming;
	}

	/**
	 * @return the $_customBackendNaming
	 */
	public function getCustomBackendNaming() {
		return $this->_customBackendNaming;
	}

	/**
	 * @return the $_autoload
	 */
	public function getAutoload() {
		return $this->_autoload;
	}

	/**
	 * @return the $_core
	 */
	public static function getCore() {
		return self::$_core;
	}

	/**
	 * @param boolean $_enabled
	 */
	static public function setEnabled($_enabled) {
		self::$_enabled = $_enabled;
	}

	/**
	 * @param field_type $_backend
	 */
	public function setBackend($_backend) {
		$this->_backend = $_backend;
	}

	/**
	 * @param field_type $_frontend
	 */
	public function setFrontend($_frontend) {
		$this->_frontend = $_frontend;
	}

	/**
	 * @param field_type $_frontendOptions
	 */
	public function setFrontendOptions($_frontendOptions) {
		$this->_frontendOptions = $_frontendOptions;
	}

	/**
	 * @param field_type $_backendOptions
	 */
	public function setBackendOptions($_backendOptions) {
		$this->_backendOptions = $_backendOptions;
	}

	/**
	 * @param field_type $_customFrontendNaming
	 */
	public function setCustomFrontendNaming($_customFrontendNaming) {
		$this->_customFrontendNaming = $_customFrontendNaming;
	}

	/**
	 * @param field_type $_customBackendNaming
	 */
	public function setCustomBackendNaming($_customBackendNaming) {
		$this->_customBackendNaming = $_customBackendNaming;
	}

	/**
	 * @param field_type $_autoload
	 */
	public function setAutoload($_autoload) {
		$this->_autoload = $_autoload;
	}

	/**
	 * @param Zend_core_Core $_core
	 */
	public static function setCore($_core) {
		static::$_core = $_core;
	}
	/**
	 * @return the $_name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param field_type $_name
	 */
	public function setName($_name) {
		$this->_name = $_name;
	}
	
	/**
	 * @return the $_cache
	 */
	public function getCache() {
		
		$cache = $this->_cache;

		if ($cache === false) {
			if(!$cache = $this->load()) {
				$cache = $this->process();
				$this->setCache($cache);
			} 	
		}
		
		return $cache;
	}

	/**
	 * @param mixed $_cache
	 */
	public function setCache($_cache) {
		$this->save($_cache);
		$this->_cache = $_cache;
	}
}
