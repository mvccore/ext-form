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

require_once('SimpleForm/Core/Configuration.php');
require_once('SimpleForm/Core/Exception.php');
require_once('SimpleForm/Core/Field.php');
require_once('SimpleForm/Core/Helpers.php');
require_once('SimpleForm/Core/View.php');

class SimpleForm extends SimpleForm_Core_Configuration
{
	/* public methods ************************************************************************/
	/**
	 * Create SimpleForm instance.
	 * Please don't forget to configure at least $form->Id, $form->Action,
	 * any control to work with and finaly any button:submit/input:submit 
	 * to submit the form to any url defined in $form->Action.
	 * @param MvcCore_Controller|mixed $controller 
	 */
	public function __construct (/*MvcCore_Controller*/ & $controller) {
		$this->Controller = $controller;
		$baseLibPath = str_replace('\\', '/', __DIR__ . '/SimpleForm');
		if (!$this->jsAssetsRootDir) $this->jsAssetsRootDir = $baseLibPath;
		if (!$this->cssAssetsRootDir) $this->cssAssetsRootDir = $baseLibPath;
	}
	/**
	 * Initialize the form, check if we are initialized or not and do it only once,
	 * check if any form id exists and initialize translation boolean for better field initializations.
	 * This is template method. To define any fields in custom SimpleForm class extension,
	 * do it in Init method and call parent method as first line inside your custom Init method.
	 * @throws SimpleForm_Core_Exception 
	 * @return SimpleForm
	 */
	public function Init () {
		if ($this->initialized) return $this;
		$this->initialized = 1;
		if (!$this->Id) {
			$clsName = get_class($this);
			throw new SimpleForm_Core_Exception("No form 'Id' property defined in: '$clsName'.");
		}
		if ((is_null($this->Translate) || $this->Translate === TRUE) && !is_null($this->Translator)) {
			$this->Translate = TRUE;
		} else {
			$this->Translate = FALSE;
		}
		return $this;
	}
	/**
	 * Add form submit error and switch form result to zero - error state.
	 * @param string $errorMsg
	 * @param string $fieldName optional
	 * @return SimpleForm
	 */
	public function AddError ($errorMsg, $fieldName = '') {
		$errorMsgUtf8 = iconv(
			mb_detect_encoding($errorMsg, mb_detect_order(), true), 
			"UTF-8",
			$errorMsg
		);
		$newErrorRec = array(strip_tags($errorMsgUtf8));
		if ($fieldName) $newErrorRec[] = $fieldName;
		$this->Errors[] = $newErrorRec;
		if ($fieldName && isset($this->Fields[$fieldName])) {
			$this->Fields[$fieldName]->AddError($errorMsgUtf8);
		}
		$this->Result = SimpleForm::RESULT_ERRORS;
		return $this;
	}
	/**
	 * Prepare for rendering.
	 * - process all defined fields and call $field->setUp();
	 *   to prepare field for rendering process.
	 * - load any possible error from session and set up 
	 *   errors into fields and into form object to render them properly
	 * - load any possible previously submitted or stored data 
	 *   from session and set up form with them.
	 * - set initialized state to 2, which means - prepared for rendering
	 */
	protected function prepareForRendering () {
		foreach ($this->Fields as & $field) {
			// translate fields if necessary and do any rendering preparation stuff
			$field->SetUp();
		}
		$errors = SimpleForm_Core_Helpers::GetSessionErrors($this->Id);
		foreach ($errors as $fieldName => $errorMsg) {
			$this->AddError($errorMsg, $fieldName);
			if (isset($this->Fields[$fieldName])) {
				// add error classes into settings config where necessary
				$field = $this->Fields[$fieldName];
				$field->AddCssClass('error');
				if (method_exists($field, 'AddGroupCssClass')) {
					$field->AddGroupCssClass('error');
				}
			}
		}
		$data = SimpleForm_Core_Helpers::GetSessionData($this->Id);
		if ($data) $this->SetDefaults($data);
		$this->initialized = 2;
	}
	/**
	 * Add multiple configured form field instances, 
	 * function have infinite params with new field instances.
	 * @param SimpleForm_Core_Field $fields,... Any SimpleForm field instance to add into form
	 * @return SimpleForm
	 */
	public function AddFields () {
		if (!$this->initialized) $this->Init();
		$fields = func_get_args();
		foreach ($fields as & $field) {
			$this->AddField($field);
		}
		return $this;
	}
	/**
	 * Add configured form field instance.
	 * @param SimpleForm_Core_Field $field 
	 * @return SimpleForm
	 */
	public function AddField (SimpleForm_Core_Field $field) {
		if (!$this->initialized) $this->Init();
		$field->OnAdded($this);
		$this->Fields[$field->Name] = $field;
		return $this;
	}
	/**
	 * Set multiple fields values by key/value array.
	 * For each key in $defaults array, library try to find form control
	 * with the same name as array key and that control value is set to array value.
	 * @param array $defaults 
	 * @return void
	 */
	public function SetDefaults (array $defaults = array()) {
		if (!$this->initialized) $this->Init();
		foreach ($defaults as $fieldName => $fieldValue) {
			if (isset($this->Fields[$fieldName])) {
				$this->Fields[$fieldName]->SetValue($fieldValue);
				if ($fieldValue) $this->Data[$fieldName] = $fieldValue;
			}
		}
	}
	/**
	 * Process standard low level submit process.
	 * If no params passed as first argument, all params from MvcCore request object are used.
	 * - if fields are not initialized - initialize them by calling $form->Init();
	 * - check max post size by php configuration if form is posted
	 * - check cross site request forgery tokens with session tokens
	 * - process all field values and their validators and call $form->AddError() where necessary
	 *	 AddError method automaticly switch $form->Result property to zero - 0 means error submit result
	 * Return array with form result, safe values by validators and errors.
	 * @param array $rawParams optional
	 * @return array array($form->Result, $form->Data, $form->Errors);
	 */
	public function Submit ($rawParams = array()) {
		if (!$this->initialized) $this->Init();
		SimpleForm_Core_Helpers::ValidateMaxPostSizeIfNecessary($this);
		if (!$rawParams) $rawParams = $this->Controller->GetRequest()->Params;
		$this->checkCsrf($rawParams);
		$this->submitFields($rawParams);
		// xxx($rawParams);
		return array(
			$this->Result,
			$this->Data,
			$this->Errors,
		);
	}
	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return void
	 */
	public function ClearSession () {
		$this->Data = array();
		SimpleForm_Core_Helpers::SetSessionData($this->Id, array());
		SimpleForm_Core_Helpers::SetSessionCsrf($this->Id, array());
		SimpleForm_Core_Helpers::SetSessionErrors($this->Id, array());
	}
	/**
	 * Rendering process alias.
	 * @see SimpleForm::Render();
	 * @return string
	 */
	public function __toString () {
		try {
			$result = $this->Render();
		} catch (Exception $e) {
			MvcCore_Debug::Exception($e);
			$result = $e->GetMessage();
		}
		return $result;
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
		if ($this->initialized < 2) $this->prepareForRendering();
	}
	/**
	 * Rendering process.
	 * - if forms is not initialized, there is automaticly 
	 *   called $form->Init(); method
	 * - if form is not prepared for rendering, there is 
	 *   automaticly called $form->prepareForRendering(); method
	 * - create new form view instance and set up the view with local
	 *   context variables
	 * - render form naturaly or by custom template
	 * - clean session errors, because errors shoud be rendered 
	 *   only once, only when it's used and it is now in rendering process
	 * @return string
	 */
	public function Render () {
		$result = '';
		if (!$this->initialized) $this->Init();
		if ($this->initialized < 2) $this->prepareForRendering();
		$this->View = new SimpleForm_Core_View($this);
		$this->View->SetUp($this);
		if ($this->TemplatePath) {
			$result = $this->View->RenderTemplate();
		} else {
			$result = $this->View->RenderNaturally();
		}
		$this->Errors = array();
		SimpleForm_Core_Helpers::SetSessionErrors($this->Id, array());
		return $result;
	}
	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with csrf tokens.
	 * @return string
	 */
	public function RenderFormBegin () {
		if (!$this->initialized) $this->Init();
		return $this->View->RenderFormBegin();
	}
	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into div.errors with div.error elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		if (!$this->initialized) $this->Init();
		return $this->View->RenderErrors();
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
		if (!$this->initialized) $this->Init();
		return $this->View->RenderContent();
	}
	/**
	 * Render form end.
	 * Render html closing </form> tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderFormEnd () {
		if (!$this->initialized) $this->Init();
		return $this->View->RenderFormEnd();
	}
	/**
	 * After every custom $form->Submit(); function implementation is at the end,
	 * call this function to redirect user by configured success/error/next step address
	 * into final place and store everything into session.
	 * @return void
	 */
	public function RedirectAfterSubmit () {
		if (!$this->initialized) $this->Init();
		SimpleForm_Core_Helpers::SetSessionErrors($this->Id, $this->Errors);
		SimpleForm_Core_Helpers::SetSessionData($this->Id, $this->Data);
		$url = "";
		if ($this->Result === SimpleForm::RESULT_ERRORS) {
			$url = $this->ErrorUrl;
		} else if ($this->Result === SimpleForm::RESULT_SUCCESS) {
			$url = $this->SuccessUrl;
		} else if ($this->Result === SimpleForm::RESULT_NEXT_PAGE) {
			$url = $this->NextStepUrl;
		}
		$ctrl = $this->Controller;
		$ctrl::Redirect($url, 303);
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
		$result = $jsFilesContent."new SimpleForm("
			."document.getElementById('".$this->Id."'),"
			."[".implode(',', $fieldsConstructors)."]"
		.")";
		if (class_exists('MvcCore_View') && strpos(MvcCore_View::$Doctype, 'XHTML') !== FALSE) {
			$result = '/* <![CDATA[ */' . $result . '/* ]]> */';
		}
		return '<script type="text/javascript">' . $result . '</script>';
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
	 * Create new cresh cross site request forgery tokens,
	 * store them into session under $form->Id and return them.
	 * @return string[]
	 */
	public function SetUpCsrf () {
		$requestPath = $this->getRequestPath();
		$randomHash = bin2hex(openssl_random_pseudo_bytes(32));
		$nowTime = (string)time();
		$name = '____'.sha1($this->Id . $requestPath . 'name' . $nowTime . $randomHash);
		$value = sha1($this->Id . $requestPath . 'value' . $nowTime . $randomHash);
		SimpleForm_Core_Helpers::SetSessionCsrf($this->Id, array($name, $value));
		return array($name, $value);
	}
	/**
	 * Return current cross site request forgery hidden 
	 * input name and it's value as stdClass.
	 * Result stdClass elements has keys 'name' and 'value'.
	 * @return stdClass
	 */
	public function GetCsrf () {
		list($name, $value) = SimpleForm_Core_Helpers::GetSessionCsrf($this->Id);
		return (object) array('name' => $name, 'value' => $value);
	}
}
