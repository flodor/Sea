<?php
require_once 'Zend/Paginator.php';
require_once 'Sea/Datagrid/Column.php';

/** 
 * !!! ATTENTION : LISEZ-MOI !!!
 * Dans les classes filles qui étendent *directement* Sea_Datagrid_Abstract il faut ajouter :
 * const TYPE = __CLASS__;
 * 
 * Actuellement (21/12/2010) la classe Sea_Datagrid_Html est la seule qui étend directement Sea_Datagrid_Abstract.
 * 
 * @author tibor
 *
 */
class Sea_Datagrid_Abstract extends Zend_Paginator {
	
	/**
	 * les colone de l'objet
	 * 
	 * @var unknown_type
	 */
	protected $_columns = array ();
	
	/**
	 * scrolling style du zend pagination
	 * 
	 * @var unknown_type
	 */
	protected $_scrollingStyle;
	
	/**
	 * Le fichier de vue utiliser pour le rendu du footer
	 * 
	 * @var unknown_type
	 */
	protected $_viewPartial = 'navigation_control.phtml';
	
	/**
	 * paramètre de navigation
	 * 
	 * @var unknown_type
	 */
	protected $_navParams = array ();
	
	/**
	 * Texte "Précédent" pour le navigation control
	 * @var string
	 */
	protected $_textPrevious = 'Préc.';
	
	/**
	 * Texte "Suivant" pour le navigation control
	 * @var string
	 */
	protected $_textNext = 'Suiv.';
	
	/**
	 * création de l'adapter depuis un contenue
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $adapter
	 * @throws Zend_Paginator_Exception
	 * 
	 * @return Zend_Paginator_AdapterAggregate
	 */
	public static function getAdapterFromData($data, $adapter = self::INTERNAL_ADAPTER) {
		
		if ($data instanceof Zend_Paginator_AdapterAggregate) {return $data->getPaginatorAdapter ();} 
		else {
			if ($adapter == self::INTERNAL_ADAPTER) {
				if (is_array ( $data )) {$adapter = 'Array';} 
				else if ($data instanceof Zend_Db_Table_Select) {$adapter = 'DbTableSelect';} 
				else if ($data instanceof Zend_Db_Select) {$adapter = 'DbSelect';} 
				else if ($data instanceof Iterator) {$adapter = 'Iterator';} 
				else if (is_integer ( $data )) {$adapter = 'Null';} 
				else { // on lance une exception
					$type = (is_object ( $data )) ? get_class ( $data ) : gettype ( $data );
					require_once 'Zend/Paginator/Exception.php';
					throw new Zend_Paginator_Exception ( 'No adapter for type ' . $type );
				}
			}
			
			$pluginLoader = self::getAdapterLoader();
			$adapterClassName = $pluginLoader->load ( $adapter );
			return $adapterClassName;
		}
		return null;
	}
	
	/**
	 * preconfiguration de l'objet
	 * 
	 * 
	 */
	protected function _init() {}
	
	/**
	 * initialisation, definition des colonne etc..
	 * 
	 * Si on specifié on passe en data le premier argument
	 * 
	 */
	public function init() {if (func_num_args() == 1) {$this->setAdapter(func_get_arg(0));}}
	
	/**
	 * Si faux, le datagrid contient toutes les données sur la première page
	 * et il n'y aura donc pas de navigation
	 * 
	 * @param boolean $bool
	 */
	public function setPagination($bool) {
		$bool = ( bool ) $bool;
		$count = $bool ? self::getDefaultItemCountPerPage () : - 1;
		$this->setItemCountPerPage ( $count );
	}
	
	/**
	 * Constructor.
	 *
	 * @param Zend_Paginator_Adapter_Interface|Zend_Paginator_AdapterAggregate $adapter
	 */
	public function __construct() {
		$this->_init();// preinitialisation
		
		// on intialise
		call_user_func_array(array($this, 'init'), func_get_args());

		// on verfie qu'il y a bien un adapter
		if (!$this->getAdapter()) {throw new Sea_Exception('Impossible de determiner l\'adaptateur de donnée');}
	}
	
