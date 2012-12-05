<?php

include_once 'PHPExcel.php';
require_once 'Sea/Excel/Table.php';
require_once 'Sea/Excel/Table/Column/Value.php';
require_once 'Sea/Excel/Table/Column/Formula.php';

/**
 * Classe de gestion d'export excel
 * 
 * @author jhouvion
 *
 */
class Sea_Excel extends PHPExcel {

	/**
	 * Defini les colonnes
	 * 
	 * @var Array
	 */
	protected $_column;
	
	/**
	 * Format de sortie
	 * 
	 * @var unknown_type
	 */
	protected $_format = 'Excel2003';
	
	/**
	 * Paramètre passer a l'objet
	 * 
	 * @var Array
	 */
	protected $_parameters = array();
	
	
	/**
	 * Constructeur
	 * 
	 * @param $param array
	 */
	function __construct($param = array()) {
		parent::__construct();
		$this->setParameters($param);
		$this->init();
	}
	

	/**
	 * function lancé dans le constructeur
	 * 
	 * C'est la fonction a surcharger sur l'extension de l'objet
	 * 
	 */
	protected function init() {null;}

	/**
	 * Getter pour le format de sortie du fichier
	 * 
	 */
	public function getFormat() {return $this->_format;}
	
	/**
	 * setter pour les paramètre
	 * 
	 * @param Array $a
	 * @return Sea_Excel2
	 */
	public function setParameters($a) {
		$this->_parameters = (array) $a;
		return $this;
	}
	
	/**
	 * getter pour les paramétre
	 * 
	 */
	public function getParameters() {
		return $this->_parameters;
	}
	
	/**
	 * Recupere un parametre en fonction de son index
	 * 
	 * @param $index
	 * @return Multi
	 */
	public function getParameter($index) {
		return array_key_exists($index, $this->_parameters) ? $this->_parameters[$index] : false;	
	}
	
	/**
	 * Méthode englobant une fonction switch pour le choix du format de tableur.
	 * @method affiche
	 * @param String $format
	 * @param String $nomFichier
	 * @example $workbook->affiche('Excel2007','MonFichier');
	 */
	public function render($nomFichier = 'Tableur', $format = ''){
		
		if (empty($format)) { $format = $this->getFormat();}
		
	    switch($format){
	        case 'Excel2007' :
	            include 'PHPExcel/Writer/Excel2007.php';
	            $writer = new PHPExcel_Writer_Excel2007($this);
	            $ext = 'xlsx';
	            $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	            //supprime le pré-calcul
	            $writer->setPreCalculateFormulas(false);
	            break;
	        case 'Excel2003' :
	            include 'PHPExcel/Writer/Excel2007.php';
	            $writer = new PHPExcel_Writer_Excel2007($this);
	            $writer->setOffice2003Compatibility(true);
	            $ext = 'xlsx';
	            $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	            //supprime le pré-calcul
	            $writer->setPreCalculateFormulas(false);
	            break;
	        case 'Excel5' :
	            include 'PHPExcel/Writer/Excel5.php';
	            $writer = new PHPExcel_Writer_Excel5($this);
	            $ext = 'xls';
	            $header = 'application/vnd.ms-excel';
	            break;
	        case 'CSV' :
	            include 'PHPExcel/Writer/CSV.php';
	            $writer = new PHPExcel_Writer_CSV($this);
	            $writer->setDelimiter(",");//l'opérateur de séparation est la virgule
	          $writer->setSheetIndex(0);//Une seule feuille possible
	          $ext = 'csv';
	          $header = 'text/csv';
	          break;
	      case 'PDF' :
	          include 'PHPExcel/Writer/PDF.php';
	          $writer = new PHPExcel_Writer_PDF($this);
	          $writer->setSheetIndex(0);//Une seule feuille possible
	          $ext = 'pdf';
	          $header = 'application/pdf';
	          break;
	      case 'HTML' :
	          include 'PHPExcel/Writer/HTML.php';
	          $writer = new PHPExcel_Writer_HTML($this);
	          $writer->setSheetIndex(0);//Une seule feuille possible
	          $ext = 'html';
	          $header = 'text/html';
	          break;
	      default:
	      	throw new Zend_Exception('Le format de sortie n\'est pas correct');
	      	break;
	  }
	  
	  header('Content-type:'.$header);
	  header('Content-Disposition:inline;filename='.$nomFichier.'.'.$ext);
	  $writer->save('php://output');
	}
	            
	
	    /**
	 * Méthode englobant une fonction switch pour le choix du format de tableur.
	 * @method enregistre
	 * @param String $format
	 * @param String $nomFichier
	 * @example $workbook->enregistre('Excel2007','MonFichier');
	 */
	public function save($nomFichier = 'Tableur', $format = 'Excel5'){
	    switch($format){
	        case 'Excel2007' :
	            include 'PHPExcel/Writer/Excel2007.php';
	            $writer = new PHPExcel_Writer_Excel2007($this);
	            $ext = 'xlsx';
	            //supprime le pré-calcul
	            $writer->setPreCalculateFormulas(false);
	            break;
	        case 'Excel2003' :
	            include 'PHPExcel/Writer/Excel2007.php';
	            $writer = new PHPExcel_Writer_Excel2007($this);
	            $writer->setOffice2003Compatibility(true);
	            $ext = 'xlsx';
	            //supprime le pré-calcul
	            $writer->setPreCalculateFormulas(false);
	            break;
	        case 'Excel5' :
	            include 'PHPExcel/Writer/Excel5.php';
	            $writer = new PHPExcel_Writer_Excel5($this);
	            $ext = 'xls';
	            break;
	        case 'CSV' :
	            include 'PHPExcel/Writer/CSV.php';
	            $writer = new PHPExcel_Writer_CSV($this);
	            $writer->setDelimiter(",");//l'opérateur de séparation est la virgule
	            $writer->setSheetIndex(0);//Une seule feuille possible
	            $ext = 'csv';
	            break;
	        case 'PDF' :
	            include 'PHPExcel/Writer/PDF.php';
	            $writer = new PHPExcel_Writer_PDF($this);
	            $writer->setSheetIndex(0);//Une seule feuille possible
	            $ext = 'pdf';
	            break;
	        case 'HTML' :
				include 'PHPExcel/Writer/HTML.php';
        		$writer = new PHPExcel_Writer_HTML($this);
          		$writer->setSheetIndex(0);//Une seule feuille possible
          		$ext = 'html';
          		break;
	        default: throw new Zend_Exception('Le format de sortie n\'est pas correct');break;
	  }
	  $writer->save($nomFichier.'.'.$ext);
	}
	
