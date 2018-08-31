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

/**
 * Responsibility: init, predispatch and render `<input>` HTML element with 
 *				   type `email`. `Tel` field has it's own validator to check 
 *				   raw submitted value only by `preg_match("#[^0-9\+]#", '', $tel);`.
 */
class Tel extends Text
{
	/**
	 * Possible values: `tel`.
	 * @var string
	 */
	protected $type = 'tel';

	/**
	 * Validators: 
	 * - `Tel` - to simply check phone by PHP `preg_match("#[^0-9\+]#", '', $tel);`.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['Tel'/*, 'SafeString', 'MinLength', 'MaxLength', 'Pattern'*/];
}
