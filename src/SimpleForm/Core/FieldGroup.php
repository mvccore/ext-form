<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once('Field.php');
require_once('Exception.php');
require_once('View.php');

abstract class SimpleForm_Core_FieldGroup extends SimpleForm_Core_Field
{
	/**
	 * Form group pseudo control type, 
	 * unique type accross all form field types.
	 * @var string
	 */
	public $Type = '';
	/**
	 * Form control value, 
	 * always as array of string or 
	 * numbers for group of controls.
	 * @var array
	 */
	public $Value = array();
	/**
	 * Form group control options to render
	 * more subcontrol attributes for specified 
	 * submitted values (array keys).
	 * This property configuration is required.
	 * Examples:
	 *
	 *  To configure radio buttons named: 'gender' for 'Female' and 'Male':
	 *     <label for="gender-f">Female:</label>
	 *     <input id="gender-f" type="radio" name="gender" value="f" />
	 *     <label for="gender-m">Male:</label>
	 *     <input id="gender-m" type="radio" name="gender" value="m" />
	 *  use configuration:
	 *     $field->Id = 'gender';
	 *     $field->Options = array(
	 *        'f' => 'Female',
	 *        'm' => 'Male',
	 *     );
	 *
	 *  To configure radio buttons named: 'gender' for 'Female' and 'Male':
	 *     <label for="gender-f" class="female">Female:</label>
	 *     <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="female-values" />
	 *     <label for="gender-m" class="male">Male:</label>
	 *     <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="male-values" />
	 *  use configuration:
	 *     $field->Id = 'gender';
	 *     $field->Options = array(
	 *        'f' => array(
	 *           'text'  => 'Female',
	 *           'class' => 'female',
	 *           'attrs' => array('data-any' => 'female-values'),
	 *        ),
	 *        'm' => array(
	 *           'text'  => 'Male',
	 *           'class' => 'male',
	 *           'attrs' => array('data-any' => 'male-values'),
	 *        ),
	 *     );
	 * @requires
	 * @var array
	 */
	public $Options = array();
	/**
	 * Css class for group label.
	 * @var string[]
	 */
	public $GroupCssClasses = array();
	/**
	 * Any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * @var string[]
	 */
	public $GroupLabelAttrs = array();
	/**
	 * Internal common templates how to render field group elements naturaly.
	 * @var array
	 */
	protected static $templates = array(
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{checked}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	);


	/* setters *******************************************************************************/