	/**
	 * Creation de l'adapter et mise en place de l'objet paginator
	 * 
	 * @return Zend_Paginator_Adapter_Interface
	 * 
	 */
	public function setAdapter($data) {
		
		// on verfie si le premier argument est exploitable
		if (!($data instanceof Zend_Paginator_Adapter_Interface) && !($data instanceof Zend_Paginator_AdapterAggregate)) {
			$adapter = self::getAdapterFromData($data);
			$data = new $adapter($data);
		}
		
		parent::__construct($data);// sonctruciton de l'objet parent
		return $data;//on renvoie l'adapter
	}
	
	/**
	 * rendu du tableau
	 * 
	 * necessite d'être etendu 
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Paginator::render()
	 */
	public function render(Zend_View_Interface $view = NULL) {
		throw new Exception ( "Render method not implemented" );
	}
	
	/**
	 * transforme un objet
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Paginator::__toString()
	 */
	public function __toString() {
		try {return $this->render ();} 
		catch ( Exception $e ) {echo $e->getMessage ();return "";}
	}
	
	/**
	 * methode magic pour reconnaissance d'ajout de column
	 * 
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $arguments
	 */
	public function __call($name, $arguments) {
        
		// on verifie que c'est bien sou le format add****
        if (!preg_match('/add(?<method>.*)/', $name, $match)) {throw new Sea_Exception('La méthode %s n\'existe pas.', $name);}
        
        // construction des arguments
        array_unshift($arguments, $match['method']);
         
       	return call_user_func_array(array($this, 'addColumn'), $arguments);
    }
	
	/**
	 * Ajoute une colonne au tableau
	 * 
	 * @param mixed $column
	 * @throws Exception
	 */
	public function addColumn($column) {
		
		// On récupère les arguments sans le premier, et on insère l'argument id en 2e position
		$args = func_get_args ();
		$params = array (count ( $this->_columns ) );
		$params [1] = $args [0];
		unset ( $args [0] );
		$params = array_merge ( $params, $args );
		return call_user_func_array ( array ($this, 'setColumn' ), $params );;
	}
	
	/**
	 * Génère automatiquement les colonnes de type Text pour tous les champs 
	 */
	public function autogenerate() {
		if ($this->getTotalItemCount () > 0) {
			$columns = array_keys ( current ( $this->getCurrentItems () ) );
			foreach ( $columns as $column ) {$this->addColumn ( 'Text', $column, $column );}
		}
		return $this;
	}
	
	/**
	 * Retourne la navigation pour la pagination
	 */
	public function renderNavigation() {
		$view =  $this->getView ()->addBasePath ( __DIR__ );
		return $view->paginationControl ( $this, $this->_scrollingStyle, $this->_viewPartial, $this->_navParams );
	}
	
	/**
	 * Getter pour les colonnes
	 * @return array
	 */
	public function getColumns() {
		return $this->_columns;
	}
	
	/**
	 * Setter pour les colonnes
	 * 
	 */
	public function setColumns(array $columns) {
		foreach ( $columns as $column ) {
			if ($column::DATAGRID_CLASS != self::TYPE) {
				unset ( $this->_columns );
				throw new Exception ( "Incompatible column" );
			}
			$this->addColumn ( $column );
		}
	}
	
	/**
	 * creation d'un colonne
	 * 
	 * @param unknown_type $type
	 */
	public function column($type) {
	    
		$column = static::TYPE . '_Column_' . $type;
		$filename = str_replace ( '_', '/', $column ) . '.php';
		
		require_once $filename;
		
		// recuperation des parametre
		$php = array ();
		foreach ( array_slice(func_get_args (), 1) as $value ) {$php [] = var_export ( $value, true );}
		
		// creation de la colum
		eval ( sprintf ( '$c =  new %s(%s);',$column , implode ( ',', $php ) ) );
		
		return $c;
	}
	
