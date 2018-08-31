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
 * Responsibility: init, predispatch and render `<input>` HTML element 
 *				   with type `password`. `Password` could have a `Password`
 *				   validator define (not defined by default) to check 
 *				   subbmited value for password strength rules. 
 *				   But raw user password still could contain very dangerous 
 *				   characters for XSS, SQL or any other attacks. Be carefull!!! 
 *				   It doesn't escape enything. It only check if configured 
 *				   character groups are presented and how much and that's all.
 */
class Password extends Text
{
	/**
	 * Possible values: `password`.
	 * @var string
	 */
	protected $type = 'password';

	/**
	 * Validators: 
	 * - `Password` - Validate raw user password by configured password strength rules.
	 * 				  Password still could contain very dangerous characters for XSS, 
	 * 				  SQL or any other attacks. Be carefull!!! This doesn't escape enything.
	 * 				  It only check if configured character groups are presented and how much
	 * 				  and that's all.
	 * @var string[]|\Closure[]
	 */
	protected $validators = [/*'Password', 'SafeString', 'MinLength', 'MaxLength', 'Pattern'*/];
}
