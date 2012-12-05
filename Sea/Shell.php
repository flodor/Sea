<?php
/** 
 * @author jhouvion
 * 
 * 
 */
class Sea_Shell {
	
	/**
	 * chemin du fichier binaire executé
	 * 
	 * @var String
	 */
	protected $_command;
	
	/**
	 * argument passé à la commande
	 * 
	 * @var array
	 */
	protected $_args = array();
	
	/**
	 * argument de lancement de l'aide
	 * 
	 * @var String
	 */
	protected $_help = '-h';
	
	/**
	 * charge le processus en sans bloqué
	 * 
	 * @var unknown_type
	 */
	protected $_nohup = false;
	
	/**
	 * 
	 * pointeur de ressource du processus
	 * 
	 * @var unknown_type
	 */
	protected $_process;
	
	/**
	 * flux d'entrée
	 * 
	 * @var unknown_type
	 */
	protected $_stdin;
	
	/**
	 * flux de sortie
	 * 
	 * @var unknown_type
	 */
	protected $_stdout;
	
	/**
	 * flux d'erreur
	 * 
	 * 
	 */
	protected $_stderr;
	
	/**
	 * constuction de la commande
	 * 
	 * @var String
	 */
	protected function _build() {

		// rcuperation de la command
		$cmd = escapeshellcmd($this->getCommand());
		
		// verification
		if (empty($cmd) || !file_exists($cmd)) {throw new Exception('Aucune commande trouvé : ' . $cmd);}
		if (!is_executable($cmd)) {throw new Exception('La fichier de commande n\'est pas executable : ' . $cmd);}
		
		// construction des arguments
		foreach ($this->_args as $key => $arg) {$cmd .= ' ' . (is_numeric($key) ? '' : $key) . (is_null($arg) ? '' : ' ' . escapeshellarg($arg));}

		// ajout des insctruction nihup si necessaire
		if ($this->isNohup()) {$cmd = "nohup " . $cmd . ' > /dev/null & echo $!';}
		
		// renvoie la commande complete
		return $cmd;
	}
	
	/**
	 * renvoie si la tache sera executé en nohup
	 * 
	 */
	public function isNohup() {
		return $this->_nohup;
	}
	
	/**
	 * renvoie le contenu de l'aide
	 * 
	 */
	public function getHelp() {
		
		// argument de l'aide
		$this->_args = array($this->_help);
		
		// on lance le processus
		$this->run();
		
		// on renvoie la sortie
		return $this->getOutput();
	}
	
	/**
	 * recuperation du pid du processus courant
	 * 
	 * @throws Exception
	 */
	public function getPid() {
		
		// on verifie que le processus est actif
		if (!is_resource($this->_process)) {throw new Exception('Aucun processus de présent');}
		
		// recuperatiuon des status
		$status = proc_get_status($this->_process);
		
		return $status['pid'];
	}

	/**
	 * constructeur
	 * 
	 * @param unknown_type $puid
	 * @param unknown_type $guid
	 * @param unknown_type $umask
	 */
	public function __construct() {
		
	    // récuperere les information passé en paramètre
		$args = func_get_args();
		
		//on apelle la function init avec les paramètres passé au constructeur
		call_user_func_array(array($this, 'init'), $args);
	}
	
	/**
	 * ajoute du traietement pendant la constructiond e l'objet
	 * 
	 */
	public function init() {;}
	
	/**
	 * lance la commande
	 * (non-PHPdoc)
	 * 
	 */
	public function run($stdin = false, $stdout = false, $stderr = false) {
		
		// construction de la requete
		$cmd = $this->_build();
		
		// contrctuion des pipe entrant et sortant
		$descriptorspec = array(	$stdin ? array('file', $stdin, 'r') : array('pipe', 'r'),
									$stdout ? array('file', $stdout, 'r') : array('pipe', 'w'),
									$stderr ? array('file', $stderr, 'r') : array('pipe', 'a'));

		// on lance le processus
		d($cmd);
		$process = proc_open($cmd, $descriptorspec, $pipes);
		
		stream_set_blocking ($pipes[0], 0);
		stream_set_blocking ($pipes[1], 0);
		stream_set_blocking ($pipes[2], 0);
		
		// on verifie que le lancement c'est bien passé
		if (!is_resource($process)) {throw new Exception('Erreur sur l\'execution de la commande : ' . $cmd);}
		
		// attribution des flux
		$this->_stdin = $pipes[0];
		$this->_stdout = $pipes[1];
		$this->_stderr = $pipes[2];
		
		// on stocke le processus
		$this->_process = $process;

		return $this;
	}
	
	/**
	 * renvoie si le processus fonctionne toujours
	 * 
	 * @throws Exception
	 */
	public function isRunning() {
		
		// on verifie que le processus est actif
		if (!is_resource($this->_process)) {throw new Exception('Aucun processus de présent');}
		
		// recuperationd es status
		$status = proc_get_status($this->_process);
		
		return $status['running'];
	}
	
	/**
	 * fermeture du processus
	 * 
	 */
	public function close() {
		
		// on verifie que le processus est actif
		if (!is_resource($this->_process)) {throw new Exception('Aucun processus de présent');}
		
		//fermeture
		proc_close($this->_process);
		
		return $this;
	}
	
	/**
	 * @return the $_command
	 */
	public function getCommand() {
		return $this->_command;
	}

	/**
	 * @param String $_command
	 */
	public function setCommand($_command) {
		$this->_command = $_command;
		return $this;
	}
	
/**
	 * mise a 0 des arguments
	 * 
	 */
	public function resetArgs() {
		$this->_args = array();
		return $this;
	}
	
	/**
	 * ajout d'un argument
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function addArg($key, $value = null) {
		$this->_args[$key] = $value;
	}
	
	/**
	 * enleve un argument
	 * 
	 * @param unknown_type $key
	 */
	public function removeArg($key) {
		unset($this->_args[$key]);
	}
	/**
	 * @return the $_args
	 */
	public function getArgs() {
		return $this->_args;
	}

	/**
	 * @return the $_stdin
	 */
	public function getStdin() {
		return $this->_stdin;
	}

	/**
	 * @return the $_stdout
	 */
	public function getStdout() {
		return $this->_stdout;
	}

	/**
	 * @return the $_stderr
	 */
	public function getStderr() {
		return $this->_stderr;
	}

	/**
	 * @param array $_args
	 */
	public function setArgs($_args) {
		$this->_args = $_args;
		return $this;
	}
	/**
	 * @param unknown_type $_nohup
	 */
	public function setNohup($_nohup) {
		$this->_nohup = $_nohup;
		return $this;
	}
}

?>