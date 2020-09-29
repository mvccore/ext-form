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

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field configurable 
 * and internal properties.
 */
trait Props
{
	/************************************************************************************************
	*									Configurable Properties									*
	************************************************************************************************/

	/**
	 * Form field HTML id attribute, completed from form name and field name.
	 * This value is completed automatically, but you can customize it.
	 * @var string|NULL
	 */
	protected $id = NULL;
	
	/**
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @requires
	 * @var string|NULL
	 */
	protected $name = NULL;
	
	/**
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @var string|NULL
	 */
	protected $type = NULL;
	
	/**
	 * Form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @var string|array|int|float|NULL
	 */
	protected $value = NULL;
	
	/**
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @var \string[]
	 */
	protected $cssClasses = [];

	/**
	 * Field title, global HTML attribute, optional.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-title
	 * @var string|NULL
	 */
	protected $title = NULL;

	/**
	 * Boolean to translate title text, `TRUE` by default.
	 * @var boolean
	 */
	protected $translateTitle = TRUE;
	
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
	 * To render field naturally, set `FALSE`, empty string or `NULL` (`NULL` is default).
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
	 * @var bool|string|NULL
	 */
	protected $viewScript = NULL;
	
	
	/**
	 * Supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->jsSupportingFile` property to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string|NULL
	 */
	protected $jsClassName = NULL;
	
	/**
	 * Field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string|NULL
	 */
	protected $jsSupportingFile = NULL;
	
	/**
	 * Field supporting css file relative path.
	 * If you want to use any custom supporting css file 
	 * for any additional purposes for your custom field, you need to 
	 * define path to your css file relatively from configured 
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting css file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your 
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string|NULL
	 */
	protected $cssSupportingFile = NULL;

	/**
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @var bool|NULL
	 */
	protected $translate = NULL;
	
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
	 * Internal field view object, created for custom field rendering purposes,
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
	 * to configure through field constructor config array.
	 * @var \string[]
	 */
	protected static $declaredProtectedProperties = [
		'view', 'form', 'translate', 'errors'
	];
}
