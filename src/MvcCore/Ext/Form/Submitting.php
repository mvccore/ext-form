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
	 * @param array $rawRequestParams optional
	 * @return array Array to list: `array($form->Result, $form->Data, $form->Errors);`
	 */
	public function Submit (array & $rawRequestParams = array()) {
		if ($this->dispatchState < 1) $this->Init();
		if (!$rawRequestParams) $rawRequestParams = $this->request->GetParams('.*');
		$this->submitSetStartResultState($rawRequestParams);
		$this->validateMaxPostSizeIfNecessary();
		$this->SubmitCsrfTokens($rawRequestParams);
		$this->submitAllFields($rawRequestParams);
		return array(
			$this->result,
			$this->values,
			$this->errors,
		);
	}

	protected function submitSetStartResultState (array & $rawRequestParams = array()) {
		if (!$this->customResultStates) {
			$this->result = \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS;
		} else {
			// try to find if there is any field name (button:submit or input:submit) 
			// in raw request params with submit start custom result state:
			$customResultStateDefined = FALSE;
			foreach ($this->customResultStates as $fieldName => $customResultState) {
				if (isset($rawRequestParams[$fieldName])) {
					$customResultStateDefined = TRUE;
					$this->result = $customResultState;
					break;
				}
			}
			if (!$customResultStateDefined) 
				$this->result = \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS;
		}
	}

	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/next
	 * step url address into final place and store everything into session.
	 * @return void
	 */
	public function SubmittedRedirect () {
		if ($this->dispatchState < 1) $this->Init();
		$url = '';
		$errorMsg = '';
		if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_ERRORS) {
			$url = $this->errorUrl;
			if (!$url) 
				$errorMsg = 'Specify `errorUrl` property.';
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS) {
			$url = $this->successUrl;
			if (!$url) 
				$errorMsg = 'Specify `successUrl` property.';
			$this->values = array();
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE) {
			$url = $this->nextStepUrl;
			if (!$url) 
				$errorMsg = 'Specify `nextStepUrl` property.';
			$this->values = array();
		}
		$session = & $this->getSession();
		$session->errors = $this->errors;
		$session->values = $this->values;
		if (!$url) throw new \RuntimeException(
			'['.__CLASS__.'] No url specified to redirect. ' . $errorMsg
		);
		self::Redirect($url, \MvcCore\Interfaces\IResponse::SEE_OTHER);
	}

	/**
	 * Get error message string from
	 * `\MvcCore\Ext\Form::$defaultErrorMessages`
	 * by given integer index.
	 * @param int $index
	 * @return string
	 */
	public function GetDefaultErrorMsg ($index) {
		return static::$defaultErrorMessages[$index];
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
	protected function submitAllFields (array & $rawRequestParams = array()) {
		foreach ($this->fields as $fieldName => & $field) {
			if ($field instanceof \MvcCore\Ext\Forms\Fields\Button) continue;
			$safeValue = $field->Submit($rawRequestParams);
			if ($safeValue !== NULL) {
				$field->SetValue($safeValue);
				$this->values[$fieldName] = $safeValue;
			}
		}
		//x($rawRequestParams);
		//xxx($this->values);
		$session = & $this->getSession();
		$session->errors = $this->errors;
		$session->values = $this->values;
	}

	/**
	 * Get cached validator instance by name. If validator instance doesn't exists
	 * in `$this->validators` array, create new validator instance.
	 * @param string $validatorName 
	 * @return \MvcCore\Ext\Forms\IValidator
	 */
	public function & GetValidator ($validatorName) {
		if (isset($this->validators[$validatorName])) {
			$validator = & $this->validators[$validatorName];
		} else {
			$validator = NULL;
			$toolClass = self::$toolClass;
			foreach (static::$validatorsNamespaces as $validatorsNamespace) {
				$validatorFullClassName =  $validatorsNamespace . $validatorName;
				if (
					class_exists($validatorFullClassName) &&
					$toolClass::CheckClassInterface(
						$validatorFullClassName, \MvcCore\Ext\Forms\IValidator::class, TRUE, TRUE
					)
				) {
					$validator = $validatorFullClassName::CreateInstance($this);
					break;
				}
			}
			if ($validator === NULL) $this->throwNewInvalidArgumentException(
				'Validator `' . $validatorName . '` not found in any namespace: `' 
				. implode('`, `', static::$validatorsNamespaces) . '`.'
			);
			$this->validators[$validatorName] = & $validator;
		}
		return $validator;
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
		if ($contentLength === NULL) $this->AddError(
			$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT)
		);
		$units = array('k' => 1000, 'm' => 1048576, 'g' => 1073741824);
		if (is_integer($rawMaxSize)) {
			$maxSize = intval($rawMaxSize);
		} else {
			$unit = strtolower(substr($rawMaxSize, -1));
			$rawMaxSize = substr($rawMaxSize, 0, strlen($rawMaxSize) - 1);
			$maxSize = isset($units[$unit])
				? intval($rawMaxSize) * $units[$unit]
				: intval($rawMaxSize);
		}
		if ($maxSize > 0 && $maxSize < $contentLength) {
			$viewClass = $this->viewClass;
			$this->AddError($viewClass::Format(
				$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::MAX_POST_SIZE),
				array($maxSize)
			));
		}
	}
}
