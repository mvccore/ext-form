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

namespace MvcCore\Ext\Forms;

interface IError {

	/**
	 * Constants used internally and mostly
	 * in validator classes to specify
	 * proper error message index.
	 * @var int
	 */
	const	REQUIRED = 0,
			EMPTY_CONTENT = 1,
			MAX_POST_SIZE = 2,
			CSRF = 3;
}
