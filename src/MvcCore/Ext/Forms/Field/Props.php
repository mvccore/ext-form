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
	/************************************************************************************************
	*									Configurable Properties									*
	************************************************************************************************/

	/**
	 * Form field HTML id attribute, completed from form name and field name.
	 * This value is completed automaticly, but you can customize it.
	 * @var string
	 */
	protected $id = NULL;
	
	/**
	 * Form field specific name, used to identify submited value.
	 * This value is reguired for all form fields.
	 * @requires
	 * @var string
	 */
	protected $name = NULL;
	
	/**
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @var string
	 */
	protected $type = NULL;
	
	/**
	 * Form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @var string|array|int|float|NULL
	 */
	protected $value = NULL;
	
	/**
	 * Control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automaticly
	 * before rendering process. Default value is `NULL`.
	 * @var string
	 */
	protected $label = NULL;
	
	/**
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @var string
	 */
	protected $labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT; // right | left
	
	/**
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL` - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL` - `<input />`
	 * @var string
	 */
	protected $renderMode = NULL;
	
	/**
	 * Form field attribute `required`, determinating
	 * if controll will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automaticly required by default form configuration.
	 * @var bool
	 */
	protected $required = NULL;

	/**
	 * Form field attribute `readonly`, determinating if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sended under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @var bool
	 */
	protected $readOnly = FALSE;
	
	/**
	 * Form field attribute `disabled`, determinating if field value will be 
	 * possible to change by user and if user will be graphicly informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sended under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @var bool
	 */
	protected $disabled = FALSE;
	
	/**
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @var \string[]
	 */
	protected $cssClasses = [];
	
	/**
	 * Collection with field HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties
	 * by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`.
	 * Default value is an empty array to not 
	 * render any additional attributes.
	 * @var array
	 */
	protected $controlAttrs = [];
	
	/**
	 * Collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or
	 * `class`, those attributes has it's own 
	 * configurable properties by setter methods 
	 * or by constructor config array. Label `class` 
	 * attribute has always the same css classes as 
	 * it's field automaticly. Default value is an empty 
	 * array to not render any additional attributes.
	 * @var array
	 */
	protected $labelAttrs = [];
	
	/**
	 * List of predefined validator classes ending names or validator instances.
	 * Keys are valiators ending names and values are validators ending names or instances.
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
	 * @var \string[]|\MvcCore\Ext\Forms\IValidator[]
	 */
	protected $validators = [];
	
	/**
	 * Boolean `TRUE` or string with template relative path 
	 * without `.phtml` or `.php` extension, if you want to render 
	 * field by any custom template. 
	 * 
	 * If `TRUE`, path to template is completed by configured 
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, 
	 * which is `/App/Views/Forms/Fields` by default.
	 * 
	 * If any string with relative path given, path must be relative 
	 * from configured `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, 
	 * which is again `/App/Views/Forms/Fields` by default.
	 * 
	 * To render field naturaly, set `FALSE`, empty string or `NULL` (`NULL` is default).
	 * 
	 * Example:
	 * ```
	 * // To render field template prepared in:
	 * // '/App/Views/Forms/Fields/my-specials/my-field-type.phtml':
	 * 
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields'); // by default
	 * $field->viewScript = 'my-specials/my-field-type';
	 * 
	 * // Or you can do the same by:
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields/my-specials');
	 * $field->type = 'my-field-type';
	 * ```
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	protected $viewScript = NULL;
	
	
	/**
	 * Supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->jsSupportingFile` property to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsClassName = NULL;
	
	/**
	 * Field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsSupportingFile = NULL;
	
	/**
	 * Field supporting css file relative path.
	 * If you want to use any custom supporting css file 
	 * for any additional purposes for your custom field, you need to 
	 * define path to your css file relatively from configured 
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting css file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your 
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $cssSupportingFile = NULL;
	
	/**
	 * Fields (and labels) default templates  
	 * for natural (not customized) field rendering.
	 * @var \stdClass
	 */
	protected static $templates = [
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];


	/************************************************************************************************
	*									  Internal Properties									  *
	************************************************************************************************/

	/**
	 * Internal array with errors for rendering.
	 * Main errors collection is stored inside form instance.
	 * @var \string[]
	 */
	protected $errors = [];

	/**
	 * Internal boolean flag about field visible texts and error messages translation.
	 * This flag is automaticly assigned from `$field->form->GetTranslate();` flag in
	 * `$field->Init();` method.
	 * @var bool
	 */
	protected $translate = NULL;

	/**
	 * Internal field view object, created for custom field rendering purposses,
	 * if property `$field->viewScript` is `TRUE` or any string with relative template path.
	 * @var \MvcCore\Ext\Forms\View|\MvcCore\Ext\Forms\IView
	 */
	protected $view = NULL;
	
	/**
	 * Internal reference to form instance, where current fields is added.
	 * @var \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected $form = NULL;

	/**
	 * Protected properties, which is not possible
	 * to configure throught field constructor config array.
	 * @var \string[]
	 */
	protected static $declaredProtectedProperties = [
		'view', 'form', 'translate', 'errors'
	];

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetToolClass();`
	 * @var string
	 */
	protected static $toolClass = NULL;
}
