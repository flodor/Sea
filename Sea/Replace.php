<?php

require_once 'Replace/Exception.php';
require_once 'Replace/Rule.php';
require_once 'Replace/Rules/Excel.php';
require_once 'Replace/Rules/Single.php';
require_once 'Replace/Data/File.php';
require_once 'Replace/Data/String.php';

/**
 * 
 * Sea_Replace permet d'effectuer des remplacements automatiques en tenant compte de règles.
 * (règle = expression régulière + une chaîne de caractère de remplacement)
 * Ces règles peuvent être chargées à partir d'un ou de plusieurs loaders et appliquées dans un adapter.
 * 
 * @author Tibor VASS
 *
 */
class Sea_Replace extends AppendIterator {
	
	/**
	 * Constructeur
	 *  
	 */
	public function __construct() {
		parent::__construct();
		$this->init();
	}

	/**
	 * Initialisation, méthode à surcharger,
	 * dans laquelle on peut charger plusieurs loaders avec load()
	 * et lancer un remplacement automatique dans un adapter avec replace()
	 * 
	 */
	public function init() {
		
	}
	
	/**
	 * Permet d'ajouter une règle de substitution "à la main".
	 * 
	 * @param string $regex
	 * @param string $subst
	 * @param string $option
	 * @param boolean $escape
	 * @param string $begin
	 * @param string $end
	 * 
	 */
	public function addSingleRule($regex, $subst, $option = "", $escape = false, $begin = "", $end = "") {
		$this->addRules(new Sea_Replace_Rules_Single($regex, $subst, $option, $escape, $begin, $end));
	}
	
	/**
	 * Ajoute une regle vide
	 * 
	 */
	public function addEmptyRule() {$this->addSingleRule('', '');}
	
	/**
	 * Ajoute des règles de substitution à partir d'un loader spécifique
	 * 
	 * @param Sea_Replace_Rules_Abstract $loader
	 * 
	 */
	public function addRules(Sea_Replace_Rules_Abstract $rules) {
		$this->append($rules);
	}
	
	/**
	 * Lance le remplacement automatique avec un adapter spécifique
	 * 
	 * @param Sea_Replace_Data_Abstract $data
	 * 
	 */
	public function replace(Sea_Replace_Data_Abstract $data, $callback = null) {
		
		// on ajoute une regle vide siu pas de regle et unction callback appellé
		if (!$this->valid() && is_callable($callback)) {$this->addEmptyRule();}
		
		while ($data->valid()) {
			$this->rewind();
			$result = NULL;
			while ($this->valid()) {
				$rule = $this->current();
				
				$result = preg_replace($rule->getRegex(), $rule->getSubst(), ($result) ? $result : $data->read());
				if ($result === NULL) { throw new Sea_Replace_Exception("preg_replace(\"" . $rule->getRegex() . "\", \"" . $rule->getSubst() . "\", \"" . $data->read() . "\") returned with the error code: " . strval(preg_last_error()), 19);}
			
				// application de la function de callback si elle existe
				try { $result = is_callable($callback) ? $callback($result) : $result; } 
				catch (Exception $e) { new Sea_Replace_Exception("Function callback incorrect");}
				
				$this->next();
			}
			
			$data->write($result);
			$data->next();
		}
	}
}
