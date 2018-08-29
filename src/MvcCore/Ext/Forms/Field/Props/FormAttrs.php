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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Image`
 * - `\MvcCore\Ext\Forms\Fields\SubmitButton`
 * - `\MvcCore\Ext\Forms\Fields\SubmitInput`
 */
trait FormAttrs
{
	/**
	 * The URL that processes the data submitted by the input element,
	 * if it is a submit button or image. This attribute overrides the
	 * `action` attribute of the element's form owner.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formaction
	 * @var string|NULL
	 */
	protected $formAction = NULL;

	/**
	 * If the input element is a submit button or image, this attribute
	 * specifies how the form values will be encoded 
	 * to send them to the server. Possible values are:
	 * - `application/x-www-form-urlencoded`
	 *   By default, it means all form values will be encoded to 
	 *   `key1=value1&key2=value2...` string.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED`.
	 * - `multipart/form-data`
	 *   Data will not be encoded to url string form, this value is required,
	 *   when you are using forms that have a file upload control. 
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART`.
	 * - `text/plain`
	 *   Spaces will be converted to `+` symbols, but no other special 
	 *   characters will be encoded.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_PLAINTEXT`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @var string|NULL
	 */
	protected $formEnctype = NULL;
	
	/**
	 * If the input element is a submit button or image, this attribute 
	 * specifies the HTTP method that the browser uses to submit the form.
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @var string|NULL
	 */
	protected $formMethod = NULL;
	
	/**
	 * If the input element is a submit button or image, this Boolean attribute 
	 * specifies that the form shouldn't be validated before submission. This 
	 * attribute overrides the novalidate attribute of the element's form owner.
	 * It means there will be no validation on client side, but there is always 
	 * validation on server side.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @var string|NULL
	 */
	protected $formNoValidate = NULL;
	
	/**
	 * If the input element is a submit button or image, this attribute is 
	 * a name or keyword indicating where to display the response that is 
	 * received by submitting the form. This is a name of, or keyword for, 
	 * a browsing context (e.g. tab, window, or inline frame). This attribute
	 * overrides the target attribute of the elements's form owner. 
	 * The following keywords have special meanings:
	 * - `_self`:		Load the response into the same browsing context as the 
	 *					current one. This value is the default if the attribute 
	 *					is not specified.
	 * - `_blank`:		Load the response into a new unnamed browsing context.
	 * - `_parent`:		Load the response into the parent browsing context of 
	 *					the current one. If there is no parent, this option 
	 *					behaves the same way as `_self`.
	 * - `_top`:		Load the response into the top-level browsing context 
	 *					(i.e. the browsing context that is an ancestor of the 
	 *					current one, and has no parent). If there is no parent, 
	 *					this option behaves the same way as `_self`.
	 * - `iframename`:	The response is displayed in a named `<iframe>`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget
	 * @var string|NULL
	 */
	protected $formTarget = NULL;
	
	/**
	 * Get the URL that processes the data submitted by the input element,
	 * if it is a submit button or image. This attribute overrides the
	 * `action` attribute of the element's form owner.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formaction
	 * @return string|NULL
	 */
	public function GetFormAction () {
		return $this->formAction;
	}

	/**
	 * Set the URL that processes the data submitted by the input element,
	 * if it is a submit button or image. This attribute overrides the
	 * `action` attribute of the element's form owner.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formaction
	 * @param string $formAction 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormAction ($formAction) {
		$this->formAction = $formAction;
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute
	 * specifies how the form values will be encoded 
	 * to send them to the server. Possible values are:
	 * - `application/x-www-form-urlencoded`
	 *   By default, it means all form values will be encoded to 
	 *   `key1=value1&key2=value2...` string.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED`.
	 * - `multipart/form-data`
	 *   Data will not be encoded to url string form, this value is required,
	 *   when you are using forms that have a file upload control. 
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART`.
	 * - `text/plain`
	 *   Spaces will be converted to `+` symbols, but no other special 
	 *   characters will be encoded.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_PLAINTEXT`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @return string|NULL
	 */
	public function GetFormEnctype () {
		return $this->formEnctype;
	}

	/**
	 * If the input element is a submit button or image, this attribute
	 * specifies how the form values will be encoded 
	 * to send them to the server. Possible values are:
	 * - `application/x-www-form-urlencoded`
	 *   By default, it means all form values will be encoded to 
	 *   `key1=value1&key2=value2...` string.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED`.
	 * - `multipart/form-data`
	 *   Data will not be encoded to url string form, this value is required,
	 *   when you are using forms that have a file upload control. 
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART`.
	 * - `text/plain`
	 *   Spaces will be converted to `+` symbols, but no other special 
	 *   characters will be encoded.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_PLAINTEXT`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @param string $formEnctype 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormEnctype ($formEnctype) {
		$this->formEnctype = $formEnctype;
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute 
	 * specifies the HTTP method that the browser uses to submit the form.
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @return string|NULL
	 */
	public function GetFormMethod () {
		return $this->formMethod;
	}

	/**
	 * If the input element is a submit button or image, this attribute 
	 * specifies the HTTP method that the browser uses to submit the form.
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values for `$formMethod` param: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @param string $formMethod 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormMethod ($formMethod) {
		$this->formMethod = $formMethod;
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this Boolean attribute 
	 * specifies that the form shouldn't be validated before submission. This 
	 * attribute overrides the novalidate attribute of the element's form owner.
	 * It means there will be no validation on client side, but there is always 
	 * validation on server side. Only `TRUE` renders the form attribute.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @return string|NULL
	 */
	public function GetFormNoValidate () {
		return $this->formNoValidate;
	}

	/**
	 * If the input element is a submit button or image, this Boolean attribute 
	 * specifies that the form shouldn't be validated before submission. This 
	 * attribute overrides the novalidate attribute of the element's form owner.
	 * It means there will be no validation on client side, but there is always 
	 * validation on server side. Only `TRUE` renders the form attribute.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @param bool|NULL $formNoValidate Only `TRUE` renders the form attribute.
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormNoValidate ($formNoValidate = TRUE) {
		$this->formNoValidate = $formNoValidate;
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute is 
	 * a name or keyword indicating where to display the response that is 
	 * received by submitting the form. This is a name of, or keyword for, 
	 * a browsing context (e.g. tab, window, or inline frame). This attribute
	 * overrides the target attribute of the elements's form owner. 
	 * The following keywords have special meanings:
	 * - `_self`:		Load the response into the same browsing context as the 
	 *					current one. This value is the default if the attribute 
	 *					is not specified.
	 * - `_blank`:		Load the response into a new unnamed browsing context.
	 * - `_parent`:		Load the response into the parent browsing context of 
	 *					the current one. If there is no parent, this option 
	 *					behaves the same way as `_self`.
	 * - `_top`:		Load the response into the top-level browsing context 
	 *					(i.e. the browsing context that is an ancestor of the 
	 *					current one, and has no parent). If there is no parent, 
	 *					this option behaves the same way as `_self`.
	 * - `iframename`:	The response is displayed in a named `<iframe>`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget
	 * @return string|NULL
	 */
	public function GetFormTarget () {
		return $this->formTarget;
	}

	/**
	 * If the input element is a submit button or image, this attribute is 
	 * a name or keyword indicating where to display the response that is 
	 * received by submitting the form. This is a name of, or keyword for, 
	 * a browsing context (e.g. tab, window, or inline frame). This attribute
	 * overrides the target attribute of the elements's form owner. 
	 * The following keywords have special meanings:
	 * - `_self`:		Load the response into the same browsing context as the 
	 *					current one. This value is the default if the attribute 
	 *					is not specified.
	 * - `_blank`:		Load the response into a new unnamed browsing context.
	 * - `_parent`:		Load the response into the parent browsing context of 
	 *					the current one. If there is no parent, this option 
	 *					behaves the same way as `_self`.
	 * - `_top`:		Load the response into the top-level browsing context 
	 *					(i.e. the browsing context that is an ancestor of the 
	 *					current one, and has no parent). If there is no parent, 
	 *					this option behaves the same way as `_self`.
	 * - `iframename`:	The response is displayed in a named `<iframe>`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget
	 * @param string $formTarget 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormTarget ($formTarget) {
		$this->formTarget = $formTarget;
		return $this;
	}
}
