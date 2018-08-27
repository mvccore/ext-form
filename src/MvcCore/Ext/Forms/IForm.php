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

namespace MvcCore\Ext\Forms;

interface IForm
{
	/**
	 * MvcCore Extension - Form - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';


	/**
	 * Form http submitting method (`GET`).
	 */
	const METHOD_GET		= 'GET';

	/**
	 * Form http submitting method (`POST`).
	 */
	const METHOD_POST		= 'POST';


	/**
	 * Form enctype attribute value `application/x-www-form-urlencoded`,
	 * By submitting - all form values will be encoded
	 * to `key1=value1&key2=value2&...` string.
	 * This enctype type is used for all `\MvcCore\Ext\Form` instances by default.
	 */
	const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';

	/**
	 * Form enctype attribute value `multipart/form-data`,
	 * By submitting - data will not be encoded to url string form.
	 * This value is required when you are using forms that have a file upload control.
	 */
	const ENCTYPE_MULTIPART  = 'multipart/form-data';

	/**
	 * Form enctype attribute value `application/x-www-form-urlencoded`,
	 * By submitting - spaces will be converted to `+` symbols,
	 * but no other special characters will be encoded.
	 */
	const ENCTYPE_PLAINTEXT  = 'text/plain';


	/**
	 * Html id attributes delimiter,
	 * used for form controls to complete
	 * it's ids as `<form-id>_<control-name>`.
	 */
	const HTML_IDS_DELIMITER = '_';


	/**
	 * Form submit result state (`0` - error happend).
	 * Submit was not successful, there was an error or errors.
	 */
	const RESULT_ERRORS		= 0;

	/**
	 * Form submit result state (`1` - everything ok).
	 * Submit was successful, no error happend.
	 * User could be redirected to success url.
	 */
	const RESULT_SUCCESS	= 1;

	/**
	 * Form submit result state (`2` - everything ok, redirect user to previous step url).
	 * Submit was successful, no error happend and one of submitting
	 * button is control to indicate that user could be redirected
	 * to previous step url in multiple forms wizzard (typicly eshop ordering).
	 */
	const RESULT_PREV_PAGE	= 2;

	/**
	 * Form submit result state (`3` - everything ok, redirect user to next step url).
	 * Submit was successful, no error happend and one of submitting
	 * button is control to indicate that user could be redirected
	 * to next step url in multiple forms wizzard (typicly eshop ordering).
	 */
	const RESULT_NEXT_PAGE	= 3;


	/**
	 * Control/labels rendering mode (`normal`).
	 * Label will be rendered before control,
	 * only checkbox and radio button label
	 * will be rendered after control.
	 */
	const FIELD_RENDER_MODE_NORMAL			= 'normal';

	/**
	 * Control/labels rendering mode (`no-label`).
	 * No label will be rendered with control.
	 */
	const FIELD_RENDER_MODE_NO_LABEL		= 'no-label';

	/**
	 * Control/labels rendering mode (`label-around`).
	 * Label will be rendered around control.
	 */
	const FIELD_RENDER_MODE_LABEL_AROUND	= 'label-around';


	/**
	 * Control errors rendering mode (`all-together`).
	 * All errors are rendered naturaly at form begin together in one html div element.
	 * If you are using custom template for form - you have to call after form beginning
	 * `$form->RenderErrors();` to get all errors into template. This value is used as
	 * default for all `\MvcCore\Ext\Form` instances.
	 */
	const ERROR_RENDER_MODE_ALL_TOGETHER		= 'all-together';

	/**
	 * Control errors rendering mode (`before-each-control`).
	 * If there will be any error, it will be rendered as single span.errors
	 * before current form control with single or multiple span.error elements
	 * inside, by errors count for current form control. It will be rendered in
	 * natural form rendering mode without template but also in custom form rendering mode
	 * with template if you call anytime in template `$field->RenderLabelAndControl();`
	 * If you will use in custom form rendering mod with template method `$field->RenderControl();`,
	 * there will be not rendered any error spans before control, you have to use `$field->RenderErrors();`
	 * to get errors for each control.
	 */
	const ERROR_RENDER_MODE_BEFORE_EACH_CONTROL	= 'before-each-control';

	/**
	 * Control errors rendering mode (`after-each-control`).
	 * If there will be any error, it will be rendered as single span.errors
	 * after current form control with single or multiple span.error elements
	 * inside, by errors count for current form control. It will be rendered in
	 * natural form rendering mode without template but also in custom form rendering mode
	 * with template if you call anytime in template `$field->RenderLabelAndControl();`
	 * If you will use in custom form rendering mode with template method `$field->RenderControl();`,
	 * there will be rendered no error spans before control, you have to use `$field->RenderErrors();`
	 * to get errors for each control.
	 */
	const ERROR_RENDER_MODE_AFTER_EACH_CONTROL	= 'after-each-control';

	/**
	 * MvcCore Form extension library directory replacement string.
	 */
	const FORM_ASSETS_DIR_REPLACEMENT = '__MVCCORE_FORM_ASSETS_DIR__';
}
