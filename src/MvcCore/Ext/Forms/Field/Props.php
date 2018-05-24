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

namespace MvcCore\Ext\Forms\Field;

trait Props
{
	/**
	 * Form control html id
	 * @var string
	 */
	public $Id = '';
	/**
	 * Form control type, usually used in <input type="">
	 * attr value, but unique type accross all form field types.
	 * @var string
	 */
	public $Type = '';
	/**
	 * Form control specific name, server as key in submitting process
	 * where is sended it's used completed value.
	 * @requires
	 * @var string
	 */
	public $Name = '';
	/**
	 * Form control value.
	 * @var string
	 */
	public $Value = '';
	/**
	 * Form control label visible text, text only.
	 * @var string
	 */
	public $Label = '';
	/**
	 * Location where to render <label> element.
	 * 'left' by default.
	 * @var string
	 */
	public $LabelSide = 'left'; // right | left
	/**
	 * Form control attribute required, determinating
	 * if controll will be required to complete by user.
	 * @var bool
	 */
	public $Required = null;
	/**
	 * Form control attribute readonly, determinating
	 * if controll will be readonly to not completed by user,
	 * and readonly by submitting process - so only session value will be used.
	 * @var bool
	 */
	public $Readonly = FALSE;
	/**
	 * Form control attribute disabled, determinating
	 * if controll will be disabled to not completed by user,
	 * and disabled by submitting process - so only session value will be used.
	 * @var bool
	 */
	public $Disabled = FALSE;
	/**
	 * Boolean telling if field will be translated or not.
	 * If nothing is configured as boolean, $field->Form->Translate values is used.
	 * If $field->Translate is TRUE, translated are placeholders, label texts and error messages.
	 * @var bool
	 */
	public $Translate = NULL;
	/**
	 * Control/label rendering mode, defined in form by defaut as: 'normal'.
	 * Normal means label will be rendered before control, only for checkbox
	 * and radio buttons labels will be rendered after controls.
	 * Another possible values are 'no-label' and 'label-around'.
	 * Use \MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL, \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL and
	 * \MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND.
	 * @var string
	 */
	public $RenderMode = NULL;
	/**
	 * Html element css class string value, more classes separated by space.
	 * @var array
	 */
	public $CssClasses = array();
	/**
	 * Collection with html <input> element additional attributes by array keys/values.
	 * @var array
	 */
	public $ControlAttrs = array();
	/**
	 * Collection with html <label> element additional attributes by array keys/values.
	 * @var array
	 */
	public $LabelAttrs = array();
	/**
	 * List of validator classes end-names or list of closure functions
	 * accepting arguments: $submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field
	 * and returning safe value as result. Closure function should call
	 * $field->Form->AddError() internaly if necessary and submitted value is not correct.
	 * All validator classes are located in directory: /Form/Validators/...
	 * For validator class \MvcCore\Ext\Form\Validators\Numeric is necessary only tu set 'Numeric'.
	 * @var string[]|\Closure[]
	 */
	public $Validators = array();
	/**
	 * Field instance errors for rendering process.
	 * @var string[]
	 */
	public $Errors = array();
	/**
	 * Field relative template path without .phtml extension,
	 * empty string by default to render field naturaly.
	 * If there is configured any path, relative from directory /App/Views/Scripts,
	 * field is rendered by custom template.
	 * @var string
	 */
	protected $viewScript = NULL;
	/**
	 * Form field view, object container with variables from local context to render in template.
	 * Created automaticly inside \MvcCore\Ext\Form\Core\Field before field rendering process.
	 * @var \MvcCore\Ext\Form\Core\View
	 */
	public $View = NULL;
	/**
	 * Supporting javascript full class name.
	 * @var string
	 */
	public $JsClass = '';
	/**
	 * Supporting javascript file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->jsAssetsRootDir;
	 * @var string
	 */
	public $Js = '';
	/**
	 * Supporting css file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->cssAssetsRootDir;
	 * @var string
	 */
	public $Css = '';
	/**
	 * Form instance where current fields is placed.
	 * @var \MvcCore\Ext\Form
	 */
	public $Form = NULL;
	/**
	 * Core rendering templates storrage.
	 * Those templates are used in form natural rendering process, form custom
	 * template rendering process, natural field rendering process but not
	 * by custom field rendering process.
	 * @var array
	 */
	public static $Templates = array(
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	);
	/**
	 * Local $this context properties which is not possible
	 * to configure throught constructor config array.
	 * @var string[]
	 */
	protected static $declaredProtectedProperties = array(
		'Id', 'View', 'Form', 'Field',
	);
}
