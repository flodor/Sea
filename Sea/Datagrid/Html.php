<?php

require_once 'Sea/Datagrid/Abstract.php';
require_once 'Sea/Datagrid/Html/Element.php';
require_once 'Sea/Form.php';

class Sea_Datagrid_Html extends Sea_Datagrid_Abstract {
	
	/**
	 * defini le type de datagrid voulue
	 * 
	 * @var unknown_type
	 */
	const TYPE = __CLASS__;
	
	/**
	 * Definition des tag a definir pour le sdifferent elements
	 * 
	 * @var unknown_type
	 */
	public static $TAGS = array(
	    '_table'		=> 'table',
	    '_head'			=> 'thead',
	    '_headerRow'	=> 'tr',
	    '_header'		=> 'th',
	    '_body'			=> 'tbody',
	    '_row'			=> 'tr',
	    '_cell'			=> 'td',
	    '_foot'			=> 'tfoot',
		'_footerRow'	=> 'tr',
		'_footer'		=> 'td',
	);

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_table;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_head;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_headerRow;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_header;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_body;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_row;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_cell;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_foot;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_footerRow;

	/**
	 * @var Sea_Datagrid_Html_TemplateElement
	 */
	protected $_footer;
	
	/**
	 * identifiant de container general
	 * 
	 * @var unknown_type
	 */
	protected $_id;

	/**
	 * Contenu en cas de resultat vide
	 * 
	 * @var unknown_type
	 */
	protected $_noResults = "<div>Aucun résultat.</div>";
	
	/**
	 * argument passé au constructeur
	 * 
	 * @var unknown_type
	 */
	protected $_args = array();
	
	/**
	 * @var Bool
	 */
	protected $_hasHeader = true;
	
	/**
	 * @var Bool
	 */
	protected $_hasFooter = true;
	
	/**
	 * initialisation premiere de l'objet
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Abstract::_init()
	 */
	protected function _init() {
		require_once 'Sea/Datagrid/Html/TemplateElement.php';
		foreach (self::$TAGS as $element	=> $tag) {$this->$element = new Sea_Datagrid_Html_TemplateElement($tag);}
	}
	
	/**
	 * surcharge du constructeur
	 * 
	 */
	public function __construct() {
		
		$this->_args = func_get_args();// recuperationd es argument
		$this->_init();// preinitialisation
		
		// on intialise
		call_user_func_array(array($this, 'init'), $this->_args);

		// on verfie qu'il y a bien un adapter
		if (!$this->getAdapter()) {throw new Sea_Exception('Impossible de determiner l\'adaptateur de donnée');}
	}
	
	/**
	 * ajoute une coloration automatique de la ligne en fonction de tableau de mapping
	 * 
	 * @param unknown_type $field
	 * @param unknown_type $map
	 */
	public function addRowColor($field, $map) {
		$this->_row->addCallback(function ($o, $row) use ($field, $map) {
									$php = '';
									foreach ($map as $k => $v ) {$php .= 'case \'' .$k. '\': $row->css(\'background\' , \'' . $v . '\') ;break;';}
									$php = 'switch ($row->data->$field) {'. $php . ';}';
									eval($php);
								});
	}


	/**
	 * effectue le rendu de l'objet
	 * 
	 * (non-PHPdoc)
	 * @see Sea_Datagrid_Abstract::render()
	 */
	public function render(Zend_View_Interface $view = NULL) {
		
		// initilisation du contenu
		$content = "";
		
		// si aucune colonne est defini, on les definie automatiquement
		if (count($this->getColumns()) == 0) {$this->autogenerate();}
		
		$this->getTable()->addClass('datagrid');// on met la class datagrod par default
		
		//recuperation l'id de la table
		$id = $this->getId();
		
		// génération des contenu
		$content .= $this->_generateHeader();
		$content .= $this->_generateBody();
		$content .= $this->_generateFooter();
		
		$tag = new Sea_Decorator_HtmlTag('div');
		$tag->setId($id);
		
		// Table
		return $tag->render($this->_table->render($content));
	}
	
	/**
	 * Génération du header
	 * 
	 */
	protected function _generateHeader() {
		
		// initialisation du contenu
		$content = '';
		
		// Génération des header
		if ($this->_hasHeader) {
			
			// initilisation du contenu du header
			$headerContent = "";
			
			// traitement poûr chacune des colonnes
			$filter = '';// filtre
			$renderFilter = false;
			foreach($this->_columns as $column) {
				
				// construction de l'element
				$header = Sea_Datagrid_Html_Element::factory($this->_header, $column);
				$header	->attrib($column->getHeader()->attrib())
						->setClass($column->getHeader()->getClass())
						->css($column->getHeader()->css());

				// on lance tout les callback
				$this->_header->runCallbacks($this, $header);
				$column->getHeader()->runCallbacks($this, $header);
				
				// on renvoie le rendu
				$content .= $header->render($column->getLabel());
				
				if ($f = $column->getStrainer()) {$renderFilter = true;}
				$filter .= $header->render($f);
			}
			
			//traiteemnt de la ligne complete
			$headerRow = Sea_Datagrid_Html_Element::factory($this->_headerRow);
			$this->_headerRow->runCallbacks($this, $headerRow);//on lance les callback
			
			$content = $headerRow->render($content);// on effectue le rendu
			if ($renderFilter) {$content = $headerRow->render($filter) . $content;}// si il y a un filtre, on effectue le rendu

			// traitement du header complet
			$head = Sea_Datagrid_Html_Element::factory($this->_head);
			$this->_head->runCallbacks($this, $head);//on lance les callback
			$content = $head->render($content);// on effectue le rendu
			
			// on libere de l'espace memoire
			unset($headerContent);
		}
		return $content;
	}
	
	
	/**
	 * Renvoie les donnée par colonne sou forme de tableau
	 * 
	 * @return array 
	 */
	public function toArray() {
		
		$result = array();
		
		// traitement pour chacune des ligne
		foreach($this as $row) {
		
			// création de la ligne
			$row = Sea_Datagrid_Html_Element::factory($this->_row, (object) $row);

			$data = array();
			
			//traietemnt pour chacune des colonne
			foreach($this->_columns as $column) {
				
				// création de l'element
				$cell = Sea_Datagrid_Html_Element::factory($this->_cell, array('row' => $row, 'column' => $column));
				$cell	->attrib($column->getCell()->attrib())
						->setClass($column->getCell()->getClass())
						->css($column->getCell()->css());
				
				// on lance les callback
				$this->_cell->runCallbacks($this, $cell);
				$column->getCell()->runCallbacks($this, $cell);
				
				// ajoute le rendu
				$data[] = $column->render($row->data, $this->getView());
			}
			
			$this->_row->runCallbacks($this, $row);// on lance les callback pour la ligne complete
			$result[] = $data;

			// on libere de l'espace memoire
			unset($row);
		}
		
		return $result;
	}
	
