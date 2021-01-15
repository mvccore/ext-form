<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: init, pre-dispatch and render `<datalist>` HTML element 
 *				   with given options, optionally translated. This field 
 *				   has no possible value to submit. It just renders the
 *				   `<datalist>` with given options.
 */
class		DataList 
extends		\MvcCore\Ext\Forms\Field 
implements	\MvcCore\Ext\Forms\Fields\IOptions {

	use \MvcCore\Ext\Forms\Field\Props\Options;
	
	/**
	 * Possible value: `data-list`, not used in HTML code for this field.
	 * @var string
	 */
	protected $type = 'data-list';

	/**
	 * Translate given options or not.
	 * @var bool|NULL
	 */
	protected $translateOptions = NULL;

	/**
	 * No templates needed.
	 * @var array
	 */
	protected static $templates = [];

	/**
	 * Get if options has to be translated or not.
	 * @return bool
	 */
	public function GetTranslateOptions () {
		return $this->translateOptions;
	}
	
	/**
	 * Set `TRUE` to translate given options or not.
	 * @param bool $translateOptions
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslateOptions ($translateOptions = TRUE) {
		$this->translateOptions = $translateOptions;
		return $this;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form instance by `$form->AddField();` method. Do not 
	 * use this method even if you don't develop any form field.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Set up translate options boolean property.
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\DataList|\MvcCore\Ext\Forms\Field
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		parent::SetForm($form);
		if (!$this->translate) $this->translateOptions = $this->translate;
		return $this;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * - Set up field render mode if not defined.
	 * - Translate options if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translateOptions) {
			$form = $this->form;
			foreach ($this->options as $key => $value) 
				$this->options[$key] = $form->Translate($key);
		}
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` 
	 * in submit processing. Do not use this method even if you 
	 * don't develop form library or any form field.
	 * 
	 * Return always `NULL` for this `<datalist>` pseudo-field.
	 * 
	 * @param array $rawRequestParams Raw request params from MvcCore 
	 *								  request object based on raw app 
	 *								  input, `$_GET` or `$_POST`.
	 * @return NULL
	 */
	public function Submit (array & $rawRequestParams = []) {
		return NULL;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * 
	 * Render `<datalist>` element, it has not allowed any additional attributes. 
	 * There is allowed only attribute `id` and `<option>` sub tags.
	 * @return string
	 */
	public function RenderControl () {
		$result = '<datalist id="' . $this->id . '">';
		foreach ($this->options as $value) 
			$result .= '<option value="' . htmlspecialchars($value, ENT_QUOTES) . '" />';
		$result .= '</datalist>';
		return $result;
	}
}
