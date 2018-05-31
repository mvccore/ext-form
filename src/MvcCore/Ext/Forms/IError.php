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

namespace MvcCore\Ext\Forms;

interface IError
{
	/**
	 * Constants used internaly and mostly
	 * in validator classes to specify
	 * proper error message index.
	 */
	const	EQUAL = 0,
			NOT_EQUAL = 1,
			REQUIRED = 2,
			INVALID_FORMAT = 3,
			INVALID_CHARS = 4,
			EMPTY_CONTENT = 5,
			CSRF = 6,
			// text
			MIN_LENGTH = 7,
			MAX_LENGTH = 8,
			LENGTH = 9,
			EMAIL = 10,
			URL = 11,
			NUMBER = 12,
			INTEGER = 13,
			FLOAT = 14,
			DATE = 15,
			DATE_TO_LOW = 16,
			DATE_TO_HIGH = 17,
			TIME = 18,
			TIME_TO_LOW = 19,
			TIME_TO_HIGH = 20,
			DATETIME = 21,
			PHONE = 22,
			ZIP_CODE = 23,
			TAX_ID = 24,
			VAT_ID = 25,
			GREATER = 26,
			LOWER = 27,
			RANGE = 28,
			DIVISIBLE = 29,
			// file upload
			MAX_FILE_SIZE = 30,
			MAX_POST_SIZE = 31,
			IMAGE = 32,
			MIME_TYPE = 33,
			// other
			VALID = 34,
			CHOOSE_MIN_OPTS = 35,
			CHOOSE_MAX_OPTS = 36,
			CHOOSE_MIN_OPTS_BUBBLE = 37,
			CHOOSE_MAX_OPTS_BUBBLE = 38;
}