	/**
	 * genere le contenu centrale
	 * 
	 */
	protected function _generateBody() {
		
		// initialisation du contenu
		$content = "";
		
		// traitement pour chacune des ligne
		foreach($this as $row) {
		    
			// création de la ligne
			$row = Sea_Datagrid_Html_Element::factory($this->_row, (object) $row);

			$data = "";
			
			//traietemnt pour chacune des colonne
			foreach($this->_columns as $column) {
				
				// création de l'element
				$cell = Sea_Datagrid_Html_Element::factory($this->_cell, array('row' => $row, 'column' => $column));
				$cell	->attrib($column->getCell()->attrib())
						->setClass($column->getCell()->getClass())
						->css($column->getCell()->css());

				// on lance les callback
				$this->_cell->runCallbacks($this, $cell);
				$column->getCell()->runCallbacks($this, $cell);
				
				// ajoute le rendu
				$data .= $cell->render($column->render($row->data, $this->getView()));
			}
			
			$this->_row->runCallbacks($this, $row);// on lance les callback pour la ligne complete
			$content .= $row->render($data);// on ajoute le rednu au contenu

			// on libere de l'espace memoire
			unset($row);
		}
			
		// on libere de l'espace memoire
		unset($data);
		
		//génération du body
		$body = Sea_Datagrid_Html_Element::factory($this->_body);
		$this->_body->runCallbacks($this, $body);// on lance les callback
		
		return $body->render($content);// on ajoute le rendu au contenu
	}
	
	/**
	 * genereation du footer
	 * 
	 */
	protected function _generateFooter() {
		
		// initialisation du contenu
		$content = "";
		
		// Génération du footer
		if (($this->count() == 0 && !empty($this->_noResults)) || ($this->_hasFooter && $this->_pageCount > 1)) {
			
			// calcul du colspan
			$this->_footer->attrib('colspan', count($this->getColumns()));
			
			// S'il n'y a aucun résultat et qu'un rendu html est demandé On affiche le html de noResults dans le footer.
			if ($this->count() == 0 && !empty($this->_noResults)) {$content = $this->_noResults;}	
		
			$this->_footer->runCallbacks($this);// on lance les callback
			$content = $this->_footer->render($content);// on ajoute le rendu au contenu
			
			$this->_footerRow->runCallbacks($this);// on lance les callback
			$content = $this->_footerRow->render($content);// on ajoute le rendu au contenu
			
			$this->_foot->runCallbacks($this);// on lance les callback
			$content = $this->_foot->render($content);// on ajoute le rendu au contenu
		}
		
		return $content;
	}
	
	public function enableHeader() {
		$this->_hasHeader = True;
		return $this;
	}
	
	public function disableHeader() {
		$this->_hasHeader = False;
		return $this;
	}
	
	public function hasHeader() {
		return $this->_hasHeader;
	}
	
	public function enableFooter() {
		$this->_hasFooter = True;
		return $this;
	}
	
	public function disableFooter() {
		$this->_hasFooter = False;
		return $this;
	}
	
	public function hasFooter() {
		return $this->_hasFooter;
	}

	public function getNoResults() {
		return $this->_noResults;
	}
	
	public function setNoResults($noResults) {
		$this->_noResults = $noResults;
		return $this;
	}
	
	public function getTable() {
		return $this->_table;
	}
	
	public function getHead() {
		return $this->_head;
	}
	
	public function getHeaderRow() {
		return $this->_headerRow;
	}
	
	public function getHeader() {
		return $this->_header;
	}
	
	public function getBody() {
		return $this->_body;
	}
	
	public function getRow() {
		return $this->_row;
	}
	
	public function getCell() {
		return $this->_cell;
	}
	
	public function getFoot() {
		return $this->_foot;
	}
	
	public function getFooterRow() {
		return $this->_footerRow;
	}
	
	public function getFooter() {
		return $this->_footer;
	}
	
	public function getArgs() {
		return $this->_args;
	}
	
	public function getArg($key) {
		return $this->_args[$key];
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		
		if (empty($this->_id)) {$this->_id = '__datagrid_' . mt_rand();}
		
		return $this->_id;
	}

	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}
}