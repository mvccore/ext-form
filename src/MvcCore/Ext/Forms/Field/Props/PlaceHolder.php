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
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 */
trait PlaceHolder {

	/**
	 * A hint to the user of what can be entered in the control, typically in the form 
	 * of an example of the type of information that should be entered. The placeholder
	 * text must not contain carriage returns or line-feeds. `NULL` value means no 
	 * placeholder attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-placeholder
	 * @var string|NULL
	 */
	protected $placeHolder = NULL;

	/**
	 * Boolean to translate placeholder text, `TRUE` by default.
	 * @var boolean
	 */
	protected $translatePlaceholder = TRUE;

	/**
	 * Automatically translate `placeHolder` attribute if necessary
	 * in `PreDispatch()` field rendering moment.
	 * @return void
	 */
	protected function preDispatchPlaceHolder () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\PlaceHolder */
		if ($this->translate && $this->placeHolder !== NULL && $this->translatePlaceholder)
			$this->placeHolder = $this->form->Translate($this->placeHolder);
	}

	/**
	 * A hint to the user of what can be entered in the control, typically in the form 
	 * of an example of the type of information that should be entered. The placeholder
	 * text must not contain carriage returns or line-feeds. `NULL` value means no 
	 * placeholder attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-placeholder
	 * @return string|NULL
	 */
	public function GetPlaceHolder () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\PlaceHolder */
		return $this->placeHolder;
	}

	/**
	 * A hint to the user of what can be entered in the control, typically in the form 
	 * of an example of the type of information that should be entered. The placeholder
	 * text must not contain carriage returns or line-feeds. `NULL` value means no 
	 * placeholder attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-placeholder
	 * @param  string|NULL  $placeHolder 
	 * @param  boolean|NULL $translatePlaceholder 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetPlaceHolder ($placeHolder, $translatePlaceholder = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\PlaceHolder */
		$this->placeHolder = $placeHolder;
		if ($translatePlaceholder !== NULL)
			$this->translatePlaceholder = $translatePlaceholder;
		return $this;
	}
}
