<?php

require_once ('Zend/Validate/EmailAddress.php');

class Sea_Validate_EmailGarbage extends Zend_Validate_EmailAddress {
	
	
	const ADDRESS_NOT_ALLOWED = 'emailAddressGarbage';
	
	/**
	 * 
	 * regle de refus
	 * 
	 * @var unknown_type
	 */
	protected $_rules = array(	'#@0-mail\..*#i',
								'#@10minutemail\..*#i',
								'#@10x9\..*#i',
								'#@12minutemail\..*#i',
								'#@aemail4u\..*#i',
								'#@arobase\..*#i',
								'#@beefmilk\..*#i',
								'#@BeefMilk\..*#i',
								'#@bluebottle\..*#i',
								'#@brefmail\..*#i',
								'#@bsnow\..*#i',
								'#@callcustomercare\..*#i',
								'#@contre-spam\..*#i',
								'#@cool\..*#i',
								'#@cool.fr\..*#i',
								'#@cortexmail\..*#i',
								'#@courriel.fr\..*#i',
								'#@creativeaffers\..*#i',
								'#@deadaddress\..*#i',
								'#@despam\..*#i',
								'#@destroy-spam\..*#i',
								'#@dingbone\..*#i',
								'#@DingBone\..*#i',
								'#@dodgit\..*#i',
								'#@e4ward\..*#i',
								'#@email-jetable\..*#i',
								'#@emailias\..*#i',
								'#@emedivision\..*#i',
								'#@eyepaste\..*#i',
								'#@FatFlap\..*#i',
								'#@fatflap\..*#i',
								'#@fakemail\..*#i',
								'#@fastfunmail\..*#i',
								'#@filzmail\..*#i',
								'#@fornow\..*#i',
								'#@forthsquare\..*#i',
								'#@gishpuppy\..*#i',
								'#@gmx\..*#i',
								'#@guerrillamail\..*#i',
								'#@haltospam\..*#i',
								'#@inbox.jbi\..*#i',
								'#@incognitomail\..*#i',
								'#@jetable\..*#i',
								'#@jetable.fr\..*#i',
								'#@jnxjn\..*#i',
								'#@justonemail\..*#i',
								'#@kasmail\..*#i',
								'#@keepmymail\..*#i',
								'#@kleemail\..*#i',
								'#@kurzepost\..*#i',
								'#@letmymail\..*#i',
								'#@LookUgly\..*#i',
								'#@lookugly\..*#i',
								'#@mail-jetable.appspotmail\..*#i',
								'#@mail-temporaire\..*#i',
								'#@mailbidon\..*#i',
								'#@mailboxable\..*#i',
								'#@mailcatch\..*#i',
								'#@mailexpire\..*#i',
								'#@mailinator\..*#i',
								'#@mailincubator\..*#i',
								'#@mailmetrash\..*#i',
								'#@mailmoat\..*#i',
								'#@mailnesia\..*#i',
								'#@mailscap\..*#i',
								'#@mbx\..*#i',
								'#@mega.zik\..*#i',
								'#@meltmail\..*#i',
								'#@mintemail\..*#i',
								'#@moncourrier.fr\..*#i',
								'#@monemail\..*#i',
								'#@monmail\..*#i',
								'#@mt2009\..*#i',
								'#@mymailoasis\..*#i',
								'#@mytrashmail\..*#i',
								'#@nabuma\..*#i',
								'#@nepwk\..*#i',
								'#@no-spam\..*#i',
								'#@nomail.xl\..*#i',
								'#@nospam.ze\..*#i',
								'#@nowmymail\..*#i',
								'#@objectmail\..*#i',
								'#@onelastmail\..*#i',
								'#@pjjkp\..*#i',
								'#@pookmail\..*#i',
								'#@proxymail\..*#i',
								'#@rcpt\..*#i',
								'#@rcpt.at\..*#i',
								'#@sitiads\..*#i',
								'#@slopsbox\..*#i',
								'#@SmellFear\..*#i',
								'#@smellfear\..*#i',
								'#@sneakemail\..*#i',
								'#@spam\..*#i',
								'#@spamavert\..*#i',
								'#@spambox\..*#i',
								'#@spamex\..*#i',
								'#@spamfree24\..*#i',
								'#@spamgourmet\..*#i',
								'#@spamjackal\..*#i',
								'#@spammotel\..*#i',
								'#@spamsphere\..*#i',
								'#@speed.1s\..*#i',
								'#@tempmail\..*#i',
								'#@tempinbox\..*#i',
								'#@tempomail\..*#i',
								'#@thankyou2010\..*#i',
								'#@thankyou2011\..*#i',
								'#@thankyou2012\..*#i',
								'#@thisisnomyrealemail\..*#i',
								'#@trash-mail\..*#i',
								'#@trash-mail.at\..*#i',
								'#@trash2009\..*#i',
								'#@trash2010\..*#i',
								'#@trash2011\..*#i',
								'#@trash2012\..*#i',
								'#@trashmail\..*#i',
								'#@trashmail.at\..*#i',
								'#@trashymail\..*#i',
								'#@tyldd\..*#i',
								'#@uggsrock\..*#i',
								'#@wegwerfmail\..*#i',
								'#@yopmail\..*#i',
								'#@youumail\..*#i',
								'#@zoemail\..*#i',
								'#.*\.at$#i',
								'#.*\.cx$#i',
								'#.*\.dj$#i',
								'#.*\.me$#i',
								'#.*\.ru$#i',
								'#.*\.tc$#i',
								'#.*\.xl$#i',
								'#.*\.ze$#i');	 
	
    /**
	 * surcharge du constructeur
     */
	public function __construct($options = array()) {
		
		// ajout du message d'erreur
		$this->_messageTemplates[self::ADDRESS_NOT_ALLOWED] = "Adresse email non autorisée";
		
		// rappel du parent
		call_user_func_array( 'parent::__construct', func_get_args());
	}
	
	
	/**
	 * validation
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value) {
		
		// on verifie si l'adresse merge 
		foreach ($this->_rules as $rule ) {if (preg_match($rule, $value)) {$this->_error(self::ADDRESS_NOT_ALLOWED);return false;}}
		
		// renvoie de la validation du parent
		return parent::isValid($value);
	}
}

?>