<?php
require_once("pChart/pDraw.class.php");
require_once("pChart/pData.class.php");
require_once("pChart/pImage.class.php");
require_once("pChart/pPie.class.php");


class Sea_pChart extends pImage {
	
	/**
	 * constructeur
	 * 
	 * 
	 * @param unknown_type $XSize
	 * @param unknown_type $YSize
	 */
	public function __construct($XSize = false,$YSize = false) {
		
		// traitement des paramètre
		$XSize = $XSize !== false ? $XSize : $this->XSize;
		$YSize = $YSize !== false ? $YSize : $this->YSize;
		
		parent::pImage($XSize,$YSize);// construction du parent
		$this->setFont();// initilisation de la police
		return call_user_func_array(array($this, 'init'), array_slice(func_get_args(), 2));// initialisation
	}
	
	/**
	 * initilisation
	 * 
	 */
	protected function init() {;}
	
	/**
	 * getter pour les data
	 * 
	 * @return pDraw
	 */
	public function getData() {
		// si les donnée ne sont pas initialisé, on les crée
		if (empty($this->DataSet)) {$this->setDataSet(new pData());}
		return $this->DataSet;
	}
	
	/**
	 * Charge une font pour l'ecriture
	 * 
	 * @param unknown_type $font
	 * @param unknown_type $size
	 * @param unknown_type $r
	 * @param unknown_type $g
	 * @param unknown_type $b
	 */
	public function setFont($font = 'verdana', $size = 8, $r = 0, $g = 0, $b = 0) {
		$this->setFontProperties(array('FontName' => $font, 'FontSize' => $size, 'R' => $r, 'G' => $g, 'B' => $b));
	}
	
	/**
	 * ajout d'un titre
	 * 
	 */
	public function setTitle($title) {
		 $this->drawFilledRectangle(0,0,$this->XSize,24,array("R"=> 150, "G"=>150, "B"=>150 , "Dash"=>1, "DashR"=>190, "DashG"=>190, "DashB"=>190));
		 $this->drawRectangle(0,0,$this->XSize - 1 ,25,array("R"=> 80,"G"=>80,"B"=>80));
		 $this->drawText($this->XSize / 2,20,$title,array("FontSize"=> 15, "Align"=>TEXT_ALIGN_BOTTOMMIDDLE, "R"=> 255,"G"=> 255,"B"=> 255));
	}
	
	/**
	 * AJoute un font au dessin
	 * 
	 */
	public function addBackground($Settings = array("R"=>230, "G"=>230, "B"=>230, "Dash"=>1, "DashR"=>240, "DashG"=>240, "DashB"=>240)) {
		 $this->drawFilledRectangle(0, 0, $this->XSize, $this->YSize,$Settings);
		 $this->drawRectangle(0,0,$this->XSize - 1,$this->YSize - 1,array("R"=> 80,"G"=>80,"B"=>80));
	}

}

?>