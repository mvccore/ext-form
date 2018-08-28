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

namespace MvcCore\Ext\Forms\Field\Attrs;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 */
trait SpellCheck
{
	/**
	 * Setting the value of this attribute to `TRUE` indicates that the element needs
	 * to have its spelling and grammar checked. The value `default` indicates that 
	 * the element is to act according to a default behavior, possibly based on the 
	 * parent element's own spellcheck value. The value `FALSE indicates that the 
	 * element should not be checked. Possible values are strings: `default`, `true` or `false`. 
	 * Value `NULL` means to not render any attribute in HTML.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-spellcheck
	 * @var string|NULL
	 */
	protected $spellCheck = NULL;
	
	/**
	 * Setting the value of this attribute to `TRUE` indicates that the element needs
	 * to have its spelling and grammar checked. The value `default` indicates that 
	 * the element is to act according to a default behavior, possibly based on the 
	 * parent element's own spellcheck value. The value `FALSE indicates that the 
	 * element should not be checked. Possible values are strings: `default`, `true` or `false`. 
	 * Value `NULL` means to not render any attribute in HTML.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-spellcheck
	 * @return string|NULL Values could be `default`, `true` or `false`. Value `NULL` means to not render any attribute in HTML.
	 */
	public function GetSpellCheck () {
		return $this->spellCheck;
	}

	/**
	 * Setting the value of this attribute to `TRUE` indicates that the element needs
	 * to have its spelling and grammar checked. The value `default` indicates that 
	 * the element is to act according to a default behavior, possibly based on the 
	 * parent element's own spellcheck value. The value `FALSE indicates that the 
	 * element should not be checked. Possible values are strings: `default`, `true` or `false`. 
	 * Value `NULL` means to not render any attribute in HTML.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-spellcheck
	 * @param string|bool|NULL $spellCheck Values could be strings: `default`, `true` or `false` or boolean values `TRUE` or `FALSE`. Value `NULL` means to not render any attribute in HTML.
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetSpellCheck ($spellCheck) {
		if ($spellCheck === TRUE) {
			$spellCheck = 'true';
		} else if ($spellCheck === FALSE) {
			$spellCheck = 'false';
		}
		$this->spellCheck = $spellCheck;
		return $this;
	}
}