	/**
	 * Set form group control options to render
	 * more values for more specified submitted keys.
	 * Examples:
	 *
	 *  To configure radio buttons named: 'gender' for 'Female' and 'Male':
	 *     <label for="gender-f">Female:</label>
	 *     <input id="gender-f" type="radio" name="gender" value="f" />
	 *     <label for="gender-m">Male:</label>
	 *     <input id="gender-m" type="radio" name="gender" value="m" />
	 *  use configuration:
	 *     $field->SetId('gender')
	 *           ->SetOptions(array(
	 *              // field values will be translated if configured
	 *              'f' => 'Female',
	 *              'm' => 'Male',
	 *           ));
	 *
	 *  To configure radio buttons named: 'gender' for 'Female' and 'Male':
	 *     <label for="gender-f" class="female">Female:</label>
	 *     <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="female-values" />
	 *     <label for="gender-m" class="male">Male:</label>
	 *     <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="male-values" />
	 *  use configuration:
	 *     $field->SetId('gender')
	 *           ->SetOptions(array(
	 *              'f' => array(
	 *                 'text'  => 'Female',	// text keys will be translated if configured
	 *                 'class' => 'female',
	 *                 'attrs' => array('data-any' => 'female-values'),
	 *              ),
	 *              'm' => array(
	 *                 'text'  => 'Male',	// text keys will be translated if configured
	 *                 'class' => 'male',
	 *                 'attrs' => array('data-any' => 'male-values'),
	 *              ),
	 *           ));
	 * @param array $options
	 */
	public function SetOptions ($options) {
		$this->Options = $options;
		return $this;
	}
	/**
	 * Set css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * @var string|string[]
	 */
	public function SetGroupCssClasses ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$this->GroupCssClasses = $cssClasses;
		} else {
			$this->GroupCssClasses = explode(' ', (string)$cssClasses);
		}
		return $this;
	}
	/**
	 * Add css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * @var string|string[]
	 */
	public function AddGroupCssClass ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$groupCssClasses = $cssClasses;
		} else {
			$groupCssClasses = explode(' ', (string)$cssClasses);
		}
		$this->GroupCssClasses = array_merge($this->GroupCssClasses, $groupCssClasses);
		return $this;
	}
	/**
	 * Set any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * Any previously defined attributes will be replaced.
	 * @var string[]
	 */
	public function SetGroupLabelAttrs ($attrs = array()) {
		$this->GroupLabelAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * All additional attributes will be completed as array merge 
	 * with previous values and new values.
	 * @var string[]
	 */
	public function AddGroupLabelAttr ($attr = array()) {
		$this->GroupLabelAttrs = array_merge($this->GroupLabelAttrs, $attr);
		return $this;
	}


	/* core methods **************************************************************************/

	/*
	// use this constructor in extended class to merge control or label automatic templates
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge((array)parent::$templates, (array)self::$templates);
	}
	*/

	/**
	 * This method  is called internaly from SimpleForm after field
	 * is added into form by $form->AddField(); method. Do not use it
	 * if you are only user of this library.
	 * - check if there are any options for current controls group
	 * Parent method:
	 * - check if field has any name, which is required
	 * - set up form and field id attribute by form id and field name
	 * - set up required
	 * @param SimpleForm $form
	 * @throws SimpleForm_Core_Exception
	 * @return void
	 */
	public function OnAdded (SimpleForm & $form) {
		parent::OnAdded($form);
		if (!$this->Options) {
			$clsName = get_class($this);
			throw new SimpleForm_Core_Exception("No 'Options' defined for form field: '$clsName'.");
		}
	}
	/**
	 * Set up field properties before rendering process.
	 * - translate all option texts
	 * Parent method:
	 * - set up field render mode
	 * - set up translation boolean
	 * - translate label if any
	 * @return void
	 */
	public function SetUp () {
		parent::SetUp();
		if (!$this->Translate) return;
		$lang = $this->Form->Lang;
		$translator = $this->Form->Translator;
		foreach ($this->Options as $key => $value) {
			if (gettype($value) == 'string') {
				// most simple key/value array options configuration
				if ($value) $this->Options[$key] = call_user_func($translator, (string)$value, $lang);
			} else if (gettype($value) == 'array') {
				// advanced configuration with key, text, css class, and any other attributes for single option tag
				$optObj = (object) $value;
				$text = isset($optObj->text) ? $optObj->text : $key;
				if ($text) {
					$this->Options[$key]['text'] = call_user_func($translator, (string)$text, $lang);
				}
			}
		}
	}


	/* rendering ******************************************************************************/

	/**
	 * Render field naturaly by render mode.
	 * Field shoud be rendered with label beside, label around
	 * or without label by local field configuration. Also there
	 * could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally () {
		$result = '';
		if (
			$this->Label && (
				$this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NORMAL ||
				$this->RenderMode == SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND
			)
		) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL || !$this->Label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}
	/**
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL) return $this->RenderControl();
		$attrsStr = $this->renderAttrsWithFieldVars(
			array(), $this->GroupLabelAttrs, $this->GroupCssClasses, TRUE
		);
		$template = $this->LabelSide == 'left' ? static::$templates->togetherLabelLeft : static::$templates->togetherLabelRight;
		$result = SimpleForm_Core_View::Format($template, array(
			'id'		=> $this->Id, 
			'label'		=> $this->Label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
		$errors = $this->RenderErrors();
		if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}
	/**
	 * Render all subcontrols by multiple calls of $field->RenderControlItem();
	 * @return string
	 */
	public function RenderControl () {
		$result = '';
		foreach ($this->Options as $key => $value) {
			$result .= $this->RenderControlItem($key, $value);
		}
		return $result;
	}
	/**
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL) return '';
		$attrsStr = $this->renderAttrsWithFieldVars(
			array(), $this->GroupLabelAttrs, $this->GroupCssClasses
		);
		return SimpleForm_Core_View::Format(static::$templates->label, array(
			'id'		=> $this->Id, 
			'label'		=> $this->Label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
	/**
	 * Render subcontrols with each subcontrol label tag 
	 * and without group label or without group specific errors.
	 * @return string
	 */
	public function RenderControlItem ($key, $option) {
		$result = '';
		$itemControlId = implode(SimpleForm::HTML_IDS_DELIMITER, array(
			$this->Form->Id, $this->Name, $key
		));
		list(
			$itemLabelText, 
			$labelAttrsStr, 
			$controlAttrsStr
		) = $this->renderControlItemCompleteAttrsClassesAndText($key, $option);
		// render control, render label and put it together if necessary
		$checked = FALSE;
		if (gettype($this->Value) == 'array') {
			$checked = in_array($key, $this->Value);
		} else {
			$checked = $this->Value === $key;
		}
		$itemControl = SimpleForm_Core_View::Format(static::$templates->control, array(
			'id'		=> $itemControlId,
			'name'		=> $this->Name,
			'type'		=> $this->Type,
			'value'		=> $key,
			'checked'	=> $checked ? ' checked="checked"' : '',
			'attrs'		=> $controlAttrsStr ? " $controlAttrsStr" : '',
		));
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NORMAL) {
			// control and label
			$itemLabel = SimpleForm_Core_View::Format(static::$templates->label, array(
				'id'		=> $itemControlId, 
				'label'		=> $itemLabelText,
				'attrs'		=> $labelAttrsStr ? " $labelAttrsStr" : '', 
			));
			$result = ($this->LabelSide == 'left') ? $itemControl . $itemLabel : $itemLabel . $itemControl;
		} else if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND) {
			// control inside label
			$result = SimpleForm_Core_View::Format(
				static::$templates->{'togetherLabel' . (($this->LabelSide == 'left') ? 'Right' : 'Left')}, 
				array(
					'id'		=> $itemControlId, 
					'label'		=> $itemLabelText,
					'control'	=> $itemControl,
					'attrs'		=> $labelAttrsStr ? " $labelAttrsStr" : '', 
				)
			);
		}
		return $result;
	}


	/* protected renderers *******************************************************************/

	/**
	 * Complete by $field->Options key and option value:
	 * - label text
	 * - label attributes string
	 * - control attributes string
	 * @param string       $key
	 * @param string|array $option 
	 * @return array
	 */
	protected function renderControlItemCompleteAttrsClassesAndText ($key, $option) {
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->Required;
		if ($this->Type == 'checkbox') $this->Required = FALSE;
		if ($optionType == 'string') {
			$itemLabelText = $option ? $option : $key;
			$labelAttrsStr = $this->renderLabelAttrsWithFieldVars();
			$controlAttrsStr = $this->renderControlAttrsWithFieldVars();
		} else if ($optionType == 'array') {
			$itemLabelText = $option['text'] ? $option['text'] : $key;
			$attrsArr = $this->ControlAttrs;
			$classArr = $this->CssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->ControlAttrs, $option['attrs']);
			}
			if (isset($option['class'])) {
				$classArrParam = array();
				if (gettype($option['class']) == 'array') {
					$classArrParam = $option['class'];
				} else if (gettype($option['class']) == 'string') {
					$classArrParam = explode(' ', $option['class']);
				}
				foreach ($classArrParam as $clsValue) if ($clsValue) $classArr[] = $clsValue;
			}
			$labelAttrsStr = $this->renderAttrsWithFieldVars(
				array(), $attrsArr, $classArr
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				array(), $attrsArr, $classArr, TRUE
			);
		}
		if ($this->Type == 'checkbox') $this->Required = $originalRequired;
		return array($itemLabelText, $labelAttrsStr, $controlAttrsStr);
	}
}