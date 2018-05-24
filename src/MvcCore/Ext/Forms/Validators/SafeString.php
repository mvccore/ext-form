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

require_once(__DIR__.'/../../Form.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');

/**
 * THIS VALIDATOR DOESN'T MEAN SAFE VALUE TO PREVENT SQL INJECTS!
 * To prevent sql injects - use PDO::prepare and PDO::execute.
 */

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class SafeString extends Core\Validator
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Forms\IField & $field) {

		// remove whitespaces from the beginning ant at the end: SPACE \t \n \r \0 \x0B
		// @see http://php.net/manual/en/function.trim.php
		$submitValue = trim($submitValue);

		// remove ASCII characters from 0 to 31 incl. (first column)
		// @see http://www.asciitable.com/index/asciifull.gif
		$cleanedValue = strtr($submitValue, array(
			"\x00"	=> '',	"\x08"	=> '',	"\x10"	=> '',	"\x18"	=> '',
			"\x01"	=> '',	"\x09"	=> '',	"\x11"	=> '',	"\x19"	=> '',
			"\x02"	=> '',	"\x0A"	=> '',	"\x12"	=> '',	"\x1A"	=> '',
			"\x03"	=> '',	"\x0B"	=> '',	"\x13"	=> '',	"\x1B"	=> '',
			"\x04"	=> '',	"\x0C"	=> '',	"\x14"	=> '',	"\x1C"	=> '',
			"\x05"	=> '',	"\x0D"	=> '',	"\x15"	=> '',	"\x1D"	=> '',
			"\x06"	=> '',	"\x0E"	=> '',	"\x16"	=> '',	"\x1E"	=> '',
			"\x07"	=> '',	"\x0F"	=> '',	"\x17"	=> '',	"\x1F"	=> '',
		));

		if (mb_strlen($cleanedValue) !== mb_strlen($submitValue)) {
			$this->addError($field, Form::$DefaultMessages[Form::INVALID_CHARS], function ($msg, $args) {
				return Core\View::Format($msg, $args);
			});
		}

		// replace characters to entities after all: ' " ` < > \ = ^ | & ~
		$safeValue = strtr($cleanedValue, array(
			"'"  => '&#39;',
			'"'  => '&quot;',
			'`'  => "&#96;",
			'<'  => "&lt;",
			'>'  => "&gt;",
			'\\' => "&#92;",
			'='  => "&#61;",
			'^'  => "&#94;",
			'|'  => "&#124;",
			'&'  => "&amp;",
			'~'  => "&#126;",
		));

		return $safeValue;
	}
}
