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
 * THIS VALIDATOR DOESN'T MEAN SAFE VALUE TO PREVENT SQL INJECTS!
 * To prevent sql injects - use `\PDO::prepare();` and `\PDO::execute()`.
 */

class SafeString extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Base ASCII characters from 0 to 31 incl. (first column).
	 * @see http://www.asciitable.com/index/asciifull.gif
	 * @var \string[]
	 */
	protected static $baseAsciiChars = array(
		"\x00"	=> '',	"\x08"	=> '',	"\x10"	=> '',	"\x18"	=> '',
		"\x01"	=> '',	"\x09"	=> '',	"\x11"	=> '',	"\x19"	=> '',
		"\x02"	=> '',	"\x0A"	=> '',	"\x12"	=> '',	"\x1A"	=> '',
		"\x03"	=> '',	"\x0B"	=> '',	"\x13"	=> '',	"\x1B"	=> '',
		"\x04"	=> '',	"\x0C"	=> '',	"\x14"	=> '',	"\x1C"	=> '',
		"\x05"	=> '',	"\x0D"	=> '',	"\x15"	=> '',	"\x1D"	=> '',
		"\x06"	=> '',	"\x0E"	=> '',	"\x16"	=> '',	"\x1E"	=> '',
		"\x07"	=> '',	"\x0F"	=> '',	"\x17"	=> '',	"\x1F"	=> '',
	);
	/**
	 * Characters to prevent XSS atack and some other special chars
	 * what is definitly not in standard user input.
	 * @var \string[]
	 */
	protected static $specialMeaningChars = array(
		'<'  => "&lt;",
		'>'  => "&gt;",
		'\\' => "&#92;",
		'&'  => "&amp;",
		'='  => "&#61;",
	);

	public function Validate ($submitValue) {
		$result = NULL;

		// remove whitespaces from both sides: `SPACE \t \n \r \0 \x0B`:
		$submitValue = trim($submitValue);

		// Remove base ASCII characters from 0 to 31 incl. (first column):
		$cleanedValue = strtr($submitValue, static::$baseAsciiChars);

		// Replace characters to entities: ' " ` < > \ = ^ | & ~
		$cleanedValue = strtr($cleanedValue, static::$specialMeaningChars);

		if (mb_strlen($cleanedValue) === mb_strlen($submitValue)) {
			$result = $cleanedValue;
		} else {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::INVALID_CHARS)	
			);
		}

		return $result;
	}
}
