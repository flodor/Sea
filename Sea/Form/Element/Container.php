<?php

require_once ('Zend/Form/Element.php');

class Sea_Form_Element_Container extends Zend_Form_Element {
	
	/**
	 * sufix pour le nom du contanier
	 * 
	 * @var String
	 */
	protected $_suffix = '_container'; 
	

/**
     * tableau contenant les element submit a affiché
     *
     * @var unknown_type
     */
    protected $_elements;

    /**
     * Ajoute un element a la pie d'éléments a afficher
     *
     * @param Zend_Form_Element_Submit $o
     * @return Sea_Form_Element_Container
     */
	public function addElement(Zend_Form_Element $o) {
		$this->_elements[$o->getName()] = $o;
		return $this;
	}

	 /**
     * chargement des decorateurs par default
     * 
     * (non-PHPdoc)
     * @see Zend_Form_Element::loadDefaultDecorators()
     */
    public function loadDefaultDecorators() {

		/* on ajoute un emplacement e decorateurs à l'élément */
		$this->addPrefixPath('Sea_Form_Decorator', 'Sea/Form/Decorator', 'decorator');

		$this->setDecorators(array(	array('Multiple'),
						            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-input')),
								    array('SeaLabel'),
								    array(array('div' => 'HtmlTag'), array('tag' => 'tr'))));
	}

	/**
	 * recupration des elements
	 *
	 * @return unknown
	 */
	public function getElements() {
		return $this->_elements;
	}
	
	/**
	 * renvoie un element
	 * 
	 * @param String $name
	 * @return Zend_Form_Element
	 */
	public function getElement($name) {
		
		if (array_key_exists($name, $this->_elements)) {return $this->_elements[$name];}
		return null;
	}

	/**
	 * rendu
	 *
	 * @param Zend_View_Interface $view
	 * @return unknown
	 */
	public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $key => $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}

?>