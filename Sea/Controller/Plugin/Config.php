<?php

/**
 * @package Sea_Controller_Plugin
 * @name /Sea/Controller/Plugin/Config.php
 * @version 1.0
 * @author Pierre-Yves Aillet
 * @since 04/01/2010
 * Ce plugin initialise la requête avec les paramêtres propres au module
 * courant (Default dbAdapter, Layout directory, ...) et met les autres
 * paramêtres en Registry.
 * 
 */
class Sea_Controller_Plugin_Config extends Zend_Controller_Plugin_Abstract
{
    const CONFIG_DIR = "/configs";
    const CONTROLLER_DIR = "/controllers";
    
    private $_moduleName;
    private $_moduleDir;
    private $_moduleConfigDir;
    private $_moduleControllerDir;
    
    /**
     * Initialise notre objet avec les paramêtres de la requête.
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function initObject(Zend_Controller_Request_Abstract $request) {
        $front = Zend_Controller_Front::getInstance();
        
        $this->setRequest($request);
        
        $this->setModuleName($request->getModuleName());
        
        $this->setModuleDir( $front->getModuleDirectory() );
        $this->setModuleConfigDir( $this->getModuleDir() . self::CONFIG_DIR );
        $this->setModuleControllerDir( $this->getModuleDir() . self::CONTROLLER_DIR );
    }
    
    /**
     * On injecte un traitement avant le dispatch de la requête
     * afin de définir les paramêtres propres au module avant que son 
     * contrôleur ne soit appelé.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {     
        $this->initObject($request);
        $dh = scandir( $this->getModuleConfigDir() );
        foreach($dh as $file) {
            strtolower($file);
            $aFile = explode(".", $file);
            if (strcasecmp($aFile[count($aFile) - 1], "ini") == 0) {
                
                unset($aFile[count($aFile) - 1]);
                $file = implode(".", $aFile);
                
                $method_name = $file."Configuration";
                if (method_exists($this, $method_name)) {
                    $this->{$method_name}();
                }
                else {
                    $this->defaultConfiguration($file);
                }
            }
        }
        $this->initAutoLoad();
    }
    
    /**
     * Permet de récupérer la configuration d'un module à partir du nom
     * du fichier sans extension.
     *
     * @param string $file Nom du fichier de configuration à partir du quel 
     * on récupère la configuration (sans extension).
     * @return Zend_Config $config retourne la configuration contenue dans
     * le fichier.
     */
    public function getConfig($file) {        
        try {
            $config = new Zend_Config_Ini(
                $this->getModuleConfigDir() . "/" . $file . ".ini",
                $this->getModuleName() 
            );
        }
        catch(Zend_Config_Exception $e) {
            $config = new Zend_Config_Ini(
                $this->getModuleConfigDir() . "/" . $file . ".ini"
            );
        }
        return $config;
        
    }
    
    /**
     * Initialise l'AutoLoading en fonction des paramétres du module.
     *
     * @return Zend_Application_Module_Autoloader autoloader défini avec les
     * paramêtres du module.
     */
    public function initAutoLoad() {
        /*
        set_include_path(implode(PATH_SEPARATOR, array(
		    get_include_path(),
		    realpath($this->getModuleDir() . '/models'),
		    realpath($this->getModuleDir() )
		)));
		*/
        $autoload = new Zend_Application_Module_Autoloader(array(
            'namespace' => ucfirst($this->getModuleName()).'_',
            'basePath'  => $this->getModuleDir(),
        ));
        $autoload->addResourceType('collection', 'collections', 'Collection');
        // Ajoute la ressource "validator" dans le namespace par defaut de l'application
		$autoload->addResourceType('validator', 'validators', 'Validate');

    }
    
    /**
     * Initialise la configuration de la base de données.
     * A partir du fichier de configuration /configs/db.ini du module
     * on définit automatiquement le dbAdapter par défaut.
     */
    public function dbConfiguration() {
        $config = $this->getConfig("db");
        $dbAdapter = Zend_Db::factory($config->db);
        Zend_Db_Table::setDefaultAdapter($dbAdapter);
        $this->addToRegistry($config->db, "db");
    }

    /**
     * Initialise la configuration du layout.
     * A partir du fichier de configuration /configs/layout.ini du module
     * on définit automatiquement le layoutPath par défaut.
     */    
    public function layoutConfiguration() {
        $config = $this->getConfig("layout");
        Zend_Layout::getMvcInstance()->setLayoutPath(
                $config->layout->layoutPath
        );

        $this->addToRegistry($config, "layout");
    }
    
