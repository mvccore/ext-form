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
 * Responsibility - define getters and setters for field properties: `minLength` and `maxLength`.
 */
interface IMinMaxLength
{
    /**
	 * Get minimum characters length. Default value is `NULL`.
	 * @return int|NULL
	 */
	public function GetMinLength ();

	/**
	 * Set minimum characters length. Default value is `NULL`.
	 * @param int|NULL $minLength 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetMinLength ($minLength);
	
	/**
	 * Get maximum characters length. Default value is `NULL`.
	 * @return int|NULL
	 */
	public function GetMaxLength ();

	/**
	 * Set maximum characters length. Default value is `NULL`.
	 * @param int|NULL $minLength 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxLength ($maxLength);
}
