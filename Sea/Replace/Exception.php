<?php
require_once 'Zend/Exception.php';

/**
 * Exception pour Sea_Replace
 * 
 * @author Tibor Vass
 *
 * 
 * Codes de retour:
 * 
 *  0: Inconnu
 *  1: Impossible d'accéder au dossier
 *  2: RecursiveDirectoryIterator a envoyé une erreur
 *  3: preg_match a renvoyé une erreur
 *  4: Impossible de trouver le fichier
 *  5: Impossible de lire le fichier
 *  6: Impossible d'écrire dans le fichier
 *  7: Paramètre $regex doit être une chaîne de caractères
 *  8: Paramètre $subst doit être une chaîne de caractères
 *  9: Paramètre $option doit être une ou plusieurs des options regexp (s, i, ou m)
 * 10: Paramètre $escape doit être un booléen
 * 11: Paramètre $protected doit être un booléen
 * 12: Paramètres $begin et $end doivent être des chaînes de caractères
 * 13: Paramètres $begin et $end doivent être non vides
 * 14: Paramètres $begin et $end doivent être soit tous deux vides soit tous deux des chaînes de caractères
 * 15: PHPExcel a envoyé une erreur
 * 16: N'arrive pas à convertir en booléen
 * 17: La colonne D du fichier Excel ne peut avoir que les valeurs suivantes "true", "on", ou "1" (pour VRAI), et "false", "off", ou "0" (pour FAUX)  
 * 18: La colonne E du fichier Excel ne peut avoir que les valeurs suivantes "true", "on", ou "1" (pour VRAI), et "false", "off", ou "0" (pour FAUX)
 * 19: preg_replace a envoyé une erreur
 * 
 */
class Sea_Replace_Exception extends Zend_Exception {
	
}
