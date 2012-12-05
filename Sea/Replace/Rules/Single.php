<?php

require_once 'Abstract.php';

/**
 * Charge une simple règle à partir d'une expression rationnelle et
 * d'une chaîne de caractères de remplacement.
 * 
 * @author Tibor Vass
 *
 */
class Sea_Replace_Rules_Single extends Sea_Replace_Rules_Abstract {
	
	/**
	 * Constructeur prenant la règle sous forme d'une expression régulière
	 * et d'une chaîne de caractères de remplacement.
	 * 
	 * ATTENTION:
	 * Par défaut, la règle est non protégée. 
	 * 
	 * @param string $regex
	 * @param string $subst
	 * @param string $option
	 * @param bool $escape
	 * @param string $begin
	 * @param string $end
	 * 
	 */
	public function __construct($regex, $subst, $option = "", $escape = true, $begin = "", $end = "") {
		$this->_load($regex, $subst, $option, $escape, $begin, $end);
	}

	protected function _load($regex, $subst, $option, $escape, $begin, $end) {
		if (empty($begin) xor empty($end)) {
			throw new Sea_Replace_Exception('$begin and $end must be both empty or both valid strings', 14);
		}
		$protected = (empty($begin)) ? false : true;
		$rule = new Sea_Replace_Rule($regex, $subst, $option, $escape, $protected);
		$rule->format($begin, $end);
		$this->append($rule);
	}
	
}
