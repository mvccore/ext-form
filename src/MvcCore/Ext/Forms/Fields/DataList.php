<?php declare(strict_types=1);

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

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: init, pre-dispatch and render `<datalist>` HTML element 
 *                 with given options, optionally translated. This field 
 *                 has no possible value to submit. It just renders the
 *                 `<datalist>` with given options.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
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
	 * No templates needed.
	 * @var \string[]|\stdClass
	 */
	protected static $templates = [
		'control' => '<datalist id="{id}" {attrs}>{options}</datalist>',
	];


	/**
	 * Create new form `<datalist>` element instance.
	 * 
	 * @param  array                  $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string                 $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string                 $type 
	 * Fixed field order number, null by default.
	 * @param  int                    $fieldOrder
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  string                 $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string                 $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string                 $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array                  $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array                  $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  array                  $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * @param  bool                   $translateOptions
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @param  array                  $optionsLoader
	 * Definition for method name and context to resolve options loading for complex cases.
	 * First item is string method name, which has to return options for `$field->SetOptions()` method.
	 * Second item is context definition int flag, where the method is located, you can use constants:
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 * Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * 
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function __construct(
		array $cfg = [],
		
		$name = NULL, 
		$type = NULL, 
		$fieldOrder = NULL,
		$title = NULL, 
		$translate = NULL, 
		$translateTitle = NULL, 
		array $cssClasses = [], 
		array $controlAttrs = [], 
		
		array $options = [],
		$translateOptions = TRUE,
		array $optionsLoader = []
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
		$this->ctorOptions($cfg);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form instance by `$form->AddField();` method. Do not 
	 * use this method even if you don't develop any form field.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Set up translate options boolean property.
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\DataList|\MvcCore\Ext\Forms\Field
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		parent::SetForm($form);
		$this->setFormLoadOptions();
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
	 *                                request object based on raw app 
	 *                                input, `$_GET` or `$_POST`.
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
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		$optionsStrs = [];
		foreach ($this->options as $value) 
			$optionsStrs[] = '<option value="' . htmlspecialchars($value, ENT_QUOTES) . '" />';
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		/** @var \stdClass $templates */
		$templates = static::$templates;
		return $formViewClass::Format($templates->control, [
			'id'		=> $this->id,
			'options'	=> implode('', $optionsStrs),
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
