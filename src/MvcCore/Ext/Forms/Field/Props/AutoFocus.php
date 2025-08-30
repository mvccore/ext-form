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
 * Trait for all visible fields, for classes:
 * - `\MvcCore\Ext\Forms\Fields\Button`
 *    - `\MvcCore\Ext\Forms\Fields\SubmitButton`
 *    - `\MvcCore\Ext\Forms\Fields\ResetButton`
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * - `\MvcCore\Ext\Forms\Fields\Image`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\ResetInput`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\Fields\SubmitInput`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait AutoFocus {

	/**
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @var ?bool
	 */
	protected $autoFocus = NULL;

	/**
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @return ?bool
	 */
	public function GetAutoFocus () {
		return $this->autoFocus;
	}

	/**
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. If there is already defined any previously configured 
	 * autofocused form field, you can use second argument `$duplicateBehaviour` to solve the problem.
	 * Second argument possible values:
	 * - `0` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION`)
	 *   Default value, an exception is thrown when there is already defined other autofocused form element.
	 * - `1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW`)
	 *   There will be removed previously defined autofocused element and configured new given one.
	 * - `-1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW`)
	 *   There will be quietly configured another field autofocused. Be careful!!! This is not standard behaviour!
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @param  ?bool $autoFocus 
	 * @param  int $duplicateBehaviour
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetAutoFocus ($autoFocus = TRUE, $duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION) {
		$this->autoFocus = $autoFocus;
		if ($autoFocus && $duplicateBehaviour !== \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW) {
			$form = $this->form;
			if ($form === NULL)
				$this->throwNewInvalidArgumentException("Add all fields into form instance first to configure field autofocus property.");
			$form::SetAutoFocusedFormField($form->GetId(), $this->name, $duplicateBehaviour);
		}
		return $this;
	}
}
