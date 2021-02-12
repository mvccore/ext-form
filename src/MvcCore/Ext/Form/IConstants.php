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

interface IConstants {

	/**
	 * Form http submitting method (`GET`).
	 * @var string
	 */
	const METHOD_GET		= 'GET';

	/**
	 * Form http submitting method (`POST`).
	 * @var string
	 */
	const METHOD_POST		= 'POST';


	/**
	 * Form `enctype` attribute value `application/x-www-form-urlencoded`,
	 * By submitting - all form values will be encoded
	 * to `key1=value1&key2=value2&...` string.
	 * This `enctype` type is used for all `\MvcCore\Ext\Form` instances by default.
	 * @var string
	 */
	const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';

	/**
	 * Form enctype attribute value `multipart/form-data`,
	 * By submitting - data will not be encoded to URL string form.
	 * This value is required when you are using forms that have a file upload control.
	 * @var string
	 */
	const ENCTYPE_MULTIPART  = 'multipart/form-data';

	/**
	 * Form `enctype` attribute value `application/x-www-form-urlencoded`,
	 * By submitting - spaces will be converted to `+` symbols,
	 * but no other special characters will be encoded.
	 * @var string
	 */
	const ENCTYPE_PLAINTEXT  = 'text/plain';


	/**
	 * Html id attributes delimiter,
	 * used for form controls to complete
	 * it's ids as `<form-id>_<control-name>`.
	 * @var string
	 */
	const HTML_IDS_DELIMITER = '_';


	/**
	 * Form submit result state (`0` - error happened).
	 * Submit was not successful, there was an error or errors.
	 * @var int
	 */
	const RESULT_ERRORS		= 0;

	/**
	 * Form submit result state (`1` - everything OK).
	 * Submit was successful, no error happened.
	 * User could be redirected to success url.
	 * @var int
	 */
	const RESULT_SUCCESS	= 1;

	/**
	 * Form submit result state (`2` - everything OK, redirect user to previous step url).
	 * Submit was successful, no error happened and one of submitting
	 * button is control to indicate that user could be redirected
	 * to previous step URL in multiple forms wizard (typically e-shop ordering).
	 * @var int
	 */
	const RESULT_PREV_PAGE	= 2;

	/**
	 * Form submit result state (`3` - everything OK, redirect user to next step url).
	 * Submit was successful, no error happened and one of submitting
	 * button is control to indicate that user could be redirected
	 * to next step URL in multiple forms wizard (typically e-shop ordering).
	 * @var int
	 */
	const RESULT_NEXT_PAGE	= 3;


	/**
	 * Control/labels rendering mode (`normal`).
	 * Label will be rendered before control,
	 * only checkbox and radio button label
	 * will be rendered after control.
	 * @var string
	 */
	const FIELD_RENDER_MODE_NORMAL			= 'normal';

	/**
	 * Control/labels rendering mode (`no-label`).
	 * No label will be rendered with control.
	 * @var string
	 */
	const FIELD_RENDER_MODE_NO_LABEL		= 'no-label';

	/**
	 * Control/labels rendering mode (`label-around`).
	 * Label will be rendered around control.
	 * @var string
	 */
	const FIELD_RENDER_MODE_LABEL_AROUND	= 'label-around';


	/**
	 * Control errors rendering mode (`all-together`).
	 * All errors are rendered naturally at form begin together in one HTML `<div>` element.
	 * If you are using custom template for form - you have to call after form beginning
	 * `$form->RenderErrors();` to get all errors into template. This value is used as
	 * default for all `\MvcCore\Ext\Form` instances.
	 * @var string
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
	 * @var string
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
	 * @var string
	 */
	const ERROR_RENDER_MODE_AFTER_EACH_CONTROL	= 'after-each-control';

	/**
	 * MvcCore Form extension library directory replacement string.
	 * @var string
	 */
	const FORM_ASSETS_DIR_REPLACEMENT = '__MVCCORE_FORM_ASSETS_DIR__';
}