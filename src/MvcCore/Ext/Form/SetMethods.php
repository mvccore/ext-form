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

trait SetMethods
{
	/**
	 * Set form id, required to configure.
	 * Used to identify session data, error messages,
	 * CSRF tokens, html form attribute id value and much more.
	 * @requires
	 * @param string $id
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Set form submitting url value.
	 * It could be relative or absolute, anything
	 * to complete classic html form attribute `action`.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAction ($url = NULL) {
		$this->action = $url;
		return $this;
	}

	/**
	 * Set form http submitting method.
	 * `POST` by default.
	 * @param string $method
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetMethod ($method = \MvcCore\Ext\Forms\IForm::METHOD_POST) {
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Set form enctype attribute - how the form values
	 * should be encoded when submitting it to the server.
	 * `application/x-www-form-urlencoded` by default, it means
	 * all form values will be encoded to `key1=value1&key2=value2...` string.
	 * @param string $enctype
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetEnctype ($enctype = \MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED) {
		$this->enctype = $enctype;
		return $this;
	}

	/**
	 * Set lang property to complete optional translator language argument automaticly.
	 * If you are operating in multilanguage project and you want to use
	 * translator in `\MvcCore\Ext\Form`, set this `lang` property to target language code
	 * you want to translate every visible text into target language. Use this property
	 * with `$form->translator`property.
	 * @param string|NULL $lang
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetLang ($lang = NULL) {
		$this->lang = $lang;
		return $this;
	}

	/**
	 * Set `$form->locale`, usualy used to create proper validator for zip codes, currencies etc...
	 * If you are operating in multilanguage project and you want to use
	 * in `\MvcCore\Ext\Form` form field validators for locale specific needs,
	 * `$form->locale` property helps you to process validation functionality
	 * with proper validator by locale code.
	 * @param string|NULL $locale
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetLocale ($locale = NULL) {
		$this->locale = strtoupper($locale);
		return $this;
	}

	/**
	 * Set form html element css class(es) attribute value.
	 * To specify more css classes - add more strings separated by space
	 * and overwrite any previous css class attribute value. Value is used for
	 * standard css class attribute for HTML `<form>` tag.
	 * @param string $cssClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssClass ($cssClass = '') {
		$this->cssClass = $cssClass;
		return $this;
	}

	/**
	 * Set form html element additional attributes.
	 * To add any other attribute for html `<form>` element,
	 * set here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple. All previously configured additional
	 * attributes will be replaced by given attributes to this function.
	 * @param array $attributes
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAttributes (array $attributes = array()) {
		$this->attributes = $attributes;
		return $this;
	}

	/**
	 * Set form success submit url string to redirect after, relative or absolute,
	 * to specify, where to redirect user after form has been submitted successfully.
	 * It's required to use `\MvcCore\Ext\Form` like this, if you want to use method
	 * `$form->SubmittedRedirect();`, at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetSuccessUrl ($url = NULL) {
		$this->successUrl = $url;
		return $this;
	}

	/**
	 * Set form success submit next step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `2`.
	 * Which means "next step" redirection after successfull submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one non-submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetNextStepUrl ($url = NULL) {
		$this->nextStepUrl = $url;
		return $this;
	}

	/**
	 * Set form error submit url string, relative or absolute, to specify,
	 * where to redirect user after has not been submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorUrl ($url = NULL) {
		$this->errorUrl = $url;
		return $this;
	}

	/**
	 * Set form submit result state. Submit could have two basic values (or three values - for next step):
	 * `NULL` - No `Submit()` method has been called yet.
	 * `0`    - Submit has errors. User will be redirected after submit to error url.
	 *          `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1`    - Submit was successfull. User will be redirected after submit to success url.
	 *          `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2`    - Submit was successfull. User will be redirected after submit to next step url.
	 *          `\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`
	 * @param int|NULL $result
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetResult ($result = \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS) {
		$this->result = $result;
		return $this;
	}

	/**
	 * Set translator to translate field labels, options, placeholders and error messages.
	 * Translator has to be `callable` (it could be `closure function` or `array`
	 * with `classname/instance` and `method name` string). First argument
	 * of `callable` has to be a translation key and second argument
	 * has to be language string (`en`, `de` ...) to translate the key into.
	 * Result of `callable` object has to be a string - translated key for called language.
	 * @param callable|NULL $handler
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetTranslator (callable $translator = NULL) {
		if ($translator !== NULL && is_callable($translator))
			$this->translator = $translator;
		return $this;
	}

	/**
	 * Set default switch how to set every form control to be required by default.
	 * If you define directly any control to NOT be required, it will NOT be required.
	 * This is only value used as DEFAULT VALUE for form fiels, not to strictly define
	 * required flag value in controls. Default value is `FALSE`.
	 * @param bool $defaultRequired
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultRequired ($defaultRequired = TRUE) {
		$this->defaultRequired = $defaultRequired;
		return $this;
	}

	/**
	 * Set multiple fields values by key/value array.
	 * For each key in `$values` array, this method try to find form field
	 * with the same name. Only data with existing fields by keys are setted into field values.
	 * Values are assigned into fields by keys in case sensitive mode by default.
	 * @param array $values						Key value array with keys as field names and values for fields.
	 * @param bool  $caseInsensitive			If `TRUE`, set up values from `$values` with keys in case insensive mode.
	 * @param bool  $clearPreviousSessionValues If `TRUE`, clear all previous data records for this form from session.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetValues (array $values = array(), $caseInsensitive = FALSE, $clearPreviousSessionValues = FALSE) {
		if ($this->dispatchState < 1) $this->Init();
		if ($clearPreviousSessionValues) $this->ClearSession();
		$defaultsKeys = $caseInsensitive
			? ',' . implode(',', array_keys($values)) . ','
			: '' ;
		foreach ($this->fields as $fieldName => & $field) {
			if (isset($values[$fieldName])) {
				$fieldValue = $values[$fieldName];
			} else if ($caseInsensitive) {
				$defaultsKeyPos = stripos($defaultsKeys, ','.$fieldName.',');
				if ($defaultsKeyPos === FALSE) continue;
				$defaultsKey = substr($defaultsKeys, $defaultsKeyPos + 1, strlen($fieldName));
				$fieldValue = $values[$defaultsKey];
			} else {
				continue;
			}
			$fieldValuesIsStr = is_string($fieldValue);
			if (
				$fieldValue !== NULL && (
					!$fieldValuesIsStr || ($fieldValuesIsStr && $fieldValue != '')
				)
			) {
				$field->SetValue($fieldValue);
				$this->values[$fieldName] = $fieldValue;
			}
		}
		return $this;
	}

	/**
	 * Set all form errors. This method is very dangerous, it replace
	 * all previously added form errors with given collection.
	 * If you  want only to add form error, use method:
	 * `$form->AddError($errorMsg, $fieldNames = NULL);` instead.
	 * First param `$errorsCollection` has to be array with arrays.
	 * Every array in collection must have first item as error message
	 * string and second argument (optional) as field name string or
	 * array with field names strings where error happend.
	 * @param array $errorsCollection
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrors ($errorsCollection = array()) {
		$this->errors = array();
		foreach ($errorsCollection as $errorMsgAndFieldNames) {
			list ($errorMsg, $fieldNames) = $errorMsgAndFieldNames;
			$this->AddError(
				$errorMsg, is_array($fieldNames) ? $fieldNames : array($fieldNames)
			);
		}
		return $this;
	}

	/**
	 * Set session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @param $seconds int
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetSessionExpiration ($seconds = 0) {
		$this->sessionExpiration = $seconds;
		return $this;
	}

	/**
	 * Set default control/label rendering mode for each form control/label.
	 * Default values is string `normal`, it means label will be rendered
	 * before control, only label for checkbox and radio button will be
	 * rendered after control.
	 * @param string $defaultFieldsRenderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultFieldsRenderMode ($defaultFieldsRenderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL) {
		$this->defaultFieldsRenderMode = $defaultFieldsRenderMode;
		return $this;
	}

	/**
	 * Set errors rendering mode, by default configured as string: `all-together`.
	 * It means all errors are rendered naturaly at form begin together in one HTML `div.errors` element.
	 * If you are using custom template for form - you have to call after form beginning: `echo $this->RenderErrors();`
	 * to get all errors into template.
	 * @param string $errorsRenderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorsRenderMode ($errorsRenderMode = \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
		$this->errorsRenderMode = $errorsRenderMode;
		return $this;
	}

	/**
	 * Set custom form view script relative path without `.phtml` extension.
	 * View script could be `TRUE` to render form by view script name completed
	 * automaticly with form id and configured view extension (.phtml) or explicit
	 * relative view script path defined by string. Automaticly completed form view
	 * script path and also explicitly defined form view script path by string are
	 * located in directory `/App/Views/Forms` by default. If you want to change this
	 * base directory - use `\MvcCore\Ext\Forms\View::SetFormsDir();` static method.
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * Set form custom template full class name to create custom view object.
	 * Default value is `\MvcCore\Ext\Forms\View` extended from `\MvcCore\View`.
	 * @param string $viewClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewClass ($viewClass = '\\MvcCore\\Ext\\Forms\\View') {
		$this->viewClass = $viewClass;
		return $this;
	}

	/**
	 * Set supporting javascript files configuration. This method is dangerous,
	 * It removes all previously, automaticly configured javascript support files.
	 * If you want only to add javascript support file, call method:
	 * `$form->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);` instead.
	 * Every record in given `$jsPathsClassNamesAndParams` array has to be defined as array with:
	 *	 `0` - `string` - Supporting javascript file relative path from protected `$form->jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @param array $jsFilesClassesAndConstructorParams
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFiles (array $jsRelPathsClassNamesAndParams = array()) {
		$this->jsSupportFiles = array();
		foreach ($jsRelPathsClassNamesAndParams as $jsRelPathClassNameAndParams) {
			list ($jsRelativePath, $jsClassName, $constructorParams) = $jsRelPathClassNameAndParams;
			$this->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);
		}
		return $this;
	}

	/**
	 * Set supporting css files configuration. This method is dangerous,
	 * It removes all previously, automaticly configured css support files.
	 * If you want only to add css support file, call method:
	 * `$form->AddCssSupportFile($cssRelativePath);` instead.
	 * Given `$cssRelativePaths` has to be array with supporting css file relative
	 * paths from protected `$form->cssAssetsRootDir`.
	 * @param array $cssRelativePaths
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFiles (array $cssRelativePaths = array()) {
		$this->cssSupportFiles = array();
		foreach ($cssRelativePaths as $cssRelativePath)
			$this->AddCssSupportFile($cssRelativePath);
		return $this;
	}

	/**
	 * Set javascript support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal javascript
	 * supporting file. Javascript renderer must add given supporting javascript
	 * file into HTML only once.
	 * @param callable|NULL $jsSupportFilesRenderer
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFilesRenderer (callable $jsSupportFilesRenderer) {
		$this->jsSupportFilesRenderer = $jsSupportFilesRenderer;
		return $this;
	}

	/**
	 * Set css support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal css
	 * supporting file. Css renderer must add given supporting css
	 * file into HTML only once.
	 * @param callable|NULL $cssSupportFilesRenderer
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFilesRenderer (callable $cssSupportFilesRenderer) {
		$this->cssSupportFilesRenderer = $cssSupportFilesRenderer;
		return $this;
	}

	/**
	 * Set MvcCore Form javascript support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom javascript file(s),
	 * you can do it by loading github package `mvccore/form-js` to your custom directory,
	 * you have to create there any other custom javascript support file for any custom field
	 * and change this property value to that javascripts directory. All supporting javascripts
	 * for `\MvcCore\Ext\Form` fields will be loaded now from there.
	 * @param string|NULL $jsSupportFilesRootDir
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFilesRootDir ($jsSupportFilesRootDir) {
		$this->jsSupportFilesRootDir = $jsSupportFilesRootDir;
		return $this;
	}

	/**
	 * Set MvcCore Form css support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom css file(s),
	 * you can do it by creating an empty directory somewhere, by copying every css file from
	 * library assets directory into it, by creating any other custom css for any custom field
	 * and by change this property value to that directory. All supporting css for `\MvcCore\Ext\Form`
	 * fields will be loaded now from there.
	 * @param string|NULL $cssSupportFilesRootDir
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFilesRootDir ($cssSupportFilesRootDir) {
		$this->cssSupportFilesRootDir = $cssSupportFilesRootDir;
		return $this;
	}
}
