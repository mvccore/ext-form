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

trait Submitting
{
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
		if ($this->initialized < 1) $this->Init();
		$this->validateMaxPostSizeIfNecessary();
		if (!$rawParams) $rawParams = $this->request->GetParams('.*');
		$this->ValidateCsrf($rawParams);
		$this->submitFields($rawParams);
		return array(
			$this->result,
			$this->values,
			$this->errors,
		);
	}

	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/next
	 * step url address into final place and store everything into session.
	 * @return void
	 */
	public function SubmittedRedirect () {
		if ($this->initialized < 1) $this->Init();
		include_once('Form/Core/Helpers.php');
		$url = '';
		if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_ERRORS) {
			$url = $this->errorUrl;
			if (!$url) $errorMsg = 'Specify `errorUrl` property.';
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS) {
			$url = $this->successUrl;
			if (!$url) $errorMsg = 'Specify `successUrl` property.';
			$this->values = array();
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE) {
			$url = $this->nextStepUrl;
			if (!$url) $errorMsg = 'Specify `nextStepUrl` property.';
			$this->values = array();
		}
		$this
			->setSessionErrors($this->errors)
			->setSessionValues($this->values);
		if (!$url) throw new \RuntimeException(
			'['.__CLASS__.'] No url specified to redirect. ' . $errorMsg
		);
		self::Redirect($url, \MvcCore\Interfaces\IResponse::SEE_OTHER);
	}

	/**
	 * Process single field configured validators and add errors where necessary.
	 * Clean client value to safe value by configured validator classes for this field.
	 * Return safe value.
	 * @param string				$fieldName
	 * @param array					$rawRequestParams
	 * @param \MvcCore\Ext\Form\Core\Field $field
	 * @return string|array
	 */
	protected function submitField ($fieldName, & $rawRequestParams, \MvcCore\Ext\Form\Core\Field & $field) {
		$result = null;
		if (!$field->Validators) {
			$submitValue = isset($rawRequestParams[$fieldName]) ? $rawRequestParams[$fieldName] : $field->GetValue();
			$result = $submitValue;
		} else {
			include_once('Validator.php');
			include_once('Configuration.php');
			include_once('View.php');
			foreach ($field->Validators as $validatorKey => $validator) {
				if ($validatorKey > 0) {
					$submitValue = $result; // take previous
				} else {
					// take submitted or default by SetDefault(array()) call in first verification loop
					$submitValue = isset($rawRequestParams[$fieldName]) ? $rawRequestParams[$fieldName] : $field->GetValue();
				}
				if ($validator instanceof \Closure) {
					$safeValue = $validator(
						$submitValue, $fieldName, $field, $this
					);
				} else /*if (gettype($validator) == 'string')*/ {
					$validatorInstance = Validator::Create($this, $validator);
					$safeValue = $validatorInstance->Validate(
						$submitValue, $fieldName, $field
					);
				}
				// set safe value as field submit result value
				$result = $safeValue;
			}
			if (is_null($safeValue)) $safeValue = '';
			// add required error message if necessary
			if (
				(
					(gettype($safeValue) == 'string' && strlen($safeValue) === 0) ||
					(gettype($safeValue) == 'array' && count($safeValue) === 0)
				) && $field->Required
			) {
				$errorMsg = Configuration::$DefaultMessages[Configuration::REQUIRED];
				if ($this->Translate) {
					$errorMsg = call_user_func($this->Translator, $errorMsg);
				}
				$errorMsg = View::Format(
					$errorMsg, array($field->Label ? $field->Label : $fieldName)
				);
				$this->AddError(
					$errorMsg, $fieldName
				);
			}
		}
		return $result;
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
		include_once(__DIR__.'/../Button.php');
		include_once('Helpers.php');
		include_once('Field.php');
		foreach ($this->Fields as $fieldName => & $field) {
			/** @var $field \MvcCore\Ext\Form\Core\Field */
			if ($field->Readonly || $field->Disabled) {
				$safeValue = $field->GetValue(); // get by SetValues(array()) call
			} else {
				$safeValue = $this->submitField($fieldName, $rawRequestParams, $field);
			}
			if (is_null($safeValue)) $safeValue = '';
			$field->SetValue($safeValue);
			if (!($field instanceof Form\Button)) {
				$this->Data[$fieldName] = $safeValue;
			}
		}
		//x($rawRequestParams);
		//xxx($this->Data);
		Helpers::SetSessionErrors($this->Id, $this->Errors);
		Helpers::SetSessionData($this->Id, $this->Data);
	}

	/**
	 * Validate max. posted size in POST request body.
	 * If there is no `Content-Length` request header, add error.
	 * If `Content-Length` value is bigger than `post_max_size` from PHP ini, add error.
	 * @return void
	 */
	protected function validateMaxPostSizeIfNecessary () {
		if ($this->method != \MvcCore\Ext\Forms\IForm::METHOD_POST) return;
		$contentLength = $this->request->GetContentLength();
		$rawMaxSize = ini_get('post_max_size');
		if ($contentLength === NULL) {
			$this->AddError(sprintf(
				\MvcCore\Ext\Form::getError(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT),
				$rawMaxSize
			));
			$this->result = \MvcCore\Ext\Forms\IForm::RESULT_ERRORS;
		}
		$units = array('k' => 1000, 'm' => 1048576, 'g' => 1073741824);
		if (is_integer($rawMaxSize)) {
			$maxSize = intval($rawMaxSize);
		} else {
			$unit = strtolower(substr($rawMaxSize, -1));
			$rawMaxSize = substr($rawMaxSize, 0, strlen($rawMaxSize) - 1);
			if (isset($units[$unit])) {
				$maxSize = intval($rawMaxSize) * $units[$unit];
			} else {
				$maxSize = intval($rawMaxSize);
			}
		}
		if ($maxSize > 0 && $maxSize < $contentLength) {
			$this->AddError(sprintf(
				\MvcCore\Ext\Form::getError(\MvcCore\Ext\Forms\IError::MAX_POST_SIZE),
				$maxSize
			));
			$this->result = \MvcCore\Ext\Forms\IForm::RESULT_ERRORS;
		}
	}

	/**
	 * Get error message string from
	 * `\MvcCore\Ext\Form::$defaultErrorMessages`
	 * by given integer index.
	 * @param int $index
	 * @return string
	 */
	protected static function getError ($index) {
		return static::$defaultErrorMessages[$index];
	}
}
