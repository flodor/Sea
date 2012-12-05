<?php

/***
 * test CB 
 * 
 * 
 * Numéro de carte
 * : 1111222233334444
 * Date de fin de validité
 * : 0308 (mars 2008) par exemple.
 * CVV ou cryptogramme visuel : 123
 * 
 */

/**
 * Effectue un paiement avec le module CGI de chez paybox
 * 
 * 
 * 
 * @author jhouvion
 *
 */
class Sea_Payment_PayboxCgi {
	
	/**
	 * contient les configuration pour un baonnement
	 * 
	 * 
	 * @var unknown_type
	 */
	protected $_subscription = array();
	
	/**
	 * si la paiment possède une subscription
	 * 
	 * @var Booléan
	 */
	protected $_subscriptionActive = false;
	
	/**
	 * Chemi du CGI a executé
	 * 
	 * 
	 * @var String
	 */
	protected $_URI;
	
	/**
	 * Mode de récupération des informations. de 1 à 4 
	 * chiffres parmi les valeurs 1,2,3 ou 4. 
	 * on force a 4 pour le CGI
	 * 
	 * @var 1à4 chiffres. 
	 */
	protected $_PBX_MODE = 4;// on force a CGI
	
	
	/**
	 * Numéro de site (TPE) donné par la banque
	 * 
	 * @var 7 chiffres
	 */
	protected $_PBX_SITE;
	
	/**
	 * Numéro de rang (« machine ») donné par la banque 
	 * 
	 * @var unknown_type
	 */
	protected $_PBX_RANG;
	
	
	/**
	 * Montant total de l‟achat en centimes sans virgule ni point.
	 * 
	 * @var 3 à 10  chiffres.
	 */
	protected $_PBX_TOTAL;
	
	
	/**
	 * Code monnaie de la transaction suivant la norme ISO 4217 (code numérique) :
	 * 
	 * « 978 » pour l‟euro.
	 * « 840 » pour le dollar US.
	 * « 952 » pour le CFA.
	 * 
	 * @var 3 Chiffres
	 */
	protected $_PBX_DEVISE;
	
	
	/**
	 * Votre référence commande.
	 *
	 * @var 1 à 250  caractères 
	 */
	protected $_PBX_CMD;
	
	
	/**
	 * Adresse email de l‟acheteur (porteur de carte).
	 * 
	 * @var 6 à 80 caractères 
	 */
	protected $_PBX_PORTEUR;
	
	
	/**
	 * Variables renvoyées par Paybox (montant, référence 
	 * commande, numéro de transaction, numéro 
	 * d‟abonnement et numéro d‟autorisation)
	 * 
	 * @var 3 à 150 caractères 
	 */
	protected $_PBX_RETOUR;
	
	
	/**
	 * Identifiant PAYBOX fourni par PAYBOX SERVICES  
	 * au moment de votre inscription. 
	 * 
	 * @var 1à9 chiffres 
	 */
	protected  $_PBX_IDENTIFIANT;
	
	
	/**
	 * Reste des élément de configuration (non-obligatoire)
	 * 
	 * @var Array
	 */
	protected $_PBX_CONFIG = array();
	
	
	/**
	 * Constructeur
	 * 
	 * @param String uri || Array config || Zend_Config
	 */
	public function __construct($config = array()) {
		
		// si la config est uen string, c'est obligatoirement une uri
		if (is_string($config)) {$this->setUri($config);}
		
		// chargement de la config
		else {$this->_config($config);}
		
		// charge l'initialisation
		$this->init();
	}
	
	/**
	 * fonction d'initialisation de l'objet
	 * 
	 * Utilisé si objet étendu
	 * 
	 */
	public function init() {;}
	
	
	/**
	 * chargement de la configuration
	 * 
	 * @param Array || Zend_Config $config
	 */
	protected function _config($config) {
		
	 	if ($config instanceof Zend_Config) { $config = $config->toArray(); }

        if (!is_array($config)) {
            require_once 'Zend/Exception.php';
            throw new Zend_Exception('Invalid argument: $config must be an array or an instance of Zend_Config');
        }
		
        foreach ($config as $key => $value) {
        	// inscription dans l'element dans la configuration
        	if (property_exists($this, '_'.$key)) {eval ('$this->_' . $key . ' = ' .var_export($value, true) .';');}
        	else {$this->addConfig($key, $value);}
        }
	}
	
