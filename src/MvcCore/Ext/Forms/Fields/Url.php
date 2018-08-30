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

namespace MvcCore\Ext\Forms\Fields;

class Url extends Text
{
	/**
	 * Possible values: `url`.
	 * @var string
	 */
	protected $type = 'url';

	/**
	 * Validators: 
	 * - `Url` - to check url format by PHP `filter_var($url, FILTER_VALIDATE_URL)`.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['Url'/*, 'SafeString', 'MinLength', 'MaxLength', 'Pattern'*/];
}
