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

namespace MvcCore\Ext\Form;

use \MvcCore\Ext\Forms\IError;

/**
 * Trait for class `MvcCore\Ext\Form` containing all internal properties.
 * @mixin \MvcCore\Ext\Form
 */
trait InternalProps {
	
	/**
	 * `TRUE` for submit request, `FALSE` otherwise.
	 * @var bool|NULL
	 */
	protected $submit = NULL;

	/**
	 * Previous CSRF session values if there are any.
	 * @var array|[string|NULL, string|NULL]
	 */
	protected $csrfValue = [];

	/**
	 * Content objects hierarchy for rendering fields and fielsets.
	 * @internal
	 * @var \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	protected $children = [];

	/**
	 * Internal fields and fieldsets order to render and validate fields.
	 * @internal
	 * @var array|\stdClass
	 */
	protected $sorting = [
		'sorted'	=> FALSE,
		'numbered'	=> [],
		'naturally'	=> [],
	];

	/**
	 * Internal array with all configured submit buttons to recognize starting
	 * result state in submit processing by presented button in params array.
	 * @internal
	 * @var \MvcCore\Ext\Forms\Fields\ISubmit
	 */
	protected $submitFields = [];

	/**
	 * Internal array to store any configured custom result state values for
	 * submit buttons or for submit inputs. Key in array are field names, values
	 * are custom submit start result state values, if form is submitted by named button.
	 * @internal
	 * @var \int[]
	 */
	protected $customResultStates = [];

	/**
	 * This is INTERNAL property for rendering fields.
	 * Value `TRUE` means `<form>` tag is currently rendered inside, `FALSE` otherwise.
	 * @internal
	 * @var bool
	 */
	protected $formTagRendergingStatus = FALSE;

	/**
	 * Places in code in development environment, where each field has been added into 
	 * form instance. This array is usefull for complex forms, extending each other, 
	 * where are sometimes situations, where some fields are already registered.
	 * @var array<string,array{"file":?string,"line":?number,"function":?string,"class":?string,"type":?string}>
	 */
	protected $fieldsEntries = [];

	/**
	 * Validators instances keyed by validators ending
	 * class names, created during `Submit()`.
	 * @internal
	 * @var \MvcCore\Ext\Forms\Validator[]
	 */
	protected $validators = [];

	/**
	 * Automatically growing tab-index value for fields with tab-index in `auto` value.
	 * @internal
	 * @var int
	 */
	protected $fieldsAutoTabIndex = 0;

	/**
	 * Internal flag to quickly know if form fields will be translated or not.
	 * Automatically completed to `TRUE` if `$form->translator` is not `NULL` and also if
	 * `$form->translator` is `callable`. `FALSE` otherwise. Default value is `FALSE`.
	 * @internal
	 * @var bool
	 */
	protected $translate = FALSE;

	/**
	 * Internal boolean about loaded `Intl` PHP extension, if `TRUE`, extension is loaded.
	 * @var bool
	 */
	protected $intlExtLoaded = NULL;
	
	/**
	 * File size units for internal conversions.
	 * @internal
	 * @var \string[]
	 */
	protected static $fileSizeUnits = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetSessionClass();`
	 * @internal
	 * @var string
	 */
	protected static $sessionClass = NULL;

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetToolClass();`
	 * @internal
	 * @var string
	 */
	protected static $toolClass = NULL;

	/**
	 * Static cache with references to all created form session
	 * namespace objects to not create them and configure them
	 * every time they are used.
	 * @internal
	 * @var array
	 */
	protected static $allFormsSessions = [];

	/**
	 * Temporary collection with js support files to add into HTML output after rendered form(s).
	 * It could be added directly after rendered form or by external renderer, doesn't matter.
	 * This serves only for purpose - how to determinate to add every supporting javascript for all
	 * it's field types only once. Keys are relative javascript support file paths and values are
	 * simple dummy boolean values.
	 * @internal
	 * @var array
	 */
	protected static $allJsSupportFiles = [];

	/**
	 * Temporary collection with css support files to add into HTML output after rendered form(s).
	 * It could be added directly after rendered form or by external renderer, doesn't matter.
	 * This serves only for purpose - how to determinate to add every supporting css for all
	 * it's field types only once. Keys are relative css support file paths and values are
	 * simple dummy boolean values.
	 * @internal
	 * @var array
	 */
	protected static $allCssSupportFiles = [];

	/**
	 * Collection with arrays, where first record is `callable` handler to process,
	 * if any form submit CSRF checking (Cross Site Request Forgery) triggers error
	 * and where second record is `boolean`, if handler is `closure` or not.
	 * Params in `callable` should be two with following types:
	 *  - `\MvcCore\Ext\Form`	- Form instance where error happened.
	 *  - `\MvcCore\Request`	- Current request object.
	 *  - `string`				- Translated error message string.
	 * Example:
	 * ````
	 *   \MvcCore\Ext\Form::AddSecurityErrorHandler(function($form, $request, $errorMsg) {
	 *        // ... anything you want to do, for example to sign out user.
	 *   });
	 * ````
	 * @internal
	 * @var \array[]
	 */
	protected static $securityErrorHandlers = [];

	/**
	 * If there is necessary to add into HTML response after rendered form
	 * any supporting javascript file, there is also necessary to add
	 * base form supporting javascript - this is relative path where
	 * the base supporting javascript is located.
	 * @internal
	 * @var string
	 */
	protected static $jsBaseSupportFile = '__MVCCORE_FORM_ASSETS_DIR__/mvccore-form.js';

	/**
	 * Supporting assets nonce attribute for CSP policy, completed only if necessary.
	 * @internal
	 * @var array<bool|string|null>
	 */
	protected static $assetsNonces = [NULL, NULL];

	/**
	 * MvcCore CSP policy tool full class name.
	 * @var string
	 */
	protected static $cspClassFullName = '\\MvcCore\\Ext\\Tools\\Csp';

	/**
	 * Default (not translated) error messages with replacements
	 * for field names and more specific info to tell the user
	 * what happened or what to do more.
	 * @internal
	 * @var array
	 */
	protected static $defaultErrorMessages = [
		IError::REQUIRED				=> "Field '{0}' is required.",
		IError::EMPTY_CONTENT			=> "Sent data are empty.",
		IError::MAX_POST_SIZE			=> "Sent data exceeds the limit of {0}.",
		IError::CSRF					=> "Form hash expired, please submit the form again.",
	];

	/**
	 * Form instances storage under it's form id strings.
	 * Key is form id, value is form instance.
	 * @internal
	 * @var \MvcCore\Ext\Form[]
	 */
	protected static $instances = [];
}
