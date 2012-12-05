<?php

/**
 * Une règle servant de message entre le moteur Sea_Replace et différents loaders.
 * 
 * @author Tibor Vass
 *
 */
class Sea_Replace_Rule {

	/**
	 * Constructeur prenant une expression rationnelle sans slash (/) de début ni de fin,
	 * une chaîne de caractères de remplacement, et une option regexp (ex: 'm', 'i', and 's')
	 * 
	 * @param string $regex
	 * @param string $subst
	 * @param string $option
	 * @param boolean $escape
	 * @param boolean $protected
	 * 
	 */
	public function __construct($regex, $subst, $option = "", $escape = false, $protected = true) {
		if (!is_string($regex)) {throw new Sea_Replace_Exception('Parameter $regex must be a string', 7);}
		if (!is_string($subst)) {throw new Sea_Replace_Exception('Parameter $subst must be a string', 8);}
		if (!is_string($option) || preg_match('/[^usim]+/i', $option) != 0) {throw new Sea_Replace_Exception('Parameter $option must be a regexp modifier string (valid modifiers are: \'s\', \'i\', \'m\')', 9);}		
		if (!is_bool($escape)) {throw new Sea_Replace_Exception('Parameter $escape must be a boolean', 10);}
		if (!is_bool($protected)) {throw new Sea_Replace_Exception('Parameter $protected must be a boolean', 11);} 
		$this->_regex = $regex;
		$this->_subst = ($escape) ? preg_quote($subst) : $subst;
		$this->_option = $option;
		$this->_protected = $protected;
	}
	
	/**
	 * Reformate l'expression rationnelle et la chaîne de caractères de remplacement
	 * de manière à protéger les règles (il n'y a donc pas d'enchaînements de règles,
	 * chaque règle est appliquée indépendemment les unes des autres)
	 * 
	 * Afin de ne pas créer d'interférences, $begin et $end doivent être choisis de telle manière
	 * que ces derniers ne figurent pas dans les chaînes de caractères à analyser.
	 * 
	 * Une même règle ne peut être formatée deux fois de suite.
	 * 
	 * @param string $begin
	 * @param string $end
	 * 
	 */
	public function format($begin, $end) {
		if (!$this->_protected || $this->_isFormatted) {return;}
		if (!is_string($begin) or !is_string($end)) {
			throw new Sea_Replace_Exception('$begin and $end must be both valid strings', 12);
		}
		if (empty($begin) or empty($end)) {
			throw new Sea_Replace_Exception('$begin and $end must be both non empty strings', 13);
		}
		$this->_regex = '(?:(?!' . $begin . '))' . $this->_regex . '(?:(?!' . $end . '))';
		$this->_subst = $begin . $this->_subst . $end;
		$this->_isFormatted = true;
	}

	/**
	 * Renvoit l'expression rationnelle sous-jacente
	 * 
	 */
	public function getRegex() {
		return '/' . $this->_regex . '/' . $this->_option;
	}
	
	/**
	 * Renvoit la chaîne de caractères de remplacement sous-jacente
	 * 
	 */
	public function getSubst() {
		return $this->_subst;
	}
	
	protected $_regex;

	protected $_subst;
	
	protected $_option;
	
	protected $_protected;
	
	protected $_isFormatted = false;

}
