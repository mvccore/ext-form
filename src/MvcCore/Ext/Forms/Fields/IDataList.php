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
 * Responsibility - define getters and setters for HTML attribute `list`, 
 * where has to be string targeting to `<datalist>` `id` attribute value.
 */
interface IDataList
{
	/**
	 * Get element `list` attribute value - the `<list>` element `id` attribute value.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-list
	 * @return string|NULL
	 */
	public function & GetList ();

	/**
	 * Set element `list` attribute value - the `<list>` 
	 * element `id` attribute value or `DataList` object instance.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-list
	 * @param string|\MvcCore\Ext\Forms\IField $dataListIdOrInstance
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetList ($dataListIdOrInstance);
}
