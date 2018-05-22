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

namespace MvcCore\Ext;

require_once('Form/Core/Configuration.php');
//require_once('Form/Core/Exception.php');
require_once('Form/Core/Field.php');
//require_once('Form/Core/Helpers.php');
//require_once('Form/Core/View.php');

class Form extends Form\Core\Configuration
{
	/**
	 * MvcCore Extension - Form - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '4.3.1';

	/* public methods ************************************************************************/
	/**
	 * Create \MvcCore\Ext\Form instance.
	 * Please don't forget to configure at least $form->Id, $form->Action,
	 * any control to work with and finaly any button:submit/input:submit
	 * to submit the form to any url defined in $form->Action.
	 * @param \MvcCore\Controller|mixed $controller
	 */
	public function __construct (/*\MvcCore\Controller*/ & $controller) {
		$this->application = \MvcCore\Application::GetInstance();
		$this->Controller = $controller;
		$baseLibPath = str_replace('\\', '/', __DIR__ . '/Form');
		if (!$this->jsAssetsRootDir) $this->jsAssetsRootDir = $baseLibPath;
		if (!$this->cssAssetsRootDir) $this->cssAssetsRootDir = $baseLibPath;
	}

	/**
	 * Rendering process alias.
	 * @see \MvcCore\Ext\Form::Render();
	 * @return string
	 */
	public function __toString () {
		return $this->Render();
	}
	/**
	 * Add form submit error and switch form result to zero - error state.
	 * @param string $errorMsg
	 * @param string|array|NULL $fieldNames optional
	 * @return \MvcCore\Ext\Form
	 */
	public function AddError ($errorMsg, $fieldNames = NULL) {
		$errorMsgUtf8 = iconv(
			mb_detect_encoding($errorMsg, mb_detect_order(), TRUE),
			"UTF-8",
			$errorMsg
		);
		$newErrorRec = array(strip_tags($errorMsgUtf8));
		/** @var int $fieldNameType 0 - NULL, 1 - string, 2 - array */
		$fieldNameType = $fieldNames === NULL ? 0 : (gettype($fieldNames) == 'array' ? 2 : 1);
		if ($fieldNameType > 0) {
			$newErrorRec[] = $fieldNames;
			if ($fieldNameType === 1) {
				if (isset($this->Fields[$fieldNames]))
					$this->Fields[$fieldNames]->AddError($errorMsgUtf8);
			} else if ($fieldNameType === 2) {
				foreach ($fieldNames as $fieldName)
					if (isset($this->Fields[$fieldName]))
						$this->Fields[$fieldName]->AddError($errorMsgUtf8);
			}
			$this->Errors[] = $newErrorRec;
		}
		$this->Result = Form::RESULT_ERRORS;
		return $this;
	}
	/**
	 * Add configured form field instance.
	 * @param \MvcCore\Ext\Form\Core\Field $field
	 * @return \MvcCore\Ext\Form
	 */
	public function & AddField (\MvcCore\Ext\Form\Interfaces\IField $field) {
		if (!$this->initialized) $this->Init();
		$field->OnAdded($this);
		$this->Fields[$field->Name] = $field;
		return $this;
	}
	/**
	 * Add multiple configured form field instances,
	 * function have infinite params with new field instances.
	 * @param \MvcCore\Ext\Form\Core\Field $fields,... Any `\MvcCore\Ext\Form\Interfaces\IField` instance to add into form.
	 * @return \MvcCore\Ext\Form
	 */
	public function & AddFields () {
		if (!$this->initialized) $this->Init();
		$fields = func_get_args();
		foreach ($fields as & $field) {
			$this->AddField($field);
		}
		return $this;
	}
	/**
	 * Unset submitted $form->Data records wchid are empty string or empty array.
	 * @return \MvcCore\Ext\Form
	 */
	public function UnsetEmptyData () {
		$dataKeys = array_keys($this->Data);
		for ($i = 0, $l = count($dataKeys); $i < $l; $i += 1) {
			$dataKey = $dataKeys[$i];
			$dataValue = $this->Data[$dataKey];
			$dataValueType = gettype($dataValue);
			if ($dataValueType == 'array') {
				if (!$dataValue) unset($this->Data[$dataKey]);
			} else {
				if ($dataValue === '') unset($this->Data[$dataKey]);
			}
		}
		return $this;
	}

	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return \MvcCore\Ext\Form
	 */
	public function & ClearSession () {
		$this->Data = array();
		include_once('Form/Core/Helpers.php');
		Form\Core\Helpers::SetSessionData($this->Id, array());
		Form\Core\Helpers::SetSessionCsrf($this->Id, array());
		Form\Core\Helpers::SetSessionErrors($this->Id, array());
		return $this;
	}
	/**
	 * Return current cross site request forgery hidden
	 * input name and it's value as stdClass.
	 * Result stdClass elements has keys 'name' and 'value'.
	 * @return \stdClass
	 */
	public function GetCsrf () {
		include_once('Form/Core/Helpers.php');
		list($name, $value) = Form\Core\Helpers::GetSessionCsrf($this->Id);
		return (object) array('name' => $name, 'value' => $value);
	}
	/**
	 * Return form field instance by form field name if it exists, else return null;
	 * @param string $fieldName
	 * @return \MvcCore\Ext\Form\Core\Field|null
	 */
	public function & GetField ($fieldName = '') {
		$result = NULL;
		if (isset($this->Fields[$fieldName])) $result = $this->Fields[$fieldName];
		return $result;
	}
	/**
	 * Return form field instances by field type string
	 * @param string $fieldType
	 * @return \MvcCore\Ext\Form\Core\Field[]
	 */
	public function & GetFieldsByType ($fieldType = '') {
		$result = array();
		foreach ($this->Fields as & $field) {
			if ($field->Type == $fieldType) $result[$field->Name] = $field;
		}
		return $result;
	}
	/**
	 * Return form field instances by field class name
	 * compared by 'is_a($field, $fieldClassName)' check
	 * @param string $fieldClassName
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no extended instances
	 * @return \MvcCore\Ext\Form\Core\Field[]
	 */
	public function & GetFieldsByClass ($fieldClassName = '', $directTypesOnly = FALSE) {
		$result = array();
		foreach ($this->Fields as & $field) {
			if (is_a($field, $fieldClassName)) {
				if ($directTypesOnly) {
					if (is_subclass_of($field, $fieldClassName)) continue;
				}
				$result[$field->Name] = $field;
			}
		}
		return $result;
	}
	/**
	 * Return first catched form field instance by field class name
	 * compared by 'is_a($field, $fieldClassName)' check
	 * @param string $fieldClassName
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no extended instances
	 * @return \MvcCore\Ext\Form\Core\Field|null
	 */
	public function & GetFirstFieldsByClass ($fieldClassName = '', $directTypesOnly = FALSE) {
		$result = NULL;
		foreach ($this->Fields as & $field) {
			if (is_a($field, $fieldClassName)) {
				if ($directTypesOnly) {
					if (is_subclass_of($field, $fieldClassName)) continue;
				}
				$result = $field;
				break;
			}
		}
		return $result;
	}
	/**
	 * Initialize the form, check if form is initialized or not and do it only once.
	 * Check if any form id exists and initialize translation boolean for better field initializations.
	 * This is template method. To define any fields in custom `\MvcCore\Ext\Form` class extension,
	 * do it in `Init()` method and call `parent::Init();` as first line inside your custom `Init()` method.
	 * @throws \MvcCore\Ext\Form\Core\Exception
	 * @return \MvcCore\Ext\Form
	 */
	public function Init () {
		if ($this->initialized) return $this;
		$this->initialized = 1;
		if (!$this->Id) {
			$clsName = get_class($this);
			include_once('Form/Core/Exception.php');
			throw new Form\Core\Exception("No form 'Id' property defined in: '$clsName'.");
		}
		if ((is_null($this->Translate) || $this->Translate === TRUE) && !is_null($this->Translator)) {
			$this->Translate = TRUE;
		} else {
			$this->Translate = FALSE;
		}
		return $this;
	}
	/**
	 * Prepare form and it's fields for rendering.
	 * This function is called automaticly by rendering process if necessary.
	 * But if you need to operate with fields in your controller before rendering
	 * with real session values and initialized session errors, you can call this
	 * method anytime to prepare form for rendering and operate with anything inside.
	 * @return void
	 */
	public function Prepare () {
		if (!$this->initialized) $this->Init();
		if ($this->initialized < 2) $this->prepareRenderIfNecessary();
	}

	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/next
	 * step url address into final place and store everything into session.
	 * @return void
	 */
	public function RedirectAfterSubmit () {
		if (!$this->initialized) $this->Init();
		include_once('Form/Core/Helpers.php');
		$url = "";
		if ($this->Result === Form::RESULT_ERRORS) {
			$url = $this->ErrorUrl;
		} else if ($this->Result === Form::RESULT_SUCCESS) {
			$url = $this->SuccessUrl;
			$this->Data = array();
		} else if ($this->Result === Form::RESULT_NEXT_PAGE) {
			$url = $this->NextStepUrl;
			$this->Data = array();
		}
		Form\Core\Helpers::SetSessionErrors($this->Id, $this->Errors);
		Form\Core\Helpers::SetSessionData($this->Id, $this->Data);
		$ctrl = $this->Controller;
		$ctrl::Redirect($url, 303);
	}
	/**
	 * Remove configured form field instance by field name.
	 * @param string $fieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveField ($fieldName = '') {
		if (!$this->initialized) $this->Init();
		if (isset($this->Fields[$fieldName])) unset($this->Fields[$fieldName]);
		return $this;
	}

	/**
	 * Render form into string to display it.
	 * - If form is not initialized, there is automaticly
	 *   called `$form->Init();` method.
	 * - If form is not prepared for rendering, there is
	 *   automaticly called `$form->prepareForRendering();` method.
	 * - Create new form view instance and set up the view with local
	 *   context variables.
	 * - Render form naturaly or by custom template.
	 * - Clean session errors, because errors shoud be rendered
	 *   only once, only when it's used and it's now - in this rendering process.
	 * @return string
	 */
	public function Render () {
		$this->prepareRenderIfNecessary();
		if ($this->TemplatePath) {
			$result = $this->View->RenderTemplate();
		} else {
			$result = $this->View->RenderNaturally();
		}
		$this->cleanUpRenderIfNecessary();
		return $result;
	}
	/**
	 * Render form content.
	 * Go through all $form->Fields and call $field->Render(); on every field
	 * and put it into an empty <div> element. Render each field in full possible
	 * way - naturaly by label configuration with possible errors configured beside
	 * or with custom field template.
	 * @return string
	 */
	public function RenderContent () {
		$this->prepareRenderIfNecessary();
		return $this->View->RenderContent();
	}
	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into div.errors with div.error elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		$this->prepareRenderIfNecessary();
		return $this->View->RenderErrors();
	}
	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with csrf tokens.
	 * @return string
	 */
	public function RenderBegin () {
		$this->prepareRenderIfNecessary();
		return $this->View->RenderBegin();
	}
	/**
	 * Render form end.
	 * Render html closing </form> tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderEnd () {
		if (!$this->initialized) $this->Init();
		$result = $this->View->RenderEnd();
		$this->cleanUpRenderIfNecessary();
		return $result;
	}
	/**
	 * Render all supporting css files directly
	 * as <style> tag content inside html template
	 * called usualy right after form end tag
	 *	or
	 * render all supporting css files by external
	 * css assets renderer to add only links to html head
	 * linked to external css source files.
	 * @return string
	 */
	public function RenderCss () {
		if (!$this->Css) return '';
		$cssFiles = $this->completeAssets('css');
		$cssFilesContent = '';
		$loadCssFilesContents = !is_callable($this->CssRenderer);
		foreach ($cssFiles as $cssFile) {
			$this->renderAssetFile($cssFilesContent, $this->CssRenderer, $loadCssFilesContents, $cssFile);
		}
		if (!$loadCssFilesContents) return '';
		return '<style type="text/css">'.$cssFilesContent.'</style>';
	}
	/**
	 * Render all supporting js files directly
	 * as <script> tag content inside html template
	 * called usualy right after form end tag
	 *	or
	 * render all supporting javascript files by external
	 * assets renderer to add only scripts to html head
	 * linked to external script source files. But there is still created
	 * one <script> tag right after form tag end with supporting javascripts
	 * initializations by rendered form fieds options, names, counts, values etc...
	 * @return string
	 */
	public function RenderJs () {
		if (!$this->Js) return '';
		$jsFiles = $this->completeAssets('js');
		$jsFilesContent = '';
		$fieldsConstructors = array();
		$loadJsFilesContents = !is_callable($this->JsRenderer);
		if (!isset(self::$js[$this->JsBaseFile])) {
			$this->JsBaseFile = $this->absolutizeAssetPath($this->JsBaseFile, 'js');
			self::$js[$this->JsBaseFile] = TRUE;
			$this->renderAssetFile($jsFilesContent, $this->JsRenderer, $loadJsFilesContents, $this->JsBaseFile);
		}
		foreach ($jsFiles as $jsFile) {
			$this->renderAssetFile($jsFilesContent, $this->JsRenderer, $loadJsFilesContents, $jsFile);
		}
		foreach ($this->Js as $item) {
			$paramsStr = json_encode($item[2]);
			$paramsStr = mb_substr($paramsStr, 1, mb_strlen($paramsStr) - 2);
			$fieldsConstructors[] = "new " . $item[1] . "(" . $paramsStr . ")";
		}
		$result = $jsFilesContent."new MvcCoreForm("
			."document.getElementById('".$this->Id."'),"
			."[".implode(',', $fieldsConstructors)."]"
		.")";
		include_once('Form/Core/View.php');
		if (class_exists('\MvcCore\View') && strpos(\MvcCore\View::$Doctype, 'XHTML') !== FALSE) {
			$result = '/* <![CDATA[ */' . $result . '/* ]]> */';
		}
		return '<script type="text/javascript">' . $result . '</script>';
	}
	/**
	 * Create new fresh cross site request forgery tokens,
	 * store them into session under $form->Id and return them.
	 * @return string[]
	 */
	public function SetUpCsrf () {
		$requestPath = $this->getRequestPath();
		$randomHash = bin2hex(openssl_random_pseudo_bytes(32));
		$nowTime = (string)time();
		$name = '____'.sha1($this->Id . $requestPath . 'name' . $nowTime . $randomHash);
		$value = sha1($this->Id . $requestPath . 'value' . $nowTime . $randomHash);
		include_once('Form/Core/Helpers.php');
		Form\Core\Helpers::SetSessionCsrf($this->Id, array($name, $value));
		return array($name, $value);
	}

	/**
	 * Process standard low level submit process.
	 * If no params passed as first argument, all params from object
	 * `\MvcCore\Application::GetInstance()->GetRequest()` are used.
	 * - If fields are not initialized - initialize them by calling `$form->Init();`.
	 * - Check max. post size by php configuration if form is posted.
	 * - Check cross site request forgery tokens with session tokens.
	 * - Process all field values and their validators and call `$form->AddError()` where necessary.
	 *	 `AddError()` method automaticly switch `$form->Result` property to zero - `0`, it means error submit result.
	 * Return array with form result, safe values from validators and errors array.
	 * @param array $rawParams optional
	 * @return array Array to list: `array($form->Result, $form->Data, $form->Errors);`
	 */
	public function Submit ($rawParams = array()) {
		if (!$this->initialized) $this->Init();
		include_once('Form/Core/Helpers.php');
		Form\Core\Helpers::ValidateMaxPostSizeIfNecessary($this);
		if (!$rawParams) $rawParams = $this->Controller->GetRequest()->GetParams('.*');
		$this->ValidateCsrf($rawParams);
		$this->submitFields($rawParams);
		return array(
			$this->Result,
			$this->Data,
			$this->Errors,
		);
	}
	/**
	 * Check cross site request forgery sended tokens from user with session tokens.
	 * If tokens are diferent, add form error and process csrf error handlers queue.
	 * @param array $rawRequestParams
	 * @return bool
	 */
	public function ValidateCsrf ($rawRequestParams = array()) {
		$result = FALSE;
		include_once('Form/Core/Helpers.php');
		$sessionCsrf = Form\Core\Helpers::GetSessionCsrf($this->Id);
		list($name, $value) = $sessionCsrf ? $sessionCsrf : array(NULL, NULL);
		if (!is_null($name) && !is_null($value)) {
			if (isset($rawRequestParams[$name]) && $rawRequestParams[$name] === $value) {
				$result = TRUE;
			}
		}
		if (!$result) {
			$errorMsg = Form::$DefaultMessages[Form::CSRF];
			if ($this->Translate) {
				$errorMsg = call_user_func($this->Translator, $errorMsg);
			}
			$this->AddError($errorMsg);
			foreach (static::$csrfErrorHandlers as $handler) {
				if (is_callable($handler)) {
					$handler($this, $errorMsg);
				}
			}
		}
		return $result;
	}
}
