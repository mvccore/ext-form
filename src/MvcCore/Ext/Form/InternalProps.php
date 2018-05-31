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

namespace MvcCore\Ext\Form;

use \MvcCore\Ext\Forms\IError;

trait InternalProps
{
	/**
	 * Internal array with all configured submit buttons to recognize starting 
	 * result state in submit processing by presented button in params array.
	 * @var \MvcCore\Ext\Forms\Fields\SubmitButton|\MvcCore\Ext\Forms\Fields\SubmitInput
	 */
	protected $submitFields = array();

	/**
	 * Internal array to store any configured custom result state values for
	 * submit buttons or for submit inputs. Key in array are field names, values
	 * are custom submit start result state values, if form is submitted by named button.
	 * @var \int[]
	 */
	protected $customResultStates = array();

	/**
	 * Validators instances keyed by validators ending 
	 * class names, created durring `Submit()`.
	 * @var \MvcCore\Ext\Forms\IValidator[]
	 */
	protected $validators = array();

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetSessionClass();`
	 * @var string
	 */
	protected static $sessionClass = NULL;

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetToolClass();`
	 * @var string
	 */
	protected static $toolClass = NULL;

	/**
	 * Temporary collection with all created
	 * form ids strings to determinate
	 * if any id exist only once or not.
	 * @var array
	 */
	protected static $allFormIds = array();

	/**
	 * Static cache with references to all created form session
	 * namespace objects to not create them and configure them
	 * every time they are used.
	 * @var array
	 */
	protected static $allFormsSessions = array();

	/**
	 * Temporary collection with js support files to add into HTML output after rendered form(s).
	 * It could be added directly after rendered form or by external renderer, doesn't metter.
	 * This serves only for purpose - how to determinate to add every supporting javascript for all
	 * it's field types only once. Keys are relative javascript support file paths and values are
	 * simple dummy booleans.
	 * @var array
	 */
	protected static $allJsSupportFiles = array();

	/**
	 * Temporary collection with css support files to add into HTML output after rendered form(s).
	 * It could be added directly after rendered form or by external renderer, doesn't metter.
	 * This serves only for purpose - how to determinate to add every supporting css for all
	 * it's field types only once. Keys are relative css support file paths and values are
	 * simple dummy booleans.
	 * @var array
	 */
	protected static $allCssSupportFiles = array();

	/**
	 * Collection with arrays, where first record is `callable` handler to process, 
	 * if any form submit CSRF checking (Cross Site Request Forgery) triggers error
	 * and where second record is `boolean`, if handler is `closure` or not.
	 * Params in `callable` should be two with following types:
	 *	- `\MvcCore\Ext\Form`	- Form instance where error happend.
	 *	- `\MvcCore\Request`	- Current request object.
	 *	- `string`				- Translated error meessage string.
	 * Example:
	 * `\MvcCore\Ext\Form::AddCsrfErrorHandler(function($form, $request, $errorMsg) {
	 *		// ... anything you want to do, for example to sign out user.
	 * });`
	 * @var \array[]
	 */
	protected static $csrfErrorHandlers = array();

	/**
	 * If there is necessary to add into HTML response after rendered form
	 * any supporting javascript file, there is also necessary to add
	 * base form supporting javascript - this is relative path where
	 * the base supporting javascript is located.
	 * @var string
	 */
	protected static $jsBaseSupportFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/mvccore-form.js';

	/**
	 * Default (not translated) error messages with replacements
	 * for field names and more specific info to tell the user
	 * what happend or what to do more.
	 * @var array
	 */
	protected static $defaultErrorMessages = array(
		IError::EQUAL					=> "Field '{0}' requires exact value: '{1}'.",
		IError::NOT_EQUAL				=> "Value for field '{0}' should not be '{1}'.",
		IError::REQUIRED				=> "Field '{0}' is required.",
		IError::INVALID_FORMAT			=> "Field '{0}' has invalid format ('{1}').",
		IError::INVALID_CHARS			=> "Field '{0}' contains invalid characters (it can not contain: {1}).",
		IError::EMPTY_CONTENT			=> "Sent data are empty.",
		IError::CSRF					=> "Form hash expired, please submit the form again.",
		IError::MIN_LENGTH				=> "Field '{0}' requires at least {1} characters.",
		IError::MAX_LENGTH				=> "Field '{0}' requires no more than {1} characters.",
		IError::LENGTH					=> "Field '{0}' requires a value between {1} and {2} characters long.",
		IError::EMAIL					=> "Field '{0}' requires a valid email address.",
		IError::URL						=> "Field '{0}' requires a valid URL.",
		IError::NUMBER					=> "Field '{0}' requires a valid number.",
		IError::INTEGER					=> "Field '{0}' requires a valid integer.",
		IError::FLOAT					=> "Field '{0}' requires a valid float number.",
		IError::DATE					=> "Field '{0}' requires a valid date format: '{1}'.",
		IError::DATE_TO_LOW				=> "Field '{0}' requires date higher or equal to '{1}'.",
		IError::DATE_TO_HIGH			=> "Field '{0}' requires date lower or equal to '{1}'.",
		IError::TIME					=> "Field '{0}' requires a valid time format: '00:00 - 23:59'.",
		IError::TIME_TO_LOW				=> "Field '{0}' requires time higher or equal to '{1}'.",
		IError::TIME_TO_HIGH			=> "Field '{0}' requires time lower or equal to '{1}'.",
		IError::DATETIME				=> "Field '{0}' requires a valid date time format: '{1}'.",
		IError::PHONE					=> "Field '{0}' requires a valid phone number.",
		IError::ZIP_CODE				=> "Field '{0}' requires a valid zip code.",
		IError::TAX_ID					=> "Field '{0}' requires a valid TAX ID.",
		IError::VAT_ID					=> "Field '{0}' requires a valid VAR ID.",
		IError::GREATER					=> "Field '{0}' requires a value equal or greater than {1}.",
		IError::LOWER					=> "Field '{0}' requires a value equal or lower than {1}.",
		IError::RANGE					=> "Field '{0}' requires a value of {1} to {2} inclusive.",
		IError::DIVISIBLE				=> "Field '{0}' requires a divisible value of {1}.",
		IError::MAX_FILE_SIZE			=> "The size of the uploaded file can be up to {0} bytes.",
		IError::MAX_POST_SIZE			=> "The uploaded data exceeds the limit of {0} bytes.",
		IError::IMAGE					=> "The uploaded file has to be image in format JPEG, GIF or PNG.",
		IError::MIME_TYPE				=> "The uploaded file is not in the expected file format.",
		IError::VALID					=> "Field '{0}' requires a valid option.",
		IError::CHOOSE_MIN_OPTS			=> "Field '{0}' requires at least {1} chosen option(s) at minimal.",
		IError::CHOOSE_MAX_OPTS			=> "Field '{0}' requires {1} of the selected option(s) at maximum.",
		IError::CHOOSE_MIN_OPTS_BUBBLE	=> "Please select at least {0} options as minimal.",
		IError::CHOOSE_MAX_OPTS_BUBBLE	=> "Please select up to {0} options at maximum.",
	);

	/**
	 * Internal flag to quickly know if form fields will be translated or not.
	 * Automaticly completed to `TRUE` if `$form->translator` is not `NULL` and also if
	 * `$form->translator` is `callable`. `FALSE` otherwise. Default value is `FALSE`.
	 * @var bool
	 */
	protected $translate = FALSE;
}
