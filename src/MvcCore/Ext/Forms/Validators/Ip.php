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

/**
 * @see https://github.com/zendframework/zend-validator/blob/master/src/Ip.php
 */
class Ip extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_IP = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_IP	=> "Field '{0}' requires a valid IP address. Allowed types: {1}.",
	];

	/**
	 * Allow IPv4 validation in octet format.
	 * Default value is `TRUE`.
	 * @var bool
	 */
	protected $allowIPv4OctetFormat = TRUE;

	/**
	 * Allow IPv4 validation in hexadecimal format.
	 * Default value is `FALSE`.
	 * @var bool
	 */
	protected $allowIPv4HexFormat = FALSE;

	/**
	 * Allow IPv4 validation in binary format.
	 * Default value is `FALSE`.
	 * @var bool
	 */
	protected $allowIPv4BinaryFormat = FALSE;
	
	/**
	 * Allow IPv6 validation.
	 * Default value is `TRUE`.
	 * @var bool
	 */
	protected $allowIPv6 = TRUE;

	/**
	 * Allow IPV6 literals.
	 * Default value is `FALSE`.
	 * @see https://en.wikipedia.org/wiki/IPv6_address#Literal_IPv6_addresses_in_network_resource_identifiers
	 * @var mixed
	 */
	protected $allowIPv6Literals = FALSE;

	/**
	 * Get `TRUE`, if validation for IPv4 address is allowed in octet format.
	 * Octet format is `0-255.0-255.0-255.0-255`. Default value is `TRUE`.
	 * @return bool
	 */
	public function GetAllowIPv4OctetFormat () {
		return $this->allowIPv4OctetFormat;
	}

	/**
	 * Set `TRUE` to allow IPv4 address validation in octet format.
	 * Octet format is `0-255.0-255.0-255.0-255`. Default value is `TRUE`.
	 * @param bool $allowIPv4OctetFormat
	 * @return \MvcCore\Ext\Forms\Validators\Ip
	 */
	public function & SetAllowIPv4OctetFormat ($allowIPv4OctetFormat = TRUE) {
		$this->allowIPv4OctetFormat = $allowIPv4OctetFormat;
		return $this;
	}

	/**
	 * Get `TRUE`, if validation for IPv4 address is allowed in hexadecimal format.
	 * Hexadecimal format is `00-FF.00-FF.00-FF.00-FF`. Default value is `FALSE`.
	 * @return bool
	 */
	public function GetAllowIPv4HexFormat () {
		return $this->allowIPv4HexFormat;
	}

	/**
	 * Set `TRUE` to allow IPv4 address validation in hexadecimal format.
	 * Hexadecimal format is `00-FF.00-FF.00-FF.00-FF`. Default value is `FALSE`.
	 * @param bool $allowIPv4HexFormat
	 * @return \MvcCore\Ext\Forms\Validators\Ip
	 */
	public function & SetAllowIPv4HexFormat ($allowIPv4HexFormat = TRUE) {
		$this->allowIPv4HexFormat = $allowIPv4HexFormat;
		return $this;
	}

	/**
	 * Get `TRUE`, if validation for IPv4 address is allowed in binary format. Binary format is:
	 * `00000000-11111111.00000000-11111111.00000000-11111111.00000000-11111111`. 
	 * Default value is `FALSE`.
	 * @return bool
	 */
	public function GetAllowIPv4BinaryFormat () {
		return $this->allowIPv4BinaryFormat;
	}

	/**
	 * Set `TRUE` to allow IPv4 address validation in binary format.Binary format is:
	 * `00000000-11111111.00000000-11111111.00000000-11111111.00000000-11111111`. 
	 * Default value is `FALSE`.
	 * @param bool $allowIPv4BinaryFormat
	 * @return \MvcCore\Ext\Forms\Validators\Ip
	 */
	public function & SetAllowIPv4BinaryFormat ($allowIPv4BinaryFormat = TRUE) {
		$this->allowIPv4BinaryFormat = $allowIPv4BinaryFormat;
		return $this;
	}

	/**
	 * Get `TRUE`, if validation of IPv6 is allowed.
	 * Default value is `TRUE`.
	 * @return bool
	 */
	public function GetAllowIPv6 () {
		return $this->allowIPv6;
	}

	/**
	 * Set `TRUE` to allow IPv6 validation.
	 * Default value is `TRUE`.
	 * @param bool $allowIPv6
	 * @return \MvcCore\Ext\Forms\Validators\Ip
	 */
	public function & SetAllowIPv6 ($allowIPv6 = TRUE) {
		$this->allowIPv6 = $allowIPv6;
		return $this;
	}

	/**
	 * Get `TRUE` if IPV6 validation allows literals.
	 * Default value is `FALSE`.
	 * @see https://en.wikipedia.org/wiki/IPv6_address#Literal_IPv6_addresses_in_network_resource_identifiers
	 * @return bool
	 */
	public function GetAllowIPv6Literals () {
		return $this->allowIPv6Literals;
	}

	/**
	 * Set `TRUE` to allow literals in IPV6 validation.
	 * Default value is `FALSE`.
	 * @see https://en.wikipedia.org/wiki/IPv6_address#Literal_IPv6_addresses_in_network_resource_identifiers
	 * @param bool $allowIPv6Literals
	 * @return \MvcCore\Ext\Forms\Validators\Ip
	 */
	public function & SetAllowIPv6Literals ($allowIPv6Literals = TRUE) {
		$this->allowIPv6Literals = $allowIPv6Literals;
		return $this;
	}

	/**
	 * Validate raw user input with maximum string length check.
	 * @param string|array $submitValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = NULL;
		$rawSubmittedValue = (string) $rawSubmittedValue;
		$ipv4Allowed = $this->allowIPv4HexFormat || $this->allowIPv4OctetFormat || $this->allowIPv4BinaryFormat;
		if ($ipv4Allowed) {
			$result = $this->validateIPv4($rawSubmittedValue);
			if ($result !== NULL) return $result;
		}
		if ($this->allowIPv6) {
			if ($this->allowIPv6Literals) {
				if (preg_match('/^\[(.*)\]$/', $rawSubmittedValue, $matches)) 
					$rawSubmittedValue = $matches[1];
			}
			$result = $this->validateIPv6($rawSubmittedValue);
			if ($result !== NULL) return $result;
		}
		if ($result === NULL) {
			$errorMsgAllowedTypes = [];
			if ($ipv4Allowed) {
				$ipv4AllowedFormats = [];
				if ($this->allowIPv4OctetFormat)	$ipv4AllowedFormats[] = 'octet';
				if ($this->allowIPv4HexFormat)		$ipv4AllowedFormats[] = 'hexadecimal';
				if ($this->allowIPv4BinaryFormat)	$ipv4AllowedFormats[] = 'binary';
				$errorMsgAllowedTypes[] = 'IPv4 (formats: ' . implode(', ', $ipv4AllowedFormats) . ')';
				if ($this->allowIPv6) 
					$errorMsgAllowedTypes[] = 'IPv6' . ($this->allowIPv6Literals ? ' (with literals)' : '');
				
			}
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_IP),
				[implode(', ', $errorMsgAllowedTypes)]
			);
		}
		return $result;
	}

	/**
	 * Validate IPv4 address format.
	 * @param string $rawSubmittedValue 
	 * @return string|NULL
	 */
	protected function validateIPv4 ($rawSubmittedValue) {
		if ($this->allowIPv4BinaryFormat && preg_match('/^([01]{8}\.){3}[01]{8}\z/i', $rawSubmittedValue)) {
			// binary format: 00000000.00000000.00000000.00000000
			$rawSubmittedValue = bindec(substr($rawSubmittedValue, 0, 8))
				. '.' . bindec(substr($rawSubmittedValue, 9, 8))
				. '.' . bindec(substr($rawSubmittedValue, 18, 8))
				. '.' . bindec(substr($rawSubmittedValue, 27, 8));
		} elseif ($this->allowIPv4OctetFormat && preg_match('/^([0-9]{3}\.){3}[0-9]{3}\z/i', $rawSubmittedValue)) {
			// octet format: 777.777.777.777
			$rawSubmittedValue = (int) substr($rawSubmittedValue, 0, 3)
				. '.' . (int) substr($rawSubmittedValue, 4, 3)
				. '.' . (int) substr($rawSubmittedValue, 8, 3)
				. '.' . (int) substr($rawSubmittedValue, 12, 3);
		} elseif ($this->allowIPv4HexFormat && preg_match('/^([0-9a-f]{2}\.){3}[0-9a-f]{2}\z/i', $rawSubmittedValue)) {
			// hex format: ff.ff.ff.ff
			$rawSubmittedValue = hexdec(substr($rawSubmittedValue, 0, 2)) 
				. '.' . hexdec(substr($rawSubmittedValue, 3, 2))
				. '.' . hexdec(substr($rawSubmittedValue, 6, 2))
				. '.' . hexdec(substr($rawSubmittedValue, 9, 2));
		}
		$ip2long = ip2long($rawSubmittedValue);
		if ($ip2long === FALSE) 
			return NULL;
		if ($rawSubmittedValue == long2ip($ip2long)) {
			return $rawSubmittedValue;
		}
		return NULL;
	}

	/**
	 * Validate IPv6 address format.
	 * @param string $rawSubmittedValue 
	 * @return string|NULL
	 */
	protected function validateIPv6 ($rawSubmittedValue) {
		if (strlen($rawSubmittedValue) < 3) {
			if ($rawSubmittedValue == '::') {
				return '::';
			} else {
				return NULL;
			}
		}
		if (strpos($rawSubmittedValue, '.')) {
			$lastColon = strrpos($rawSubmittedValue, ':');
			$validatedIPv4 = $this->validateIPv4(substr($rawSubmittedValue, $lastColon + 1));
			if (!($lastColon && $validatedIPv4)) 
				return NULL;
			$rawSubmittedValue = substr($rawSubmittedValue, 0, $lastColon) . ':0:0';
		}
		if (strpos($rawSubmittedValue, '::') === FALSE) {
			if (preg_match('/\A(?:[a-f0-9]{1,4}:){7}[a-f0-9]{1,4}\z/i', $rawSubmittedValue))
				return $rawSubmittedValue;
		}
		$colonCount = substr_count($rawSubmittedValue, ':');
		if ($colonCount < 8) {
			if (preg_match('/\A(?::|(?:[a-f0-9]{1,4}:)+):(?:(?:[a-f0-9]{1,4}:)*[a-f0-9]{1,4})?\z/i', $rawSubmittedValue)) 
				return $rawSubmittedValue;
		}
		if ($colonCount == 8) {
			if (preg_match('/\A(?:::)?(?:[a-f0-9]{1,4}:){6}[a-f0-9]{1,4}(?:::)?\z/i', $rawSubmittedValue)) 
				return $rawSubmittedValue;
		}
		return NULL;
	}

}
