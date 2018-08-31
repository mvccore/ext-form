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
 *				   type `search`. `Search` field has no default validator, only
 *				   validator `SafeString` from parent class `Text`. It replace 
 *				   characters & " \' < > to &amp; &quot; &#039; &lt; &gt;
 *				   Se be carefull if you want to search in database with 
 *				   apostrophe quots, you need to remove `SafeString` validator
 *				   or you need to replace back `&#035;` to `'`, but 
 *				   everytime - you have to use database escaping by `\PDO::prepare()`!
 */
class Search extends Text
{
	/**
	 * Possible values: `search`.
	 * @var string
	 */
	protected $type = 'search';

	/**
	 * Default placeholder text - `Search`.
	 * @var string
	 */
	protected $placeHolder = 'Search';
}
