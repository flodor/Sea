<?php

/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Validate {
	
	/**
	 * message d'erreur
	 * 
	 * @var unknown_type
	 */
	static $messages = array();
	
	/**
	 * verifie une valeur a partir d'un validateur
	 * 
	 * @param unknown_type $value
	 * @param unknown_type $type
	 */
	static function valid($value, $type) {
		
		// initilisation
		$messages = array();
		$validator= false;
		
		if ($type instanceof Zend_Validate_Abstract)	 {$validator = $type;}
		else {
			foreach(Zend_Loader_Autoloader::getInstance()->getRegisteredNamespaces() as $namespace) {
				
				// construction du nom de la classe
				$class = sprintf('%sValidate_%s', $namespace, $type);

				// creation du validateur si la class existe
				try {
					@Zend_Loader::loadClass($class);// on essaie de charger la classe
					$php = '';// initialisationd es arguments
					foreach((array) $args = array_slice(func_get_args(), 2) as $k => $v) {$php .= (empty($php) ? '' : ',') . sprintf('$args[%s]', $k);}
					eval (sprintf('$validator = new %s(%s);', $class, $php));// création du validateur
				} catch(Exception $e) {;}
			}
		}
		
		// si mauvais validateur, on envoie une exception
		if (!($validator instanceof Zend_Validate_Abstract)) {throw new Exception('Validateur invalide');}
		
		$return = $validator->isValid($value);// on verifie si la valeur est ok
		self::$messages = $validator->getMessages();// inscription des messages
		return $return;// on retourne le resultat du test
	}
	
	/**
	 * renvoie une chaine formater avec les les message d'erreur
	 * 
	 */
	static function getTextMessages() {return implode(PHP_EOL, self::$messages);}
}