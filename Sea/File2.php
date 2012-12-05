<?php

class Sea_File2 {
	
	/**
	 * pointeur
	 *
	 * @var unknown_type
	 */
	protected $_handle;
	
	/**
	 * mode d'ouverture du fichier
	 *
	 * @var unknown_type
	 */
	protected $_mode;
	
	/**
	 * nom du fichier
	 *
	 * @var unknown_type
	 */
	protected $_filename;
	
	/**
	 * Constructeur
	 *
	 * @param $filename, $mode        	
	 */
	function __construct($filename, $mode = 'a') {
		
		$this->setFilename ($filename);// on set le nom du fichier
		$this->setMode ($mode); //on set le mode
		$this->is_readable();
		$this->_open();
	}
	
	/**
	 * Initialisation
	 *
	 * @param Sea_File2        	
	 */
	protected function _open() {
		//si le fichier est lisible on l'ouvre
		if(!($this->_handle = fopen($this->getFilename(), $this->getMode()))) {
			throw new Sea_Exception('Impossible d\'ouvrir le fichier : %s', $this->getFilename());
		}
	}

	
	/**
	 * lit le contenu du fichier
	 *
	 * @return String $content
	 */
	public function read($rewind = true) {
		//cas ou on souhaite lire au debut du fichier
		if ($rewind){rewind ( $this->_handle );}

		// Lecture du contenu du fichier
		if (($content = fread ( $this->_handle, filesize ( $this->getFilename() ) )) === false)
			throw new Sea_Exception ( 'Impossible d\'ouvrir ou de lire le fichier %s', $this->getFilename() );
		
		return $content;
	}
	
	/**
	 *
	 * Ecrit dans le fichier
	 *
	 * @param String $content        	
	 * @return Sea_File2
	 */
	public function write($content) {
		
		// Ecriture du fichier
		if (fwrite ( $this->_handle, $content ) === false) {
			throw new Sea_Exception ( 'Ecriture impossible dans le fichier : "' . $this->_filename . '"' );
		}
		
	}

	/**
	 * efface le contenu d'un fichier
	 *
	 * @return Sea_File2
	 */
	public function clear() {

		// position du pointeur replacé au debut
		if (! rewind ( $this->_handle, 0 ) === false) {
			throw new Sea_Exception ( 'Déplacement du pointeur avec rewind() impossible' );
		}

		// suppression du contenu du fichier
		if (ftruncate ( $this->_handle, 0 ) === false) {
			throw new Sea_Exception ( 'Impossible de supprimer le contenu du fichier : "' . $this->_filename . '"' );
		}

		return $this;
	}

	/**
	 * Deplace un fichier
	 *
	 * @param $filename répertoire        	
	 * @return Sea_File
	 */
	public function move($filename) {

		// on récupere le chemin
		if (! ($source = realpath ( $this->_filename ))) {
			throw new Sea_Exception ( 'le fichier n\'existe pas' );
		}

		// sauvegarde position du pointeur
		if (($position = ftell ( $this->_handle )) === false) {
			throw new Sea_Exception ( 'Impossible de determiné la position du pointeur' );
		}
		// changement de répertoire
		$file = $filename . basename ( $source ); // répertoire de destination +
		                                          // nom du fichier
		if (! @rename ( $source, $file )) {
			throw new Sea_Exception ( 'Impossible de déplacer le fichier' );
		}
		// pointeur replacer
		$this->_open ( $file, $this->_mode );
		fseek ( $this->_handle, $position );
		
		return $this;
	}
	
	/**
	 * supprime un fichier
	 */
	public function delete() {
		if (!@unlink ($this->getFilename())) {
			throw new Sea_Exception ( 'Le fichier "' . $this->_filename . '" n\'existe pas ou ne peux être détruit' );
		}
	}
	
	/**
	 * verifie si on peut lire dans le fichier
	 *
	 */
	private function is_readable() {
		if (!is_readable($this->getFilename())) {
			throw new Sea_Exception ( 'Impossible de lire le fichier "' . $this->getFilename());
		}
		
		return true;
	}
	
