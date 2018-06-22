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
	public function Submit (array & $rawRequestParams = []) {
		if ($this->dispatchState < 1) $this->Init();
		if (!$rawRequestParams) $rawRequestParams = $this->request->GetParams('.*');
		$this
			->SubmitSetStartResultState($rawRequestParams)
			->SubmitValidateMaxPostSizeIfNecessary()
			->SubmitCsrfTokens($rawRequestParams)
			->SubmitAllFields($rawRequestParams)
			->SaveSession();
		return [
			$this->result,
			$this->values,
			$this->errors,
		];
	}

	/**
	 * Try to set up form submit result state into any special positive
	 * value by presented submit button name in `$rawRequestParams` array
	 * if there is any special submit result value configured by button names
	 * in `$form->customResultStates` array. If no special button submit result 
	 * value configured, submit result state is set to `1` by default.
	 * @param array $rawRequestParams 
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SubmitSetStartResultState (array & $rawRequestParams = []) {
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
		return $this;
	}

	/**
	 * Validate max. posted size in POST request body by `Content-Length` HTTP header.
	 * If there is no `Content-Length` request header, add error.
	 * If `Content-Length` value is bigger than `post_max_size` from PHP ini, add form error.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SubmitValidateMaxPostSizeIfNecessary () {
		if ($this->method != \MvcCore\Ext\Forms\IForm::METHOD_POST) return $this;
		$contentLength = $this->request->GetContentLength();
		$rawMaxSize = ini_get('post_max_size');
		if ($contentLength === NULL) $this->AddError(
			$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT)
		);
		$units = ['k' => 1024, 'm' => 1048576, 'g' => 1073741824];
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
				[$maxSize]
			));
		}
		return $this;
	}
	
	/**
	 * Go throught all fields, which are not `button:submit` or `input:submit` types
	 * and call on every `$field->Submit()` method to process all configured field validators.
	 * If method `$field->Submit()` returns anything else than `NULL`, that value is automaticly
	 * assigned under field name into form result values and into form field value.
	 * @param array $rawRequestParams
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SubmitAllFields (array & $rawRequestParams = []) {
		foreach ($this->fields as $fieldName => & $field) {
			if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) continue;
			$safeValue = $field->Submit($rawRequestParams);
			if ($safeValue !== NULL) {
				$field->SetValue($safeValue);
				$this->values[$fieldName] = $safeValue;
			}
		}
		return $this;
	}


	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/prev/next
	 * step url address into final place and store everything into session.
	 * You can also to redirect form after submit by yourself.
	 * @return void
	 */
	public function SubmittedRedirect () {
		if ($this->dispatchState < 1) $this->Init();
		$urlPropertyName = '';
		if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_ERRORS) {
			$urlPropertyName = 'errorUrl';
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS) {
			$urlPropertyName = 'successUrl';
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_PREV_PAGE) {
			$urlPropertyName = 'prevStepUrl';
		} else if ($this->result === \MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE) {
			$urlPropertyName = 'nextStepUrl';
		}
		$url = isset($this->{$urlPropertyName}) ? $this->{$urlPropertyName} : NULL;
		$errorMsg = $url ? '' : 'Specify `' . $urlPropertyName . '` property.' ;
		if ($this->result) $this->values = [];
		$this->SaveSession();
		if (!$url) throw new \RuntimeException(
			'['.__CLASS__.'] No url specified to redirect. ' . $errorMsg
		);
		self::Redirect($url, \MvcCore\Interfaces\IResponse::SEE_OTHER);
	}

	/**
	 * Get cached validator instance by name. If validator instance doesn't exist
	 * in `$this->validators` array, create new validator instance, cache it and return it.
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
					$validator = $validatorFullClassName::CreateInstance()
						->SetForm($this);
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
	 * Get error message string from internal protected static property
	 * `\MvcCore\Ext\Form::$defaultErrorMessages` by given integer index.
	 * @param int $index
	 * @return string
	 */
	public function GetDefaultErrorMsg ($index) {
		return static::$defaultErrorMessages[$index];
	}
}
