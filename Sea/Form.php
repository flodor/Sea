<?php
/**
 * Creation du formaulire pour les évènement
 * @author Julien Houvion
 * @since 14/06/2009
 */


class Sea_Form extends Zend_Form
{
	/**
	 * prefix pour le element a rajouter
	 * 
	 * @var String
	 */
	static public $ELEMENT_TYPE = 'Sea_Form_Element_';
	
	/**
	 * Bouton d'action du fomulaire
	 * 
	 * @var unknown_type
	 */
	protected $_submit;
	
	/**
	 * Taille de la partie label du formulaire(gauche)
	 */
	protected $_labelWidth = '30%';

	/**
	 * surclass du constucteur
	 *
	 * @param unknown_type $option
	 */
	function __construct($option = null) {
		
		// ajout des decorateur de sea
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');
		
		$this->_init();// initialisation developpeur pour eviter les surcharge du constructeur
		
	    // rfécuperere les information passé en paramètre
		$args = func_get_args();
		
		//on apelle la function init avec les paramètres passé au constructeur
		call_user_func_array(array($this, 'init'), $args);
		
		// on charge les decorateurs par default
		$this->loadDefaultDecorators();
	}

	/**
	 * Premier niveau d'initialisation
	 * 
	 */
	protected function _init() {;}
	
	
	/**
	 * @return the $_labelWidth
	 */
	public function getLabelWidth() {
		return $this->_labelWidth;
	}

	/**
	 * @param string $_labelWidth
	 */
	public function setLabelWidth($_labelWidth) {
		$this->_labelWidth = $_labelWidth;
		return $this;
	}

	/**
	 * chagement des decorateur par default
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Form::loadDefaultDecorators()
	 */
	public function loadDefaultDecorators() {

	    // on ajoute les decorateur
        $this->addDecorators(array(	array('FormElements'),
        							array(array('body' =>'HtmlTag'), array('tag' => 'table', 'class' => 'ui-widget-content ui-state-highlight form-table')),
        							array('Submit', array('class' => 'formSubmit')),
        							array('form', array('class' => 'form has-validation', 'novalidate' => 'novalidate')),
        							array('SeaLegend'),
        							array(array('container' => 'HtmlTag'), array('tag' => 'div', 'class' => 'ui-widget'))));
        
        return $this;
	}
	
	/**
	 * surcharge du rendu pour ajouter des modification
	 * (non-PHPdoc)
	 * @see Zend_Form::render()
	 */
	public function render(Zend_View_Interface $view = null) {
		
		// ge stion de la taille du label
		foreach($this->getElements() as $e) {if ($d = $e->getDecorator('SeaLabel')) {$d->setOption('width', $this->getLabelWidth());}}
		return parent::render($view);
	}
	
	/**
	 * ajoute un element au formulaire
	 * @see parent::addElement()
	 * 
	 * @return Zend_Form_Element
	 */
	public function addElement($element, $name = null, $options = null) {

		if (!($element instanceof Zend_Form_Element)) {
		    
			// On récupère les arguments sans le premier, et on insère l'argument id en 2e position
			$args = func_get_args();
			
			// on verifie que le type existe
			$type = self::$ELEMENT_TYPE . $element;
			
			if (!class_exists($type)) {
				$filename =  str_replace('_', '/', $type ) . '.php';// nom du fichier
				if (!file_exists($filename) || !is_readable($filename)) {throw new Sea_Exception('Impossible de charger le fichier : %s', $filename);}
				require_once $filename;// chargement du fichier
				if (!class_exists($type)) {throw new Sea_Exception('IMpossible de charger le classe %s', $type);}
			}
			
			unset($args[0]);//suppression du premier paramètre
			
			// création de l'élement
			$reflection = new ReflectionClass($type);
			$element = $reflection->newInstanceArgs($args);
		}
		
		parent::addElement($element);// ajout de l'element
		
		return $element;
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
         
       	return call_user_func_array(array($this, 'addElement'), $arguments);
    }
	
	/**
	 * ajoute un bouton de validation de formulaire
	 * 
	 * @param String $id
	 * @param String $value
	 * @param Array $attribs
	 */
	public function addSubmit($id, $value = false, $attribs = []) {
		
		// créartion de l'element
		require_once 'Sea/Form/Element/Submit.php';
		$element = new Sea_Form_Element_Submit($id, $value);
		$element->setAttrib('type', empty($value) ? 'button' : 'submit');
		
		// gestiond es attribuit
		if (is_array($attribs)) {if (!empty($attribs['type'])) { $element->setAttrib('type', $attribs['type']); unset($attribs['type']);}}
		
		$element->setOptions($attribs);
		$element->setDecorators(array(array('ViewHelper')));
		
		// attribution de l'element a la pile 
		$this->_submit[] = $element;
		return $element;
	}
	
	/**
	 * efface les éléments et les  bouton de soumission
	 * 
	 */
	public function clearElements() {
		parent::clearElements();
		$this->_submit = array();
	}
	
	/**
	 * renvoie l'element definie par le name, 
	 * prend en compte les container
	 * 
	 * @see Zend_Form::getElement()
	 */
	public function getElement($name) {

	    if (array_key_exists($name, $this->_elements)) {return $this->_elements[$name];}
		
		foreach ($this->_elements as $element) {
			if ($element instanceof Sea_Form_Element_Container) {
				if (array_key_exists($name, $element->getElements())) {return $element->getElement($name);}
			}
		}
        return null;
	}
	
	/**
	 * @return the $_submit
	 */
	public function getSubmit() {
		return $this->_submit;
	}

	/**
	 * @param unknown_type $_submit
	 */
	public function setSubmit($_submit) {
		$this->_submit = $_submit;
		return $this;
	}
}

