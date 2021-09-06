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
	protected static $baseAsciiChars = [
		"\x00"	=> '',	"\x08"	=> '',		"\x10"	=> '',	"\x18"	=> '',
		"\x01"	=> '',	/*"\x09"=> "\t",*/	"\x11"	=> '',	"\x19"	=> '',
		"\x02"	=> '',	/*"\x0A"=> "\n",*/	"\x12"	=> '',	"\x1A"	=> '',
		"\x03"	=> '',	"\x0B"	=> '',		"\x13"	=> '',	"\x1B"	=> '',
		"\x04"	=> '',	"\x0C"	=> '',		"\x14"	=> '',	"\x1C"	=> '',
		"\x05"	=> '',	/*"\x0D"=> "\r",*/	"\x15"	=> '',	"\x1D"	=> '',
		"\x06"	=> '',	"\x0E"	=> '',		"\x16"	=> '',	"\x1E"	=> '',
		"\x07"	=> '',	"\x0F"	=> '',		"\x17"	=> '',	"\x1F"	=> '',
	];

	/**
	 * Validate raw user input, if there are any XSS characters 
	 * or base ASCII characters or characters in this list: | = \ %,
	 * add submit error and return `NULL`.
	 * @param  string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL  Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// remove white spaces from both sides: `SPACE \t \n \r \0 \x0B`:
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		
		// Remove base ASCII characters from 0 to 31 included (first column) except `\n \r \t`:
		$cleanedValue = strtr($rawSubmittedValue, static::$baseAsciiChars);

		if (mb_strlen($cleanedValue) === 0) return NULL;
		
		return $cleanedValue;
	}
}
