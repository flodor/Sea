<?php

/** 
 * classe servant a donnée les avantage du rendu des view
 * utiliser aggregate($this, 'Sea_View_Aggregate').
 * 
 * @author jhouvion
 * 
 * 
 */
class Sea_View_Aggregate {
	
	/**
	 * Chemin du fichier phtml pour effectuer le rendu
	 * 
	 * @var unknown_type
	 */
	protected $script;
	
	/**
	 * Vue afin de gerer le rendu
	 * 
	 * @var Zend_View
	 */
	protected $view;
	
	/**
	 * Calcul du rendu (contenu)
	 * 
	 */
	protected function render() {
		// on retourne le dernier rendu
		return $this->view->render($this->getScript());
	}
	
   /**
     * Set view object
     *
     * @param  Zend_View_Interface $view
     * @return Zend_Form
     */
    public function setView(Zend_View_Interface $view = null) {
        $this->view = $view;
        return $this;
    }

    /**
     * Retrieve view object
     *
     * If none registered, attempts to pull from ViewRenderer.
     *
     * @return Zend_View_Interface|null
     */
    public function getView() {
        if (null === $this->view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }

        return $this->view;
    }
   
	/**
	 * @return the $_script
	 */
	public function getScript() {
		return $this->script;
	}

	/**
	 * @param unknown_type $_script
	 */
	public function setScript($_script) {
		$this->script = $_script;
		return $this;
	}

}

?>