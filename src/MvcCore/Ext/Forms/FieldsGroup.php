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

namespace MvcCore\Ext\Forms;

/**
 * Responsibility: init, pre-dispatch and render group of common form controls,
 *                 mostly `input` controls. This class is not possible to
 *                 instantiate, you need to extend this class to create own
 *                 specific form control.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class	FieldsGroup
extends			\MvcCore\Ext\Forms\Field
implements		\MvcCore\Ext\Forms\Fields\IVisibleField,
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IOptions,
				\MvcCore\Ext\Forms\Fields\IMultiple,
				\MvcCore\Ext\Forms\IFieldsGroup {

	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\Options;
	use \MvcCore\Ext\Forms\Field\Props\GroupLabelCssClasses;
	use \MvcCore\Ext\Forms\Field\Props\GroupLabelAttrs;

	/**
	 * Form group pseudo control type,
	 * unique type across all form field types.
	 * @var string|NULL
	 */
	protected $type = NULL;

	/**
	 * Form group controls value, in most cases it's an array of strings.
	 * For extended class `RadioGroup` - the type is only a `string` or `NULL`.
	 * @var \string[]|NULL
	 */
	protected $value = [];

	/**
	 * Standard field template strings for natural
	 * rendering - `label`, `control`, `togetherLabelLeft` and `togetherLabelRight`.
	 * @var \string[]|\stdClass
	 */
	protected static $templates = [
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{checked}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];

	/**
	 * @inheritDocs
	 * @param array $cfg Config array with public properties and it's
	 *                   values which you want to configure, presented
	 *                   in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public static function CreateInstance ($cfg = []) {
		return new static($cfg);
	}

	/**
	 * @inheritDocs
	 * @return \string[]|string|NULL
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * @inheritDocs
	 * @param  \float[]|\int[]|\string[]|float|int|string|array|NULL $value
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetValue ($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return bool
	 */
	public function GetMultiple () {
		return TRUE;
	}

	/**
	 * @inheritDocs
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetMultiple ($multiple = TRUE) {
		return $this;
	}

	/* core methods **************************************************************************/

	/**
	 * Create new form control group instance.
	 * 
	 * @param  array                                            $cfg 
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string                                           $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string                                           $type 
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  \float[]|\int[]|\string[]|float|int|string|array $value
	 * Form field value. It could be string or array, int or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param  string                                           $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string                                           $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string                                           $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  \string[]                                        $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array                                            $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * @param  array                                            $validators 
	 * List of predefined validator classes ending names or validator instances.
	 * Keys are validators ending names and values are validators ending names or instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to 
	 * implement interface  `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base  abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * 
	 * @param  string                                           $accessKey
	 * The access key global attribute provides a hint for generating
	 * a keyboard shortcut for the current element. The attribute 
	 * value must consist of a single printable character (which 
	 * includes accented and other characters that can be generated 
	 * by the keyboard).
	 * @param  bool                                             $autoFocus
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @param  bool                                             $disabled
	 * Form field attribute `disabled`, determination if field value will be 
	 * possible to change by user and if user will be graphically informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sent under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param  bool                                             $readOnly
	 * Form field attribute `readonly`, determination if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sent under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param  bool                                             $required
	 * Form field attribute `required`, determination
	 * if control will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically required by default form configuration.
	 * @param  int|string                                       $tabIndex
	 * An integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. You can set `auto` string value to get next form tab-index value automatically. 
	 * Tab-index for every field in form is better to index from value `1` or automatically and 
	 * moved to specific higher value by place, where is form currently rendered by form 
	 * instance method `$form->SetBaseTabIndex()` to move tab-index for each field into 
	 * final values. Tab-index can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tab-index. If several elements share the same tab-index, 
	 *   their relative order follows their relative positions in the document.
	 * 
	 * @param  string                                           $label
	 * Control label visible text. If field form has configured any translator, translation 
	 * will be processed automatically before rendering process. Default value is `NULL`.
	 * @param  bool                                             $translateLabel
	 * Boolean to translate label text, `TRUE` by default.
	 * @param  string                                           $labelSide
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  int                                              $renderMode
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @param  array                                            $labelAttrs
	 * Collection with `<label>` HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or `class`, those attributes has it's own 
	 * configurable properties by setter methods or by constructor config array. Label `class` 
	 * attribute has always the same css classes as it's field automatically. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  array                                            $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * @param  bool                                             $translateOptions
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @param  array                                            $optionsLoader
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
	 * @param  \string[]                                        $groupLabelCssClasses
	 * Css class or classes for group label as array of strings.
	 * 
	 * @param  array                                            $groupLabelAttrs
	 * Any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * 
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function __construct(
		array $cfg = [],

		$name = NULL,
		$type = NULL,
		$value = NULL,
		$title = NULL,
		$translate = NULL,
		$translateTitle = NULL,
		array $cssClasses = [],
		array $controlAttrs = [],
		array $validators = [],

		$accessKey = NULL,
		$autoFocus = NULL,
		$disabled = NULL,
		$readOnly = NULL,
		$required = NULL,
		$tabIndex = NULL,

		$label = NULL,
		$translateLabel = TRUE,
		$labelSide = NULL,
		$renderMode = NULL,
		array $labelAttrs = [],

		array $options = [],
		$translateOptions = TRUE,
		array $optionsLoader = [],

		array $groupLabelCssClasses = [],

		array $groupLabelAttrs = []
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		$this->ctorOptions($cfg);
		// Merge control or label automatic templates always in extended class constructor.
		/*static::$templates = (object) array_merge(
			(array) parent::$templates,
			(array) self::$templates
		);*/
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @template
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		if ($this->form !== NULL) return $this;
		parent::SetForm($form);
		$this->setFormLoadOptions();
		if (!$this->options) $this->throwNewInvalidArgumentException(
			'No `options` property defined.'
		);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @template
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
		if (!$this->translate) return;
		$form = $this->form;
		foreach ($this->options as $key => & $value) {
			if (is_string($value)) {
				// most simple key/value array options configuration
				if ($value)
					$this->options[$key] = $form->Translate((string) $value);
			} else if (is_array($value)) {
				// advanced configuration with key, text, css class, and any other attributes for single option tag
				$text = isset($value['text'])
					? $value['text']
					: $key;
				if ($text)
					$value['text'] = $form->Translate((string) $text);
			}
		}
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @param  string|NULL $labelAndControlSeparator
	 * @return string
	 */
	public function RenderNaturally ($labelAndControlSeparator = NULL) {
		$result = '';
		if (
			$this->label && (
				$this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL ||
				$this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND
			)
		) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL || !$this->label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
			if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL)
			return $this->RenderControl();
		$attrsStr = $this->renderAttrsWithFieldVars(
			['multiple'], $this->groupLabelAttrs, $this->groupLabelCssClasses, FALSE
		);
		/** @var \stdClass $templates */
		$templates = & static::$templates;
		$template = $this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT
			? $templates->togetherLabelLeft
			: $templates->togetherLabelRight;
		$viewClass = $this->form->GetViewClass();
		$result = $viewClass::Format($template, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " {$attrsStr}" : '',
		]);
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @return string
	 */
	public function RenderControl () {
		$result = '';
		foreach ($this->options as $key => $value) {
			$result .= $this->RenderControlItem($key, $value);
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL)
			return '';
		$attrsStr = $this->renderAttrsWithFieldVars(
			['multiple'], $this->groupLabelAttrs, $this->groupLabelCssClasses, FALSE
		);
		$viewClass = $this->form->GetViewClass();
		/** @var \stdClass $templates */
		$templates = & static::$templates;
		return $viewClass::Format($templates->label, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'attrs'		=> $attrsStr ? " {$attrsStr}" : '',
		]);
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @param  string       $key
	 * @param  string|array $option
	 * @return string
	 */
	public function RenderControlItem ($key, $option) {
		$result = '';
		$itemControlId = implode(\MvcCore\Ext\IForm::HTML_IDS_DELIMITER, [
			$this->form->GetId(), $this->name, $key
		]);
		list(
			$itemLabelText,
			$labelAttrsStr,
			$controlAttrsStr
		) = $this->renderControlItemCompleteAttrsClassesAndText($key, $option);
		if (!$this->form->GetFormTagRenderingStatus())
			$controlAttrsStr .= (strlen($controlAttrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		// render control, render label and put it together if necessary
		$checked = gettype($this->value) == 'array'
			? in_array($key, $this->value)
			: $this->value === $key;
		$viewClass = $this->form->GetViewClass();
		/** @var \stdClass $templates */
		$templates = & static::$templates;
		$itemControl = $viewClass::Format($templates->control, [
			'id'		=> $itemControlId,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars_decode(htmlspecialchars($key, ENT_QUOTES), ENT_QUOTES),
			'checked'	=> $checked ? ' checked="checked"' : '',
			'attrs'		=> strlen($controlAttrsStr) > 0 ? ' ' . $controlAttrsStr : '',
		]);
		if ($this->renderMode == \MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL) {
			// control and label
			$itemLabel = $viewClass::Format($templates->label, [
				'id'		=> $itemControlId,
				'label'		=> $itemLabelText,
				'attrs'		=> $labelAttrsStr ? " {$labelAttrsStr}" : '',
			]);
			$result = ($this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT)
				? $itemControl . $itemLabel
				: $itemLabel . $itemControl;
		} else if ($this->renderMode === \MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND) {
			// control inside label
			$templatesKey = 'togetherLabel' . (
				($this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT)
					? 'Right'
					: 'Left'
			);
			$result = $viewClass::Format(
				static::$templates->$templatesKey,
				[
					'id'		=> $itemControlId,
					'label'		=> $itemLabelText,
					'control'	=> $itemControl,
					'attrs'		=> $labelAttrsStr ? " {$labelAttrsStr}" : '',
				]
			);
		}
		return $result;
	}

	/**
	 * Complete and return semi-finished strings for rendering by field key and option:
	 * - Label text string.
	 * - Label attributes string.
	 * - Control attributes string.
	 * @param  string       $key
	 * @param  string|array $option
	 * @return array
	 */
	protected function renderControlItemCompleteAttrsClassesAndText ($key, & $option) {
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->required;
		if ($this->type == 'checkbox')
			$this->required = FALSE;
		if ($optionType == 'string') {
			$itemLabelText = $option ? $option : $key;
			$labelAttrsStr = $this->renderLabelAttrsWithFieldVars();
			$controlAttrsStr = $this->renderControlAttrsWithFieldVars(['accessKey', 'multiple']);
		} else if ($optionType == 'array') {
			$itemLabelText = $option['text'] ? $option['text'] : $key;
			$attrsArr = $this->controlAttrs;
			$classArr = $this->cssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->controlAttrs, $option['attrs']);
			}
			if (isset($option['class'])) {
				$classArrParam = [];
				if (gettype($option['class']) == 'array') {
					$classArrParam = $option['class'];
				} else if (gettype($option['class']) == 'string') {
					$classArrParam = explode(' ', $option['class']);
				}
				foreach ($classArrParam as $clsValue) if ($clsValue) $classArr[] = $clsValue;
			}
			$labelAttrsStr = $this->renderAttrsWithFieldVars(
				['multiple'], $attrsArr, $classArr, FALSE
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				['multiple'], $attrsArr, $classArr, TRUE
			);
		}
		if ($this->type == 'checkbox')
			$this->required = $originalRequired;
		return [
			$itemLabelText,
			$labelAttrsStr,
			$controlAttrsStr
		];
	}
}