	/**
	 * Set la colonne d'id $id par $column
	 * Verifie que la colonne est compatible en comparant DATAGRID_CLASS au type du tableau
	 * 
	 * Si l'id est négatif ou strictement supérieur au dernier id + 1, une autre exception est envoyée.
	 * 
	 * @param mixed $column
	 * @param mixed $id
	 */
	public function setColumn($id, $column) {
		
		// Vérification de la validité de $id
		$id = (int) $id;
		if ($id > count ( $this->_columns ) || $id < 0) {
			throw new Exception ( "Column id must be positive or null and less than the last column's id + 1" );
		}
		
		if (is_a ( $column, 'Sea_Datagrid_Column' )) {
			if ($column::DATAGRID_CLASS == static::TYPE) {$this->_columns [$id] = $column;} 
			else {throw new Exception ( "Incompatible column" );}
		
		// cas de plusieur parametre
		} else {
            // recuperationd es paramètre			
			$args = array_slice(func_get_args (), 1);
			
			// construction de la colonne
			if (!($column = call_user_func_array(array( $this , 'column'), $args))) {
			    throw new Sea_Exception('Impossible de créer la colonne');
			}
			
		    $this->_columns[$id] = $column;// ajout de la colonne a la pile
		}
		
		return $this->_columns[$id];
	}
	
	/**
	 * Effacer toutes les colonnes
	 * 
	 */
	public function clearColumns() {
		$this->_columns = array ();
		return $this;
	}
	
	/**
	 * Retourne la colonne d'id $id
	 * 
	 * @param int $id
	 */
	public function getColumnById($id) {
		return $this->_columns [$id];
	}
	
	/**
	 * Retourne la colonne ayant pour label $label
	 * 
	 * Si le label n'est pas trouvé, false est renvoyé.
	 * Si le meme label existe plusieurs fois, une exception est envoyée!
	 * 
	 * @param $label
	 */
	public function getColumnByLabel($label) {
		return $this->getColumnById($this->getColumnIdByLabel($label));
	}

	/**
	 * Retourne l'id de la colonne ayant pour label $label
	 *
	 * Si le label n'est pas trouvé, false est renvoyé.
	 * Si le meme label existe plusieurs fois, une exception est envoyée!
	 
	 * @param $label
	 */
	public function getColumnIdByLabel($label) {
		$ids = array();
		foreach ($this->_columns as $id => $column) {if ($column->getLabel() == $label) {$ids[] = $id;}}
		if (count($ids) > 1) { throw new Zend_Exception('Label "' . $label . '" exists more than once.'); }
		return current($ids);
	}
	
	/**
	 * Supprime une colonne avec l'id
	 * 
	 * @param int $id
	 * @return bool
	 */
	public function removeColumn($id) {
		foreach ( $this->_columns as $key => $value ) {
			if ($key == $id) {
				unset ( $this->_columns [$key] );
			}
		}
		
		// reordonne les colonne
		sort( $this->_columns);
		
		return $this;
	}
	
	public function getScrollingStyle() {
		return $this->_scrollingStyle;
	}
	
	public function setScrollingStyle($scrollingStyle) {
		$this->_scrollingStyle = ( string ) $scrollingStyle;
		return $this;
	}
	
	public function getViewPartial() {
		return $this->_viewPartial;
	}
	
	public function setViewPartial($viewPartial) {
		$this->_viewPartial = ( string ) $viewPartial;
		return $this;
	}
	
	public function getNavParams() {
		return $this->_navParams;
	}
	
	public function setNavParams(array $navParams) {
		$this->_navParams = $navParams;
		return $this;
	}
	
	public function getTextPrevious() {
		return $this->_textPrevious;
	}
	
	public function setTextPrevious($textPrevious) {
		$this->_textPrevious = $textPrevious;
		return $this;
	}
	
	public function getTextNext() {
		return $this->_textNext;
	}
	
	public function setTextNext($textNext) {
		$this->_textNext = $textNext;
		return $this;
	}
} 
