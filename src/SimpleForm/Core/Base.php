<?php

require_once(__DIR__.'/../../SimpleForm.php');
require_once(__DIR__.'/../Button.php');
require_once('Helpers.php');
require_once('Field.php');
require_once('Validator.php');
require_once('View.php');

abstract class SimpleForm_Core_Base
{
	/**
	 * Initialized state. You can call $form->Init(); method any time you want,
	 * it automaticly recognize, if it is already initialized or not, but there is 
	 * necessary to call at Init() function begin parent::Init(); call to do it.
	 * Sometimes you need to work with feelds before rendering outside of form
	 * and there is necessary to call $form->Init() menthod by yourself, but normaly 
	 * it's called internaly after it is realy needed, so only for render process 
	 * and submit process. This initialization state property has three values:
	 *	0 - not initialized
	 *	1 - initialized, but fields are not prepared internaly for rendering
	 *	2 - all fieds are prepared for rendering (it is processed internaly in 
	 *		$form->Render() function, state 2 is not necessary to know.
	 * @var int
	 */
	protected $initialized = 0;
	/**
	 * Temporary collection of js files to add after form (directly into html output
	 * or by external renderer, doesn't metter), this serves only for purposes how to 
	 * determinate to add supporting javascript by field type only once. Keys are relative
	 * javascript file paths and values are simple dummy booleans.
	 * @var array
	 */
	protected static $js = array();
	/**
	 * Temporary collection of css files to add after form (directly into html output
	 * or by external renderer, doesn't metter), this serves only for purposes how to
	 * determinate to add supporting css by field type only once. Keys are relative
	 * css file paths and values are simple dummy booleans.
	 * @var array
	 */
	protected static $css = array();
	/**
	 * Simple form javascript assets root directory.
	 * After SimpleForm instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom javascript,
	 * you can do it by loading github package mvccore/simpleform-cusom-js somewhere
	 * create there any other custom javascript for any custom field and change this value 
	 * to that directory. All supporting javascript for SimpleForm fields will be loaded from there.
	 * @var string
	 */
	protected $jsAssetsRootDir = '';
	/**
	 * Simple form css assets root directory.
	 * After SimpleForm instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom css,
	 * you can do it by creating an empty directory somewhere, by copying every css file from 
	 * library assets directory into it, by creating any other custom css for any custom field 
	 * and by change this value to that directory. All supporting css for SimpleForm 
	 * fields will be loaded from there.
	 * @var string
	 */
	protected $cssAssetsRootDir = '';
	/**
	 * Collection with callable handlers to process anytime CSRF checking cause an error inside form.
	 * @var array
	 */
	protected static $csrfErrorHandlers = array();


