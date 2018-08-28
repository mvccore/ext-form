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

class DataList 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IOptions
{
	use \MvcCore\Ext\Forms\Field\Attrs\Options;

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
	public function & GetTranslateOptions () {
		return $this->translateOptions;
	}
	
	/**
	 * Set `TRUE` to translate given options or not.
	 * @param bool $translateOptions
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetTranslateOptions ($translateOptions = TRUE) {
		$this->translateOptions = $translateOptions;
		return $this;
	}

	/**
	 * This method is called internally from `\MvcCore\Ext\Form` after field
	 * is added into form by `$form->AddField();` method. Do not use it
	 * if you don't develop any library component.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->translate) $this->translateOptions = $this->translate;
		return $this;
	}

	/**
	 * Set up field properties before rendering process.
	 * - Set up translation boolean.
	 * - Translate options if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translateOptions) {
			$form = & $this->form;
			foreach ($this->options as $key => $value) 
				$this->options[$key] = $form->Translate($key);
		}
	}

	/**
	 * Return allways `NULL` for this pseudofield.
	 * @param array $rawRequestParams 
	 * @return NULL
	 */
	public function Submit (array & $rawRequestParams = []) {
		return NULL;
	}

	/**
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
