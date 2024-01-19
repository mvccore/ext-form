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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 */
trait DataList {

	/**
	 * `DataList` field unique name.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-list
	 * @var string|NULL
	 */
	protected $list = NULL;

	/**
	 * Get `DataList` field unique name.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-list
	 * @return string|NULL
	 */
	public function GetList () {
		return $this->list;
	}

	/**
	 * Set `DataList` field instance or `DataList` field unique name.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-list
	 * @param  string|\MvcCore\Ext\Forms\Fields\IDataList $dataListNameOrInstance
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetList ($dataListNameOrInstance) {
		if ($dataListNameOrInstance instanceof \MvcCore\Ext\Forms\IField) {
			$this->list = $dataListNameOrInstance->GetName();
		} else {
			$this->list = (string) $dataListNameOrInstance;	
		}
		return $this;
	}
}