	/**
	 * Get request path with protocol, domain, port, part but without any possible query string.
	 * @return string
	 */
	protected function getRequestPath () {
		$requestUri = $_SERVER['REQUEST_URI'];
		$lastQuestionMark = mb_strpos($requestUri, '?');
		if ($lastQuestionMark !== FALSE) $requestUri = mb_substr($requestUri, 0, $lastQuestionMark);
		$protocol = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https:' : 'http:';
		return $protocol . '//' . $_SERVER['HTTP_HOST'] . $requestUri;
	}
	/**
	 * Check cross site request forgery sended tokens from user with session tokens.
	 * If tokens are diferent, add form error and process csrf error handlers queue.
	 * @param array $rawRequestParams 
	 * @return void
	 */
	protected function checkCsrf ($rawRequestParams = array()) {
		$result = FALSE;
		$sessionCsrf = SimpleForm_Core_Helpers::GetSessionCsrf($this->Id);
		list($name, $value) = $sessionCsrf ? $sessionCsrf : array(NULL, NULL);
		if (!is_null($name) && !is_null($value)) {
			if (isset($rawRequestParams[$name]) && $rawRequestParams[$name] === $value) {
				$result = TRUE;
			}
		}
		if (!$result) {
			$errorMsg = SimpleForm::$DefaultMessages[static::CSRF];
			if ($this->Translate) {
				$errorMsg = call_user_func($this->Translator, $errorMsg);
			}
			$this->AddError($errorMsg);
			foreach (static::$csrfErrorHandlers as $handler) {
				if (is_callable($handler)) {
					call_user_func($handler, $this);
				}
			}
		}
	}
	/**
	 * Complete css or js supporting files to add after rendered form
	 * or to add them by external renderer. This function process all 
	 * added assets and filter them to add them finally only one by once.
	 * @param string $assetsKey 
	 * @return array
	 */
	protected function completeAssets ($assetsKey = '') {
		$files = array();
		$assetsKeyUcFirst = ucfirst($assetsKey);
		foreach ($this->$assetsKeyUcFirst as $item) {
			$files[$this->absolutizeAssetPath($item[0], $assetsKey)] = TRUE;
		}
		$files = array_keys($files);
		foreach ($files as $key => $file) {
			if (isset(static::${$assetsKey}[$file])) {
				unset($files[$key]);
			} else {
				static::${$assetsKey}[$file] = TRUE;
			}
		}
		return array_values($files);
	}
	/**
	 * Absolutize assets path. Every field has cofigured it's supporting css or js file with
	 * absolute path replacement inside file path string by '__SIMPLE_FORM_DIR__'.
	 * Replace now the replacement by prepared properties $form->jsAssetsRootDir or $form->cssAssetsRootDir
	 * to set path into library assets folder by default or to any other user defined paths.
	 * @param string $path
	 * @param string $assetsKey
	 * @return string
	 */
	protected function absolutizeAssetPath ($path = '', $assetsKey = '') {
		$assetsRootDir = $assetsKey == 'js' ? $this->jsAssetsRootDir : $this->cssAssetsRootDir;
		return str_replace(
			array('__SIMPLE_FORM_DIR__', '\\'),
			array($assetsRootDir, '/'),
			$path
		);
	}
	/**
	 * Render supporting js/css file. Add it after renderer form content or call extenal renderer.
	 * @param string	$content 
	 * @param callable	$renderer 
	 * @param bool		$loadContent 
	 * @param string	$absPath 
	 * @return void
	 */
	protected function renderAssetFile (& $content, & $renderer, $loadContent, $absPath) {
		if ($loadContent) {
			$content .= trim(file_get_contents($absPath), "\n\r;") . ';';
		} else {
			call_user_func($renderer, new SplFileInfo($absPath));
		}
	}
	/**
	 * Process all fields configured validators and add errors where necessary.
	 * Clean client values to safe values by configured validator classes for each field.
	 * After all fields are processed, store clean values and error messages into session
	 * to use them in any possible future request, where is necessary to fill and submit 
	 * the form again, for example by any error and redirecting to form error url.
	 * @param array $rawRequestParams
	 * @return void
	 */
	protected function submitFields ($rawRequestParams = array()) {
		foreach ($this->Fields as $fieldName => & $field) {
			/** @var $field SimpleForm_Core_Field */
			if ($field->Readonly || $field->Disabled) {
				$safeValue = $field->GetValue(); // get by SetDefaults(array()) call
			} else {
				$safeValue = $this->submitField($fieldName, $rawRequestParams, $field);
			}
			if (is_null($safeValue)) $safeValue = '';
			$field->SetValue($safeValue);
			if (!($field instanceof SimpleForm_Button)) {
				$this->Data[$fieldName] = $safeValue;
			}
		}
		//x($rawRequestParams);
		//xxx($this->Data);
		SimpleForm_Core_Helpers::SetSessionErrors($this->Id, $this->Errors);
		SimpleForm_Core_Helpers::SetSessionData($this->Id, $this->Data);
	}
	/**
	 * Process single field configured validators and add errors where necessary.
	 * Clean client value to safe value by configured validator classes for this field.
	 * Return safe value.
	 * @param string				$fieldName 
	 * @param array					$rawRequestParams 
	 * @param SimpleForm_Core_Field $field 
	 * @return string|array
	 */
	protected function submitField ($fieldName, & $rawRequestParams, SimpleForm_Core_Field & $field) {
		$result = null;
		if (!$field->Validators) {
			$submitValue = isset($rawRequestParams[$fieldName]) ? $rawRequestParams[$fieldName] : $field->GetValue();
			$result = $submitValue;
		} else {
			//x($field->Validators);
			foreach ($field->Validators as $validatorKey => $validator) {
				if ($validatorKey > 0) {
					$submitValue = $result; // take previous
				} else {
					// take submitted or default by SetDefault(array()) call in first verification loop
					$submitValue = isset($rawRequestParams[$fieldName]) ? $rawRequestParams[$fieldName] : $field->GetValue();
				}
				if ($validator instanceof Closure) {
					$safeValue = $validator(
						$submitValue, $fieldName, $field, $this
					);
				} else /*if (gettype($validator) == 'string')*/ {
					$validatorInstance = SimpleForm_Core_Validator::Create($validator, $this);
					$safeValue = $validatorInstance->Validate(
						$submitValue, $fieldName, $field
					);
				}
				if (is_null($safeValue)) $safeValue = '';
				if (
					(
						(gettype($safeValue) == 'string' && strlen($safeValue) === 0) || 
						(gettype($safeValue) == 'array' && count($safeValue) === 0)
					) && $field->Required
				) {
					$errorMsg = SimpleForm::$DefaultMessages[SimpleForm::REQUIRED];
					if ($this->Translate) {
						$errorMsg = call_user_func($this->Translator, $errorMsg);
					}
					$errorMsg = SimpleForm_Core_View::Format(
						$errorMsg, array($field->Label ? $field->Label : $fieldName)
					);
					$this->AddError(
						$errorMsg, $fieldName
					);
				}
				$result = $safeValue;
			}
		}
		return $result;
	}
}
