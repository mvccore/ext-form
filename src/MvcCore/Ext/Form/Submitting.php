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
		$this->SubmitAllFields($rawParams);
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
		$this
			->setSessionErrors($this->errors)
			->setSessionValues($this->values);
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
	public function SubmitAllFields ($rawRequestParams = array()) {
		foreach ($this->fields as $fieldName => & $field) {
			$safeValue = $field->Submit($rawRequestParams);
			if ($safeValue !== NULL) {
				$field->SetValue($safeValue);
				if (!($field instanceof \MvcCore\Ext\Forms\Fields\Button))
					$this->values[$fieldName] = $safeValue;
			}
		}
		//x($rawRequestParams);
		//xxx($this->values);
		$session = & $this->getSession();
		$session->errors = & $this->errors;
		$session->values = & $this->values;
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
				$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT),
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
				$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::MAX_POST_SIZE),
				$maxSize
			));
			$this->result = \MvcCore\Ext\Forms\IForm::RESULT_ERRORS;
		}
	}
}
