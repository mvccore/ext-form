<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Validators;

use Traversable;

class CreditCard extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Detected CCI list.
	 * @var string
	 */
	const AMERICAN_EXPRESS	= 'American_Express';
	const UNIONPAY			= 'Unionpay';
	const DINERS_CLUB		= 'Diners_Club';
	const DINERS_CLUB_US	= 'Diners_Club_US';
	const DISCOVER			= 'Discover';
	const JCB				= 'JCB';
	const LASER				= 'Laser';
	const MAESTRO			= 'Maestro';
	const MASTERCARD		= 'Mastercard';
	const SOLO				= 'Solo';
	const VISA				= 'Visa';
	const MIR				= 'Mir';

	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_CHECKSUM			= 0;
	const ERROR_CONTENT			= 1;
	const ERROR_INVALID			= 2;
	const ERROR_LENGTH			= 3;
	const ERROR_PREFIX			= 4;
	const ERROR_SERVICE			= 5;
	const ERROR_SERVICEFAILURE	= 6;

	/**
	 * List of CCV names.
	 * @var array
	 */
	protected static $cardTypes = [
		self::AMERICAN_EXPRESS,
		self::DINERS_CLUB,
		self::DINERS_CLUB_US,
		self::DISCOVER,
		self::JCB,
		self::LASER,
		self::MAESTRO,
		self::MASTERCARD,
		self::SOLO,
		self::UNIONPAY,
		self::VISA,
		self::MIR,
	];

	/**
	 * List of allowed CCV lengths.
	 * @var array
	 */
	protected static $cardLengths = [
		self::AMERICAN_EXPRESS	=>	[15],
		self::DINERS_CLUB		=>	[14],
		self::DINERS_CLUB_US	=>	[16],
		self::DISCOVER			=>	[16, 19],
		self::JCB				=>	[15, 16],
		self::LASER				=>	[16, 17, 18, 19],
		self::MAESTRO			=>	[12, 13, 14, 15, 16, 17, 18, 19],
		self::MASTERCARD		=>	[16],
		self::SOLO				=>	[16, 18, 19],
		self::UNIONPAY			=>	[16, 17, 18, 19],
		self::VISA				=>	[13, 16, 19],
		self::MIR				=>	[13, 16],
	];

	/**
	 * List of accepted CCV provider tags.
	 * @var array
	 */
	protected static $cardPrefixes = [
		self::AMERICAN_EXPRESS	=> ['34', '37'],
		self::DINERS_CLUB		=> ['300', '301', '302', '303', '304', '305', '36'],
		self::DINERS_CLUB_US	=> ['54', '55'],
		self::DISCOVER			=> ['6011', '622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925', '644', '645', '646', '647', '648', '649', '65'],
		self::JCB				=> ['1800', '2131', '3528', '3529', '353', '354', '355', '356', '357', '358'],
		self::LASER				=> ['6304', '6706', '6771', '6709'],
		self::MAESTRO			=> ['5018', '5020', '5038', '6304', '6759', '6761', '6762', '6763', '6764', '6765', '6766', '6772'],
		self::MASTERCARD		=> ['2221', '2222', '2223', '2224', '2225', '2226', '2227', '2228', '2229', '223', '224', '225', '226', '227', '228', '229', '23', '24', '25', '26', '271', '2720', '51', '52', '53', '54', '55'],
		self::SOLO				=> ['6334', '6767'],
		self::UNIONPAY			=> ['622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925'],
		self::VISA				=> ['4'],
		self::MIR				=> ['2200', '2201', '2202', '2203', '2204'],
	];

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_CHECKSUM		=> "Field '{0}' contains an invalid checksum.",
		self::ERROR_CONTENT		=> "Field '{0}' must contain only digits.",
		self::ERROR_LENGTH		=> "Field '{0}' contains an invalid amount of digits.",
		self::ERROR_PREFIX		=> "Field '{0}' is not from an allowed institute.",
		self::ERROR_SERVICE		=> "Field '{0}' seems to be an invalid credit card number.",
		self::ERROR_SERVICEFAILURE=> "Field '{0}' throws an exception while validating the input.",
	];

	/**
	 * Allowed credit card types. If no allowed credit card types defined, 
	 * then all credit card types are allowed automaticly.
	 * Array of string constants like: `\MvcCore\Ext\Forms\Validators\CreditCard::VISA ...`.
	 * @var \string[]
	 */
	protected $allowedTypes = [];

	/**
	 * List of external validation services defined as `callable`s.
	 * Defined functions could be indexed by priority index.
	 * Every `callable` item in array has to accept first argument
	 * as raw user input string, second argument as `\MvcCore\Ext\forms\IField`
	 * instance and third argument configured allowed credit card types strings array 
	 * (if empty array, all types allowed). `Callable` has to return safe 
	 * user input value or `NULL` if value is not allowed.
	 * @var \callable[]
	 */
	protected $externalValidationCallbacks = [];

	/**
	 * Get allowed credit card types. If no allowed credit card types defined, 
	 * then all credit card types are allowed automaticly.
	 * @return \string[]
	 */
	public function & GetAllowedTypes () {
		return $this->allowedTypes;
	}
	
	/**
	 * Set multiple allowed credit card types. If no allowed credit card types defined, 
	 * then all credit card types are allowed automaticly.
	 * This function is dangerous, because it removes all previously defined credit card types to allow.
	 * If you only add another credit card type, use: `$validator->AddAllowedTypes();` instead.
	 * Example: `$validator->SetAllowedTypes(\MvcCore\Ext\Forms\Validators\CreditCard::VISA, ...);`
	 * @param \string[] $allowedTypes,... Allowed credit card types from `\MvcCore\Ext\Forms\Validators\CreditCard::$cardTypes`.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & SetAllowedTypes (/*...$allowedTypes*/) {
		$this->allowedTypes = [];
		foreach (func_get_args() as $allowedType) 
			$this->AddAllowedType($allowedType);
		return $this;
	}
	
	/**
	 * Add multiple allowed credit card types. If no allowed credit card types defined, 
	 * then all credit card types are allowed automaticly.
	 * Example: `$validator->AddAllowedTypes(\MvcCore\Ext\Forms\Validators\CreditCard::VISA, ...);`
	 * @param \string[] $allowedTypes,... Allowed credit card types from `\MvcCore\Ext\Forms\Validators\CreditCard::$cardTypes`.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & AddAllowedTypes (/*...$allowedTypes*/) {
		foreach (func_get_args() as $allowedType) 
			$this->AddAllowedType($allowedType);
		return $this;
	}
	
	/**
	 * Add allowed credit card type. If no allowed credit card types defined, 
	 * then all credit card types are allowed automaticly.
	 * Example: `$validator->AddAllowedType(\MvcCore\Ext\Forms\Validators\CreditCard::VISA);`
	 * @param string $allowedType Allowed credit card type from `\MvcCore\Ext\Forms\Validators\CreditCard::$cardTypes`.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & AddAllowedType ($allowedType) {
		if (in_array($allowedType, static::$cardTypes)) {
			$this->allowedTypes[] = $allowedType;
		} else {
			$this->throwNewInvalidArgumentException(
				'Unknown credit card type to allow `'.$allowedType.'`.'
			);
		}
		return $this;
	}

	/**
	 * Get list of external validation services defined as `callable`s.
	 * Defined functions could be indexed by priority index.
	 * Every `callable` item in array  accepts first argument
	 * as raw user input string, second argument as `\MvcCore\Ext\forms\IField`
	 * instance and third argument configured allowed credit card types strings array 
	 * (if empty array, all types allowed). `Callable` has to return safe 
	 * user input value or `NULL` if value is not allowed.
	 * @return \callable[]
	 */
	public function & GetExternalValidationCallbacks () {
		return $this->externalValidationCallbacks;
	}
	
	/**
	 * Set list of external validation services defined as `callable`s.
	 * Defined functions could be indexed by priority index.
	 * This function is dangerous, because it removes all previously defined external validation callbacks.
	 * If you only add another external validation callback, use: `$validator->AddExternalValidationCallback();` instead.
	 * Every `callable` item in array has to accept first argument
	 * as raw user input string, second argument as `\MvcCore\Ext\forms\IField`
	 * instance and third argument configured allowed credit card types strings array 
	 * (if empty array, all types allowed). `Callable` has to return safe 
	 * user input value or `NULL` if value is not allowed.
	 * @param \callable[] $externalValidationCallbacks,... `Callables accepting as first argument raw user input, second argument as `\MvcCore\Ext\forms\IField` instance and third argument configured allowed credit card types strings array (if empty array, all types allowed). `Callable` has to return safe user input value or `NULL` if value is not allowed.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & SetExternalValidationCallbacks (/*...$externalValidationCallbacks*/) {
		$this->externalValidationCallbacks = [];
		foreach (func_get_args() as $externalValidationCallback) 
			$this->AddExternalValidationCallback($externalValidationCallback, NULL);
		return $this;
	}
	
	/**
	 * Add list of external validation services defined as `callable`s.
	 * Defined functions could be indexed by priority index.
	 * Every `callable` item in array has to accept first argument
	 * as raw user input string, second argument as `\MvcCore\Ext\forms\IField`
	 * instance and third argument configured allowed credit card types strings array 
	 * (if empty array, all types allowed). `Callable` has to return safe 
	 * user input value or `NULL` if value is not allowed.
	 * @param \callable[] $externalValidationCallbacks,... `Callables accepting as first argument raw user input, second argument as `\MvcCore\Ext\forms\IField` instance and third argument configured allowed credit card types strings array (if empty array, all types allowed). `Callable` has to return safe user input value or `NULL` if value is not allowed.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & AddExternalValidationCallbacks (/*...$externalValidationCallbacks*/) {
		foreach (func_get_args() as $externalValidationCallback) 
			$this->AddExternalValidationCallback($externalValidationCallback, NULL);
		return $this;
	}
	
	/**
	 * Add external validation service defined as `callable`.
	 * Defined function could be indexed by priority index.
	 * Given function has to accept first argument as raw user input string, 
	 * second argument as `\MvcCore\Ext\forms\IField` instance and third 
	 * argument configured allowed credit card types strings array 
	 * (if empty array, all types allowed). `Callable` has to return safe 
	 * user input value or `NULL` if value is not allowed.
	 * @param \callable[] $externalValidationCallback `Callable accepting as first argument raw user input, second argument as `\MvcCore\Ext\forms\IField` instance and third argument configured allowed credit card types strings array (if empty array, all types allowed). `Callable` has to return safe user input value or `NULL` if value is not allowed.
	 * @param int|NULL $priorityIndex Default value is `NULL`.
	 * @return \MvcCore\Ext\Forms\Validators\CreditCard
	 */
	public function & AddExternalValidationCallback ($externalValidationCallback, $priorityIndex = NULL) {
		if (is_callable($externalValidationCallback)) {
			if ($priorityIndex !== NULL) {
				if (isset($this->externalValidationCallbacks[$priorityIndex])) {
					array_splice($this->externalValidationCallbacks, $priorityIndex, 0, $externalValidationCallback);
				} else {
					$this->externalValidationCallbacks[$priorityIndex] = $externalValidationCallback;
				}
			} else {
				$this->externalValidationCallbacks[] = $externalValidationCallback;
			}
		} else {
			$this->throwNewInvalidArgumentException(
				'Given external validation callback is not `callable`: `'.$externalValidationCallback.'`.'
			);
		}
		return $this;
	}

	/**
	 * Returns valid credit card number as string if and only if 
	 * `$rawSubmittedValue` follows the Luhn algorithm (mod-10 checksum).
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedValue = (string) $rawSubmittedValue;
		if (!ctype_digit($rawSubmittedValue)) {
			$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_CONTENT));
			return NULL;
		}
		$length = strlen($rawSubmittedValue);
		// if no allowed type has been configured, all types are automaticly allowed
		$allowedTypes  = $this->allowedTypes  
			? $this->allowedTypes 
			: static::$cardTypes;
		$foundedType = FALSE;
		$foundedLength = FALSE;
		foreach ($allowedTypes as $allowedType) {
			foreach (static::$cardPrefixes[$allowedType] as $prefix) {
				if (substr($rawSubmittedValue, 0, strlen($prefix)) == $prefix) {
					$foundedType = TRUE;
					if (in_array($length, static::$cardLengths[$allowedType])) {
						$foundedLength = TRUE;
						break 2;
					}
				}
			}
		}
		if ($foundedType == FALSE) {
			$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_PREFIX));
			return NULL;
		}
		if ($foundedLength == FALSE) {
			$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_LENGTH));
			return NULL;
		}
		$sum	= 0;
		$weight = 2;
		for ($i = $length - 2; $i >= 0; $i--) {
			$digit = $weight * $rawSubmittedValue[$i];
			$sum += floor($digit / 10) + $digit % 10;
			$weight = $weight % 2 + 1;
		}
		if ((10 - $sum % 10) % 10 != $rawSubmittedValue[$length - 1]) {
			$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_CHECKSUM));
			return NULL;
		}
		// validate user input by defined external `callable` functions:
		if ($this->externalValidationCallbacks) {
			foreach ($this->externalValidationCallbacks as $externalValidationCallback) {
				try {
					$rawSubmittedValue = call_user_func_array($externalValidationCallback, [$rawSubmittedValue, $this->field, $allowedTypes]);
					if ($rawSubmittedValue === NULL) {
						$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_SERVICE));
						return NULL;
					}
				} catch (\Exception $e) {
					$debugClass = \MvcCore\Application::GetInstance()->GetDebugClass();
					$debugClass::Log($e, \MvcCore\Interfaces\IDebug::EXCEPTION);
					$this->field->AddValidationError(static::GetErrorMessage(self::ERROR_SERVICEFAILURE));
					return NULL;
				}
			}
		}
		return $rawSubmittedValue;
	}
}