	/**
	 * Change les droits d'un fichier
	 *
	 * @param
	 *        	$mode
	 */
	public function chmod($mode) {
		if (!chmod( $this->_filename, $mode )) {
			throw new Sea_Exception ( 'Les droits du fichier "' . $this->_filename . '" ne peuvent pas être changé' );
		}
	}
	
	/**
	 * Info sur la classe
	 * 
	 */
	public function info($options = false) {
		if (!($info = pathinfo( $this->_filename, $options ))) {
			throw new Sea_Exception ( 'Impossible d\'accéder au donné de "' . $this->_filename );
		}
		
		return $info;
	}
	
	/**
	 * donne le nom du fichier (sans extension)
	 *
	 * @return $String
	 */
	public function filename(){
		return $this->info(PATHINFO_FILENAME);
	}
	
	/**
	 * Telecharge le fichier
	 *
	 * @return unknown_type
	 */
	public function download() {
		return $this->_echo(true);
	}
	
	/**
	 * Affiche le fichier
	 * 
	 * @return unknown_type 
	 */
	public function show() {
		return $this->_echo ( false );
	}
	
	/**
	 * 
	 * @param unknown_type $forceDownload
	 */
	protected function _echo($forceDownload = true) {

		
		// verifie que l'on a un contenu

		$basename = $this->basename();// nom du fichier
		$type = $this->mime();// inscription du mime
		
		if ($forceDownload) {
			
			header('X-Sendfile: '.$this->getFilename());
			header('Content-Type: text/html');
			header('Content-Disposition: attachement; filename="'.$basename.'"');
			header('Content-Transfer-Encoding: binary');
			exit ;
// 			$type = !empty( $type ) ? $type : 'application/octet-stream';
// 			header ( "Content-Type: $type" );
// 			header ( "Content-disposition: attachment; filename=\"" . $disposition . "\"" );
// 			header ( 'Content-Transfer-Encoding: binary' );
		} else {
			$type = !empty( $type ) ? $type : 'text/html';
			header ( "Content-Type: $type " );
			header ( "Content-disposition: inline; filename=\"" . $basename . "\"" );
		}
				
		header ( "Content-length: " . filesize ( $this->getFilename() ) ); // on inscrit la taille du fichier
		header ( "Cache-control: private" ); // compatibilité ie
		header ( 'Pragma: cache' ); // compatibilité ie
		header ( "Expires: 0" ); // on supprime le cache
		
		flush (); // on vide le cache
		echo file_get_contents($this->getFilename());// affichage du contenu du fichier
		exit;// on coupe le script
		
		
	}
	
	/**
	 * Setter du mode
	 *
	 * @param unknown_type $mode
	 * @return Sea_File2
	 */
	private function setMode($mode) {
		$this->_mode = $mode;
		return $this;
	}
	
	/**
	 * Getter du mode
	 *
	 * @return $mode
	 */
	private function getMode() {
		return $this->_mode;
	}
	
	/**
	 * Setter du $filename
	 *
	 * @param unknown_type $filename
	 * @return Sea_File2
	 */
	private function setFilename($filename) {
		$this->_filename = $filename;
		return $this;
	}
	
	/**
	 * Getter du $filename
	 *
	 * @return $filename
	 */
	public function getFilename() {
		return $this->_filename;
	}
	
	/**
	 * Telecharge le fichier
	 *
	 *	@return String
	 */
	
	public function mime(){
		return mime_content_type( $this->getFilename() );
	}
	
	/**
	 * donne le nom du repertoire
	 *
	 * @return String
	 */
	
	public function dirname(){
		return $this->info(PATHINFO_DIRNAME);
	}
	
	/**
	 * donne le nom du fichier
	 *
	 * @return $String
	 */
	public function basename(){
		return $this->info(PATHINFO_BASENAME);
	}
	
	/**
	 * donne l'extension du fichier
	 *
	 * @return $String
	 */
	public function extension(){
		return $this->info(PATHINFO_EXTENSION);
	}
}