	/**
	 * Renvoie les paramètre a transmettre au CGI
	 * 
	 * @return String
	 */
	protected function _getQuery() {
		
		// initlisation du resultat
		$query = "";
		
		// récupération de la config
		$attributes = $this->getConfig();
		
		
		// charegment des paramètre obligatoire
		foreach ($this as $key => $value) {if (substr($key, 1, 4) == 'PBX_' && $key != "_PBX_CONFIG") {$attributes[substr($key, 1)] = $value;}}
		
		// génération de la requete
		foreach($attributes as $key => $value) {$query .= ' ' . $key . '=' .$value;}
		
		return $query;
	}
	
	/**
	 * appelle le cgi pour le paiement
	 * 
	 */
	public function proceed() {
		
		// gestion des abonnements
		if ($this->getSubscriptionActive()) {$this->setCmd($this->getCmd() . $this->_renderSubscription());}
		
		// lance le cgi de paiement
		ob_clean();// on vide le buffer
		return shell_exec($this->getUri() . $this->_getQuery());
	}
	
	/**
	 * Construction de mla chaine de création d'abonnement
	 * 
	 */
	protected function _renderSubscription() {
		
		// intialisation du resultat
		$sub = "";
		
		// champs obligatoire pour l'abonnement
		$need = array(	'IBS_2MONT' => 10 ,'IBS_NBPAIE' => 2 ,'IBS_FREQ' => 2 ,'IBS_QUAND' => 2 , 'IBS_DELAIS' => 3);
		
		// constructyion de la chaine
		foreach ($need as $key => $size) {
			$value = isset($this->_subscription[$key]) ?  $this->_subscription[$key] : false ;
			//on verfie que le paramètre existe bien
			if ($value === false) { throw new Zend_Exception('Le paramètre suivant est manquant : '. $key);}

			// formatage de la valeur en focntiond e la taille attendu
			while (strlen($value) < $size) {$value = '0' .$value;}
			$sub .= $key . $value;
		}
		
		return $sub;
	}
	
	/**
	 * ajoute un abonnement
	 * 
	 * @param $IBS_2MONT
	 * @param $IBS_NBPAIE
	 * @param $IBS_FREQ
	 * @param $IBS_QUAND
	 * @param $IBS_DELAIS
	 */
	public function setSubscription($IBS_2MONT, $IBS_NBPAIE ,$IBS_FREQ ,$IBS_QUAND, $IBS_DELAIS) {
		
		// active l'abonnement
		$this->setSubscriptionActive(true);
		
		// insere les paramètre de l'abonnement
		$this->_subscription = array(	'IBS_2MONT' => $IBS_2MONT,
										'IBS_NBPAIE' => $IBS_NBPAIE,
										'IBS_FREQ' => $IBS_FREQ,
										'IBS_QUAND' => $IBS_QUAND,
										'IBS_DELAIS' => $IBS_DELAIS);
		return $this;
	}
	
	/**
	 * renvoie un attribu de la config
	 * 
	 * @param String $key 
	 */
	public function getConfigValue($key){
		return array_key_exists($key, $this->_PBX_CONFIG) ? $this->_PBX_CONFIG[$key] : false;
	}
	
	
	/**
	 * Ajoute un élément a la configuration
	 * E
	 * @param $key
	 * @param $value
	 */
	/**
	 * @return the $_uri
	 */
	/**
	 * @return the $_subscription
	 */
	public function getSubscription() {
		return $this->_subscription;
	}

	/**
	 * @return the $_subscriptionActive
	 */
	public function getSubscriptionActive() {
		return $this->_subscriptionActive;
		
	}

	/**
	 * @param Booléan $_subscriptionActive
	 */
	public function setSubscriptionActive($_subscriptionActive) {
		$this->_subscriptionActive = $_subscriptionActive;
		return $this;
	}

	public function getUri() {
		return $this->_URI;
	}

