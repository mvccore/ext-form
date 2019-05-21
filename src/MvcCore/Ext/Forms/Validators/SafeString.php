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
 * Responsibility: Validate raw user input as "safe string" to display it in 
 *				   response. Remove from submitted value base ASCII characters 
 *				   from 0 to 31 included (first column) and special characters: 
 *				   `& " ' < > | = \ %`. 
 *				   THIS VALIDATOR DOESN'T MEAN SAFE VALUE TO PREVENT SQL INJECTS! 
 *				   To prevent sql injects - use `\PDO::prepare();` and `\PDO::execute()`.
 */
class SafeString extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Base ASCII characters from 0 to 31 included (first column).
	 * @see http://www.asciitable.com/index/asciifull.gif
	 * @var \string[]
	 */
	protected static $baseAsciiChars = [
		"\x00"	=> '',	"\x08"	=> '',		"\x10"	=> '',	"\x18"	=> '',
		"\x01"	=> '',	"\x09"	=> "\t",	"\x11"	=> '',	"\x19"	=> '',
		"\x02"	=> '',	"\x0A"	=> "\n",	"\x12"	=> '',	"\x1A"	=> '',
		"\x03"	=> '',	"\x0B"	=> '',		"\x13"	=> '',	"\x1B"	=> '',
		"\x04"	=> '',	"\x0C"	=> '',		"\x14"	=> '',	"\x1C"	=> '',
		"\x05"	=> '',	"\x0D"	=> "\r",	"\x15"	=> '',	"\x1D"	=> '',
		"\x06"	=> '',	"\x0E"	=> '',		"\x16"	=> '',	"\x1E"	=> '',
		"\x07"	=> '',	"\x0F"	=> '',		"\x17"	=> '',	"\x1F"	=> '',
	];
	/**
	 * Characters to prevent XSS attack and some other special chars
	 * what could be dangerous user input.
	 * @see http://php.net/manual/en/function.htmlspecialchars.php
	 * @var \string[]
	 */
	protected static $specialMeaningChars = [
		// commented characters are cleaned bellow by `htmlspecialchars()`
		//'&'	=> "&amp;",
		//'"'	=> "&quot;",
		//"'"	=> "&apos;",
		//'<'	=> "&lt;",
		//'>'	=> "&gt;",
		'|'	=> "&#124;",
		'='	=> "&#61;",
		'\\'=> "&#92;",
		'%'	=> "&#37;",
	];

	/**
	 * Validate raw user input, if there are any XSS characters 
	 * or base ASCII characters or characters in this list: | = \ %,
	 * add submit error and return `NULL`.
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// remove white spaces from both sides: `SPACE \t \n \r \0 \x0B`:
		$rawSubmittedValue = trim($rawSubmittedValue);
		
		// Remove base ASCII characters from 0 to 31 included (first column):
		$cleanedValue = strtr($rawSubmittedValue, static::$baseAsciiChars);

		// Replace characters to entities: & " ' < > to &amp; &quot; &#039; &lt; &gt;
		// http://php.net/manual/en/function.htmlspecialchars.php
		$cleanedValue = htmlspecialchars($cleanedValue, ENT_QUOTES);
		
		// Replace characters to entities: | = \ %
		$cleanedValue = strtr($cleanedValue, static::$specialMeaningChars);
		
		return $cleanedValue;
	}
}
