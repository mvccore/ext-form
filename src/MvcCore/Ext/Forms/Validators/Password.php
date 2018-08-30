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

class Password extends \MvcCore\Ext\Forms\Validator
{
	const MIN_LENGTH = 12;
	const MAX_LENGTH = 255;
	const LOWERCASE_CHARS = 'a-z';
	const UPPERCASE_CHARS = 'A-Z';
	const DIGIT_CHARS = '0-9';
	const SPECIAL_CHARS = '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';

    /**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_MUST_HAVE_MIN_LENGTH		= 0;
	const ERROR_MUST_HAVE_MAX_LENGTH		= 1;
	const ERROR_MUST_HAVE_LOWERCASE_CHARS	= 2;
	const ERROR_MUST_HAVE_UPPERCASE_CHARS	= 3;
	const ERROR_MUST_HAVE_DIGIT_CHARS		= 4;
	const ERROR_MUST_HAVE_SPECIAL_CHARS		= 5;

	protected $mustHaveMinLength = self::MIN_LENGTH;
	protected $mustHaveMaxLength = self::MAX_LENGTH;
	protected $mustHaveLowerCaseChars = TRUE;
	protected $mustHaveUpperCaseChars = TRUE;
	protected $mustHaveDigits = TRUE;
	protected $mustHaveSpecialChars = TRUE;
	protected $specialChars = self::SPECIAL_CHARS;

	public function GetMustHaveMinLength () {
		return $this->mustHaveMinLength;
	}
	public function & SetMustHaveMinLength ($mustHaveMinLength = TRUE) {
		$this->mustHaveMinLength = $mustHaveMinLength;
		return $this;
	}
	public function GetMustHaveMaxLength () {
		return $this->mustHaveMaxLength;
	}
	public function & SetMustHaveMaxLength ($mustHaveMaxLength = TRUE) {
		$this->mustHaveMaxLength = $mustHaveMaxLength;
		return $this;
	}
	public function GetMustHaveLowerCaseChars () {
		return $this->mustHaveLowerCaseChars;
	}
	public function & SetMustHaveLowerCaseChars ($mustHaveLowerCaseChars = TRUE) {
		$this->mustHaveLowerCaseChars = $mustHaveLowerCaseChars;
		return $this;
	}
	public function GetMustHaveUpperCaseChars () {
		return $this->mustHaveUpperCaseChars;
	}
	public function & SetMustHaveUpperCaseChars ($mustHaveUpperCaseChars = TRUE) {
		$this->mustHaveUpperCaseChars = $mustHaveUpperCaseChars;
		return $this;
	}
	public function GetMustHaveDigits () {
		return $this->mustHaveDigits;
	}
	public function & SetMustHaveDigits ($mustHaveDigits = TRUE) {
		$this->mustHaveDigits = $mustHaveDigits;
		return $this;
	}
	public function GetMustHaveSpecialChars () {
		return $this->mustHaveSpecialChars;
	}
	public function & SetMustHaveSpecialChars ($mustHaveSpecialChars = TRUE) {
		$this->mustHaveSpecialChars = $mustHaveSpecialChars;
		return $this;
	}
	public function GetSpecialChars	() {
		return $this->specialChars;
	}
	public function & SetSpecialChars	($specialChars = self::SPECIAL_CHARS) {
		$this->specialChars = $specialChars;
		return $this;
	}

	public function __construct (array $cfg = []) {
		foreach ($cfg as $propertyName => $propertyValue) {
			if (!property_exists($this, $propertyName)) {
				$this->throwNewInvalidArgumentException(
					'Property `'.$propertyName.'` is not possible '
					.'to configure by constructor `$cfg` param. '
					. 'There is only possible to configure properties: `'
					. implode('`, `', [
						'mustHaveMinLength:int',
						'mustHaveMaxLength:int',
						'mustHaveLowerCaseChars:bool',
						'mustHaveUpperCaseChars:bool',
						'mustHaveDigits:bool',
						'mustHaveSpecialChars:bool',
						'specialChars:string',
					]) . '`.'
				);
			} else {
				settype($propertyValue, gettype($this->{$propertyName}));
				$this->{$propertyName} = $propertyValue;
			}
		}
	}

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_MUST_HAVE_MIN_LENGTH		=> "Password must have a minimum length of {1} characters.",
		self::ERROR_MUST_HAVE_MAX_LENGTH		=> "Password must have a maximum length of {1} characters.",
		self::ERROR_MUST_HAVE_LOWERCASE_CHARS	=> "Password must contain lowercase characters ({1}).",
		self::ERROR_MUST_HAVE_UPPERCASE_CHARS	=> "Password must contain uppercase characters ({1}).",
		self::ERROR_MUST_HAVE_DIGIT_CHARS		=> "Password must contain digits ({1}).",
		self::ERROR_MUST_HAVE_SPECIAL_CHARS		=> "Password must contain special characters ( {1} ).",
	];

	/**
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$password = trim((string) $rawSubmittedValue);
		$passwordLength = mb_strlen($password);

		if ($passwordLength < $this->mustHaveMinLength) 
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MUST_HAVE_MIN_LENGTH),
				[static::ERROR_MUST_HAVE_MIN_LENGTH]
			);

		if ($passwordLength > $this->mustHaveMaxLength) {
			$password = mb_substr($password, 0, static::ERROR_MUST_HAVE_MAX_LENGTH);
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MUST_HAVE_MAX_LENGTH),
				[static::ERROR_MUST_HAVE_MAX_LENGTH]
			);
		}

		if ($this->mustHaveLowerCaseChars && !preg_match('/[a-z]/', $password)) 
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MUST_HAVE_LOWERCASE_CHARS),
				['[a-z]']
			);

		if ($this->mustHaveUpperCaseChars && !preg_match('/[A-Z]/', $password)) 
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MUST_HAVE_UPPERCASE_CHARS),
				['[A-Z]']
			);

		if ($this->mustHaveDigits && !preg_match('/[0-9]/', $password)) 
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MUST_HAVE_DIGIT_CHARS),
				['[0-9]']
			);

		if ($this->mustHaveSpecialChars) {
			$specialCharsArr = str_split($this->specialChars);
			$passwordCharsArr = str_split($password);
			$specialCharsInPassword = array_intersect($passwordCharsArr, $specialCharsArr);
			if (count($specialCharsInPassword) === 0) 
				$this->field->AddValidationError(
					static::GetErrorMessage(static::ERROR_MUST_HAVE_SPECIAL_CHARS),
					[htmlspecialchars($this->specialChars, ENT_QUOTES)]
				);
		}

		return $password;
	}
}