	/**
	 * Créé un objet de paramétrage de tableau et le renvoie
	 * 
	 * @Return Sea_Excel_Table
	 * 
	 */
	public function table() { return new Sea_Excel_Table(); }
	
	/**
	 * Créé un objet de paramétrage de tableau et le paramètre en focntion d'une requete SQL
	 *
	 * 
	 * @param String $sql => requete SQL
	 */
	public function tableFromSql($sql, $header = array()) {
		
		// formatage de la requete
		$sql = ($sql instanceof Zend_Db_Select) ? $sql->assemble() : $sql;
		
		// on essaye de recuperer la connection courante
		$db = Zend_Db_Table::getDefaultAdapter();
		
		// construction de la connection
		$data = $db->fetchAll($sql);
		$table = !empty($data) ? $this->tableFromArray($data, $header) : $this->table();
			
		return $table;
	}
	
	/**
	 * créer un tableau a partir d'un tableau
	 * 
	 * @param $array
	 * @param $header
	 */
	public function tableFromArray($array, $header = array()) {
		
		$table = $this->table();// création de la table
	
		//attribution des donnée
		$table->setData($array); 
		
		// Création des champs
		foreach(current($array) as $index => $value) {
			if(is_numeric($index)) continue;
			$label = array_key_exists($index, $header) ? $header[$index] :  $index;
			if (mb_detect_encoding($label, array("UTF-8", "ISO-8859-1", "ASCII")) != "UTF-8") {$label = utf8_encode($label);}
			$table->add(new Sea_Excel_Table_Column_Value( $label,  $index));
		} 
		
		return $table;
	}
	
	/**
	 * Insert une tables dans la feuille active
	 * 
	 * @param Sea_Excel_Table $t
	 */
	public function addTable(Sea_Excel_Table $t) {
		$sheet = $this->getActiveSheet();// recupération de la feuille active
		$t->render($sheet);// inscription du tableau dans la feuille
	}
}
