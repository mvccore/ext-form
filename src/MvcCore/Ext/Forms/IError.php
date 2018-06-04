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
	const	REQUIRED = 0,
			EMPTY_CONTENT = 1,
			CSRF = 2,
			// dates
			DATE = 3,
			DATE_TO_LOW = 4,
			DATE_TO_HIGH = 5,
			TIME = 6,
			TIME_TO_LOW = 7,
			TIME_TO_HIGH = 8,
			DATETIME = 9,
			// number, range...
			GREATER = 10,
			LOWER = 11,
			RANGE = 12,
			DIVISIBLE = 13,
			// file upload
			MAX_FILE_SIZE = 14,
			MAX_POST_SIZE = 15,
			IMAGE = 16,
			MIME_TYPE = 17,
			// other
			CHOOSE_MIN_OPTS = 18,
			CHOOSE_MAX_OPTS = 19,
			CHOOSE_MIN_OPTS_BUBBLE = 20,
			CHOOSE_MAX_OPTS_BUBBLE = 21;
}
