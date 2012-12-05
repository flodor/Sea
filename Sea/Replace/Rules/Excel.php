<?php

require_once 'PHPExcel/IOFactory.php';
require_once 'Abstract.php';

/**
 * Charge les règles à partir d'un fichier Excel
 * 
 * Dans la première feuille de calcul (sheet 1),
 * colonne A : expressions régulières (OBLIGATOIRE)
 * colonne B : chaînes de caractères de remplacement (par défaut: chaîne vide)
 * colonne C : options à faire passer à preg_replace (regexp modifiers s, m, et i) (par défaut: chaîne vide)
 * colonne D : booléen pour l'échappement de la chaîne de caractères de remplacement ("1", "on", "true" pour activer (par défaut), "0", "off", "false" pour désactiver)
 * colonne E : booléen pour la protection de la règle ("1", "on", "true" pour activer (par défaut), "0", "off", "false" pour désactiver)
 *  
 * Le chargement s'arrête à la première case vide de la colonne A.
 * 
 * @author Tibor Vass
 *
 */
class Sea_Replace_Rules_Excel extends Sea_Replace_Rules_Abstract {
	
	/**
	 * Constructeur
	 * 
	 * @param string $filename
	 * @param string $begin
	 * @param string $end
	 * 
	 */
	public function __construct($filename, $begin = "#_BEGIN_#", $end = "#_END_#") {
		$this->_load($filename, $begin, $end);
	}
	
	protected function _load($filename, $begin, $end) {
		// Vérification des entrées
		if (empty($begin) xor empty($end)) {throw new Sea_Replace_Exception('$begin and $end must be both empty or both valid strings', 14);}
		if (!file_exists($filename)) {throw new Sea_Replace_Exception("Cannot find file $filename", 4);}
		if (!is_readable($filename)) {throw new Sea_Replace_Exception("Cannot read file $filename", 5);}
		
		// Chargement du fichier Excel
		try {
			$sheet = PHPExcel_IOFactory::load($filename)->setActiveSheetIndex(0);
		} catch (Exception $e) {
			throw new Sea_Replace_Exception("PHPExcel returned with the following error message:\n" . $e->getMessage(), 15);
		}

		// Parsing du fichier Excel, jusqu'à la première case vide de la colonne A
		for ($i = 1; $regexp = (string) $sheet->getCell("A" . strval($i))->getValue(); $i++) {
			$subst = $sheet->getCell("B" . strval($i))->getValue();
			$opts = $sheet->getCell("C" . strval($i))->getValue();
			$escape = $sheet->getCell("D" . strval($i))->getValue();
			$protected = $sheet->getCell("E" . strval($i))->getValue();

			// Convertir $escape en booléen
			try {
				$escape = self::_toBool($escape, self::$DEFAULT_ESCAPE);				
			} catch (Sea_Replace_Exception $e) {
				if ($e->getCode() == 16) {
					throw new Sea_Replace_Exception("Column D in the Excel file $filename, must have cells containing true/on/1 or false/off/0 only", 17);
				} else { throw $e; }
			}
			
			// Convertir $protected en booléen
			try {
				$protected = self::_toBool($protected, self::$DEFAULT_PROTECTION);				
			} catch (Sea_Replace_Exception $e) {
				if ($e->getCode() == 16) {
					throw new Sea_Replace_Exception("Column E in the Excel file $filename, must have cells containing true/on/1 or fals/off/0 only", 18);
				}
			}
			
			// Création de la règle
			try {
				$data = new Sea_Replace_Rule($regexp, $subst, $opts, $escape, $protected);
			} catch (Sea_Replace_Exception $e) {
				
			}
			$data->format($begin, $end);
			
			// Ajout de la règle
			$this->append($data);
		}
		if (!empty($begin)) {
			$this->append(new Sea_Replace_Rule($begin, '', '', false, false));
			$this->append(new Sea_Replace_Rule($end, '', '', false, false));
		}
	}
	
	protected static $DEFAULT_ESCAPE = true;
	 
	protected static $DEFAULT_PROTECTION = true;
	
	
	protected static function _toBool($e, $default) {
		if ($e === null) {
			$e = self::$DEFAULT_ESCAPE;
		} else {
			$e = (string) $e;
			$new_e = preg_replace(array('/true|on|1/i', '/false|off|0/i'), array("#", ""), $e);
			if ($e == $new_e) {throw new Sea_Replace_Exception("Cannot convert to boolean", 16);}
			$e = (boolean) $new_e;
		}
		return $e;
	}
	
}
