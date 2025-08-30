<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Validators;

/**
 * Responsibility: Validate raw user input as "safe string" to display it in 
 *                 response. Remove from submitted value base ASCII characters 
 *                 from 0 to 31 included (first column) and special characters: 
 *                 `& " ' < > | = \ %`. 
 *                 THIS VALIDATOR DOESN'T MEAN SAFE VALUE TO PREVENT SQL INJECTS! 
 *                 To prevent sql injects - use `\PDO::prepare();` and `\PDO::execute()`.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SafeString extends \MvcCore\Ext\Forms\Validator {

	/**
	 * Base ASCII characters from 0 to 31 included (first column).
	 * @see http://www.asciitable.com/index/asciifull.gif
	 * @var \string[]
	 */
	protected static $chars2RemoveDefault = [
		"\x00",	"\x08",	"\x10",	"\x18",
		"\x01",	"\x09",	"\x11",	"\x19",
		"\x02",	"\x0A",	"\x12",	"\x1A",
		"\x03",	"\x0B",	"\x13",	"\x1B",
		"\x04",	"\x0C",	"\x14",	"\x1C",
		"\x05",	"\x0D",	"\x15",	"\x1D",
		"\x06",	"\x0E",	"\x16",	"\x1E",
		"\x07",	"\x0F",	"\x17",	"\x1F",
	];


	/**
	 * Boolean to remove new line chars (`\r` or `\n`) from submitted value. 
	 * `FALSE` by default.
	 * @var bool
	 */
	protected $removeNewLines	= FALSE;
	
	/**
	 * Boolean to remove tabs (`\t`) from submitted value. 
	 * `FALSE` by default.
	 * @var bool
	 */
	protected $removeTabs		= FALSE;

	/**
	 * Set of characters to remove from submitted value.
	 * @internal
	 * @var array
	 */
	protected $chars2Remove		= [];

	/**
	 * Get boolean to remove new line chars (`\r` or `\n`) from submitted value. 
	 * @return bool
	 */
	public function GetRemoveNewLines () {
		return $this->removeNewLines;
	}

	/**
	 * Set boolean to remove new line chars (`\r` or `\n`) from submitted value. 
	 * @param  bool $removeTabs
	 * @return \MvcCore\Ext\Forms\Validators\SafeString
	 */
	public function SetRemoveNewLines ($removeNewLines) {
		$this->removeNewLines = $removeNewLines;
		return $this;
	}

	/**
	 * Get boolean to remove tabs (`\t`) from submitted value. 
	 * @return bool
	 */
	public function GetRemoveTabs () {
		return $this->removeTabs;
	}

	/**
	 * Set boolean to remove tabs (`\t`) from submitted value. 
	 * @param  bool $removeTabs
	 * @return \MvcCore\Ext\Forms\Validators\SafeString
	 */
	public function SetRemoveTabs ($removeTabs) {
		$this->removeTabs = $removeTabs;
		return $this;
	}


	/**
	 * Create safe string validator instance.
	 * 
	 * @param  array $cfg
	 * Config array with protected properties and it's 
	 * values which you want to configure, presented 
	 * in camel case properties names syntax.
	 * 
	 * @param  bool  $removeNewLines
	 * Boolean to remove new line chars (`\r` or `\n`) from submitted value. 
	 * `FALSE` by default.
	 * @param  bool  $removeTabs
	 * Boolean to remove tabs (`\t`) from submitted value. 
	 * `FALSE` by default.
	 * 
	 * @throws \InvalidArgumentException 
	 * @return void
	 */
	public function __construct(
		array $cfg = [],
		$removeNewLines = FALSE,
		$removeTabs = FALSE
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		$chars2Remove = array_fill_keys(static::$chars2RemoveDefault, '');
		if ($this->removeNewLines) {
			$chars2Remove["\x0D"] = ''; // \r
			$chars2Remove["\x0A"] = ''; // \n
		} else {
			unset($chars2Remove["\x0D"]); // \r
			unset($chars2Remove["\x0A"]); // \n
		}
		if ($this->removeTabs) {
			$chars2Remove["\x09"] = ''; // \t
		} else {
			unset($chars2Remove["\x09"]); // \t
		}
		$this->chars2Remove = $chars2Remove;
	}

	/**
	 * Validate raw user input, if there are any XSS characters 
	 * or base ASCII characters or characters in this list: | = \ %,
	 * add submit error and return `NULL`.
	 * @param  string|array<string> $rawSubmittedValue Raw submitted value from user.
	 * @return ?string  Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		if ($rawSubmittedValue === NULL) return NULL;

		// remove white spaces from both sides: `SPACE \t \n \r \0 \x0B`:
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		
		// Remove base ASCII characters from 0 to 31 included (first column) except `\n \r \t`:
		$cleanedValue = strtr($rawSubmittedValue, $this->chars2Remove);

		if (mb_strlen($cleanedValue) === 0) return NULL;
		
		return $cleanedValue;
	}
}
