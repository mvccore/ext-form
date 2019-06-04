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

/**
 * Trait for class `MvcCore\Ext\Form` containing submitting logic and methods.
 */
trait Submitting
{
	/**
	 * Process standard low level submit process.
	 * If no params passed as first argument, all params from object
	 * `\MvcCore\Application::GetInstance()->GetRequest()` are used.
	 * - If fields are not initialized - initialize them by calling `$form->Init();`.
	 * - Check maximum post size by php configuration if form is posted.
	 * - Check cross site request forgery tokens with session tokens.
	 * - Process all field values and their validators and call `$form->AddError()` where necessary.
	 *	 `AddError()` method automatically switch `$form->Result` property to zero - `0`, it means error submit result.
	 * Return array with form result, safe values from validators and errors array.
	 * @param array $rawRequestParams optional
	 * @return array An array to list: `[$form->result, $form->data, $form->errors];`
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
		/** @var $this \MvcCore\Ext\Forms\IForm */
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
	 * Validate maximum posted size in POST request body by `Content-Length` HTTP header.
	 * If there is no `Content-Length` request header, add error.
	 * If `Content-Length` value is bigger than `post_max_size` from PHP INI, add form error.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SubmitValidateMaxPostSizeIfNecessary () {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($this->method != \MvcCore\Ext\Forms\IForm::METHOD_POST) return $this;
		$contentLength = $this->request->GetContentLength();
		if ($contentLength === NULL) $this->AddError(
			$this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT)
		);
		$maxSize = static::GetPhpIniSizeLimit('post_max_size');
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
	 * Go through all fields, which are not `button:submit` or `input:submit` types
	 * and call on every `$field->Submit()` method to process all configured field validators.
	 * If method `$field->Submit()` returns anything else than `NULL`, that value is automatically
	 * assigned under field name into form result values and into form field value.
	 * @param array $rawRequestParams
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SubmitAllFields (array & $rawRequestParams = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$rawRequestParams = $this->submitAllFieldsEncodeAcceptCharsets($rawRequestParams);
		$this->values = [];
		foreach ($this->fields as $fieldName => & $field) {
			if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) continue;
			$safeValue = $field->Submit($rawRequestParams);
			if ($safeValue === NULL) {
				$this->values[$fieldName] = NULL;
			} else {
				$field->SetValue($safeValue);
				$this->values[$fieldName] = $safeValue;
			}
		}
		return $this;
	}


	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/prev/next
	 * step URL address into final place and store everything into session.
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
		if (!$url && $this->result > -1 && $this->result < 4) {
			$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
			throw new \RuntimeException(
				'['.$selfClass.'] No url specified to redirect. ' . $errorMsg
			);
		}
		if ($url) self::Redirect($url, \MvcCore\IResponse::SEE_OTHER);
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
						$validatorFullClassName, 'MvcCore\\Ext\\Forms\\IValidator', TRUE, TRUE
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

	/**
	 * If form has defined any `accept-charset` attribute values,
	 * go through all accept charset(s) and try to transcode all raw values 
	 * and collect translation statistics from this process. Then decode 
	 * best translation charset and return by given param `$rawRequestParams`
	 * new translated raw values by first best charset in `accept-charset` attribute.
	 * @param array & $rawRequestParams 
	 * @return array
	 */
	protected function & submitAllFieldsEncodeAcceptCharsets (array & $rawRequestParams = []) {
		if (count($this->acceptCharsets) === 0) return $rawRequestParams;
		$toEncoding = strtoupper($this->GetResponse()->GetEncoding());
		if (!static::$toolClass) static::$toolClass = \MvcCore\Application::GetInstance()->GetToolClass();
		// try to translate one accepting charset by one and collect success statistics
		$bestCharset = NULL;
		$translatedStats = [];
		$translatedRawRequestParams = [];
		foreach ($this->acceptCharsets as $acceptCharset) {
			if ($acceptCharset === 'UNKNOWN') $acceptCharset = $toEncoding;
			list($stats, $total, $rawTranslatedValues) = $this->encodeAcceptCharsetsArrayOrString(
				$rawRequestParams, $acceptCharset, $toEncoding
			);
			$translatedRawRequestParams[$acceptCharset] = $rawTranslatedValues;
			if ($total === $stats) {
				$bestCharset = $acceptCharset;
				break;
			} else {
				$translatedStats[$acceptCharset] = $stats;	
			}
		}
		if (!$bestCharset) {
			// decide which charset is the best
			asort($translatedStats);
			$translatedStats = array_reverse($translatedStats);
			reset($translatedStats);
			$firstCharset = current(array_keys($translatedStats));
			$bestScore = $translatedStats[$firstCharset];
			foreach ($this->acceptCharsets as $acceptCharset) {
				if ($acceptCharset === 'UNKNOWN') $acceptCharset = $toEncoding;
				if ($translatedStats[$acceptCharset] === $bestScore) {
					$bestCharset = $acceptCharset;
					break;
				}
			}
		}
		return $translatedRawRequestParams[$bestCharset];
	}

	/**
	 * Try to encode raw input `array` or `string` by `iconv()` 
	 * from given `$fromEncoding` charset to given `$toEncoding` 
	 * charset. Return array with records:
	 * - `0` - Wow many items has been transcoded without error.
	 * - `1` - How many items has been transcoded.
	 * - `2` - Transcoded raw input string by `iconv()`.
	 * @param string|\string[] $rawValue 
	 * @param string $fromEncoding 
	 * @param string $toEncoding 
	 * @return array
	 */
	protected function encodeAcceptCharsetsArrayOrString (& $rawValue, $fromEncoding, $toEncoding) {
		if (gettype($rawValue) == 'array') {
			$stats = 0;
			$totalCount = 0;
			$rawTranslatedValues = [];
			foreach ($rawValue as $rawKey => & $rawValueItem) {
				if (gettype($rawValueItem) == 'array') {
					list ($rawItemStats, $rawItemsTotal, $rawTranslatedValue) = $this->encodeAcceptCharsetsArrayOrString(
						$rawValueItem, $fromEncoding, $toEncoding
					);
				} else {
					list ($rawItemStats, $rawItemsTotal, $rawTranslatedValue) = $this->encodeAcceptCharsetsString(
						$rawValueItem, $fromEncoding, $toEncoding
					);
				}
				$totalCount += $rawItemsTotal;
				if ($rawItemStats) {
					$stats += $rawItemStats;
					$rawTranslatedValues[$rawKey] = $rawTranslatedValue;
				} else {
					$rawTranslatedValues[$rawKey] = $rawValue;
				}
			}
			return [$stats, $totalCount, $rawTranslatedValues];
		} else {
			return $this->encodeAcceptCharsetsString(
				$rawValue, $fromEncoding, $toEncoding
			);
		}
	}

	/**
	 * Try to encode raw input `string` by `iconv()` 
	 * from given `$fromEncoding` charset to given `$toEncoding` 
	 * charset. Return array with records:
	 * - `0` - Wow many items has been transcoded without error.
	 * - `1` - How many items has been transcoded.
	 * - `2` - Transcoded raw input string by `iconv()`.
	 * @param string|\string[] $rawValue 
	 * @param string $fromEncoding 
	 * @param string $toEncoding 
	 * @return array
	 */
	protected function encodeAcceptCharsetsString (& $rawValue, $fromEncoding, $toEncoding) {
		$errors = [];
		$toolClass = static::$toolClass;
		$translatedValue = $toolClass::Invoke(
			'iconv', 
			[$fromEncoding, $toEncoding . '//TRANSLIT', $rawValue], 
			function () use (& $errors) {
				$errors[] = func_get_args();
			}
		);
		if ($errors || $translatedValue === FALSE) {
			return [0, 1, NULL];
		} else {
			return [1, 1, $translatedValue];
		}
	}
}
