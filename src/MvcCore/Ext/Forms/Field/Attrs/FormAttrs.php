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
 * - \MvcCore\Ext\Forms\Fields\Image
 * - \MvcCore\Ext\Forms\Fields\SubmitButton
 * - \MvcCore\Ext\Forms\Fields\SubmitInput
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
	 * specifies the content encoding that is used to submit the form 
	 * data to the server. Possible values:
	 * 
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @var string|NULL
	 */
	protected $formEnctype = NULL;
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @var string|NULL
	 */
	protected $formMethod = NULL;
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @var string|NULL
	 */
	protected $formNoValidate = NULL;
	
	/**
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
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @return string|NULL
	 */
	public function GetFormEnctype () {
		return $this->formEnctype;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype
	 * @param string $formEnctype 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormEnctype ($formEnctype) {
		$this->formEnctype = $formEnctype;
		return $this;
	}
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @return string|NULL
	 */
	public function GetFormMethod () {
		return $this->formMethod;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod
	 * @param string $formMethod 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormMethod ($formMethod) {
		$this->formMethod = $formMethod;
		return $this;
	}
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @return string|NULL
	 */
	public function GetFormNoValidate () {
		return $this->formNoValidate;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate
	 * @param string $formNoValidate 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormNoValidate ($formNoValidate) {
		$this->formNoValidate = $formNoValidate;
		return $this;
	}
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget
	 * @return string|NULL
	 */
	public function GetFormTarget () {
		return $this->formTarget;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget
	 * @param string $formTarget 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormTarget ($formTarget) {
		$this->formTarget = $formTarget;
		return $this;
	}
}