    public function frameworkConfiguration() {
        $this->addToRegistry( 
            $this->getModuleConfigDir() . "/framework.ini", 
            'collection');
		$this->addToRegistry( 
            $this->getModuleConfigDir() . "/framework.ini", 
            'form');
		$this->addToRegistry( 
            $this->getModuleConfigDir() . "/framework.ini", 
            'date');
    }
    
    /**
     * Traitement par défaut des fichiers de configuration.
     * Lorsque le type de fichier de configuration n'est pas reconnu,
     * on ajoute simplement les informations au registre Zend dans une entrée
     * associée au module ( Zend_Registry::get(<modulename>)[file] ).
     *
     * @param string $file fichier de configuration à traiter
     * (sans chemin et sans extension).
     */
    public function defaultConfiguration($file) {
        $config = $this->getConfig($file);
        $this->addToRegistry($config, $file);
    }
    
    /**
     * Ajoute la configuration d'un fichier au registre Zend dans une entrée
     * associée au module ( Zend_Registry::get(<modulename>)[file] ).
     * 
     * @param Zend_Config $config Objet Zend_Config généré à partir du fichier.
     * @param string $file Nom du fichier d'où est issue la configuration.
     */
    public function addToRegistry($newConfig, $file) {
        if (Zend_Registry::isRegistered('config')) {
            $config = Zend_Registry::get('config');
        }
        if ($config === NULL) {
            $config = new Stdclass();
        }
        
        if (!property_exists($config, $this->getModuleName())) {
            $config->{$this->getModuleName()} = new Stdclass();
        }
        $moduleConfiguration = $mconfig->{$this->getModuleName()};
        if (property_exists($config, $file)) {
            $moduleConfiguration->{$file} = $newConfig->{$file};
        }
        else {
            $moduleConfiguration->{$file} = $newConfig->{$file};
        }
        Zend_Registry::set('config', $config);
    }
    
    /**
     * Setter de moduleControllerDir.
     * Permet de définir le chemin vers les controleurs du module.
     *
     * @param string $configDir Chemin des fichiers controleurs du module.
     */
    public function setModuleControllerDir($controllerDir) {
        $this->_moduleControllerDir = $controllerDir;
    }
    
    /**
     * Getter de moduleControllerDir.
     * Permet de récupérer le chemin vers les fichiers controleurs du module.
     *
     * @return string Chemin des fichiers controleurs du module.
     */
    public function getModuleControllerDir() {
        return $this->_moduleControllerDir;
    }
        
    /**
     * Setter de moduleConfigDir.
     * Permet de définir le chemin vers les fichiers de configuration du 
     * module.
     *
     * @param string $configDir Chemin des fichiers de configuration du module.
     */
    public function setModuleConfigDir($configDir) {
        $this->_moduleConfigDir = $configDir;
    }
    
    /**
     * Getter de moduleConfigDir.
     * Permet de récupérer le chemin vers les fichiers de configuration du 
     * module.
     *
     * @return string Chemin des fichiers de configuration du module.
     */
    public function getModuleConfigDir() {
        return $this->_moduleConfigDir;
    }
    
    /**
     * Setter de moduleDir.
     * Permet de définir le chemin vers les fichiers du module.
     *
     * @param string $dir Chemin du module.
     */
    public function setModuleDir($dir) {
        $this->_moduleDir = $dir;
    }
    
    /**
     * Getter de moduleDir.
     * Permet de récupérer le chemin vers les fichiers du module.
     *
     * @return string Chemin du module.
     */
    public function getModuleDir() {
        return $this->_moduleDir;
    }
    
    /**
     * Setter de moduleName.
     * Permet de définir le nom du module courant.
     *
     * @param string $dir Nom du module courant.
     */
    public function setModuleName($moduleName) {
        $this->_moduleName = $moduleName;
    }
    
    /**
     * Getter de moduleName.
     * Permet de Récupérer le nom du module courant.
     *
     * @return string Nom du module courant.
     */
    public function getModuleName() {
        return $this->_moduleName;
    }
    
    /**
     * Setter de request.
     * Permet de définir la requête courante.
     * 
     * @param Zend_Controller_Request_Abstract $request requête courante.
     */
    public function setRequest(Zend_Controller_Request_Abstract $request) {
        $this->_request = $request;
    }
    
    /**
     * Getter de request.
     * Permet de Récupérer la requête courante.
     *
     * @return Zend_Controller_Request_Abstract la requête courante.
     */
    public function getRequest() {
        return $this->_request;
    }
    
    public function __toString() {
        $ret = "";
        $ret .= $this->getModuleName()."\n";
        $ret .= $this->getModuleDir()."\n";
        $ret .= $this->getModuleConfigDir()."\n";
        $ret .= $this->getModuleControllerDir()."\n";
        return html_entity_decode($ret);
    }
}