	/**
	 * @param String $_uri
	 */
	public function setUri($_uri) {
		$this->_URI = $_uri;
		return $this;
	}

	
	/**
	 * Ajoute une valeur dans la conguration
	 * 
	 * @param String $key
	 * @param String $value
	 */
	public function addConfig($key, $value) {
		$this->_PBX_CONFIG[$key] = $value;
		return $this;
	}
	
	/**
	 * suppresion d'un élément de configurationh
	 * 
	 * 
	 * @param String $key
	 */
	public function removeConfig($key) {
		if(array_key_exists($key, $this->getConfig())) {unset($this->_PBX_CONFIG[$key]);}
		return $this;
	}
	
	/**
	 * efface la configuration
	 *
	 */
	public function clearConfig() {
		$this->setConfig(array());
		return $this;
	}
	
	/**
	 * @return the $_PBX_MODE
	 */
	public function getMode() {
		return $this->_PBX_MODE;
	}

	/**
	 * @return the $_PBX_SITE
	 */
	public function getSite() {
		return $this->_PBX_SITE;
	}

	/**
	 * @return the $_PBX_RANG
	 */
	public function getRang() {
		return $this->_PBX_RANG;
	}

	/**
	 * @return the $_PBX_TOTAL
	 */
	public function getTotal() {
		return $this->_PBX_TOTAL;
	}

	/**
	 * @return the $_PBX_DEVISE
	 */
	public function getDevise() {
		return $this->_PBX_DEVISE;
	}

	/**
	 * @return the $_PBX_CMD
	 */
	public function getCmd() {
		return $this->_PBX_CMD;
	}

	/**
	 * @return the $_PBX_PORTEUR
	 */
	public function getPorteur() {
		return $this->_PBX_PORTEUR;
	}

	/**
	 * @return the $_PBX_RETOUR
	 */
	public function getRetour() {
		return $this->_PBX_RETOUR;
	}

	/**
	 * @return the $_PBX_IDENTIFIANT
	 */
	public function getIdentifiant() {
		return $this->_PBX_IDENTIFIANT;
	}

	/**
	 * @return the $_PBX_CONFIG
	 */
	public function getConfig() {
		return $this->_PBX_CONFIG;
	}

	/**
	 * @param 1à4 $_PBX_MODE
	 */
	/*
	public function setMode($_PBX_MODE) {
		$this->_PBX_MODE = $_PBX_MODE;
	}
	*/
	
	/**
	 * @param 7 $_PBX_SITE
	 */
	public function setSite($_PBX_SITE) {
		$this->_PBX_SITE = $_PBX_SITE;
		return $this;
	}

	/**
	 * @param unknown_type $_PBX_RANG
	 */
	public function setRang($_PBX_RANG) {
		$this->_PBX_RANG = $_PBX_RANG;
		return $this;
	}

	/**
	 * @param 3 $_PBX_TOTAL
	 */
	public function setTotal($_PBX_TOTAL) {
		$this->_PBX_TOTAL = $_PBX_TOTAL;
		return $this;
	}

	/**
	 * @param 3 $_PBX_DEVISE
	 */
	public function setDevise($_PBX_DEVISE) {
		$this->_PBX_DEVISE = $_PBX_DEVISE;
		return $this;
	}

	/**
	 * @param 1 $_PBX_CMD
	 */
	public function setCmd($_PBX_CMD) {
		$this->_PBX_CMD = $_PBX_CMD;
		return $this;
	}

	/**
	 * @param 6 $_PBX_PORTEUR
	 */
	public function setPorteur($_PBX_PORTEUR) {
		$this->_PBX_PORTEUR = $_PBX_PORTEUR;
		return $this;
	}

	/**
	 * @param 3 $_PBX_RETOUR
	 */
	public function setRetour($_PBX_RETOUR) {
		$this->_PBX_RETOUR = $_PBX_RETOUR;
		return $this;
	}

	/**
	 * @param 1à9 $_PBX_IDENTIFIANT
	 */
	public function setIdentifiant($_PBX_IDENTIFIANT) {
		$this->_PBX_IDENTIFIANT = $_PBX_IDENTIFIANT;
		return $this;
	}

	/**
	 * @param Array $_PBX_CONFIG
	 */
	public function setConfig($_PBX_CONFIG) {
		$this->_PBX_CONFIG = $_PBX_CONFIG;
		return $this;
	}
}