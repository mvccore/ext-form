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

/**
 * Trait for class `MvcCore\Ext\Form` containing submitting logic and methods.
 * @mixin \MvcCore\Ext\Form
 */
trait Submitting {

	/**
	 * @inheritDocs
	 * @param  array $rawRequestParams Optional, raw `$_POST` or `$_GET` array could be passed.
	 * @return array An array to list: `[$form->result, $form->data, $form->errors];`
	 */
	public function Submit (array & $rawRequestParams = []) {
		/** @var $this \MvcCore\Ext\Form */
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init(TRUE);
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->PreDispatch(TRUE);
		$submitWithParams = count($rawRequestParams) > 0;
		if (!$submitWithParams) {
			$sourceType = $this->method === \MvcCore\Ext\IForm::METHOD_GET
				? \MvcCore\IRequest::PARAM_TYPE_QUERY_STRING
				: \MvcCore\IRequest::PARAM_TYPE_INPUT;
			$paramsKeys = array_keys($this->fields);
			if ($this->csrfEnabled)
				$paramsKeys[] = $this->getSession()->csrf[0];
			$rawRequestParams = $this->request->GetParams(
				FALSE, $paramsKeys, $sourceType
			);
		}
		$this->SubmitSetStartResultState($rawRequestParams);
		if ($submitWithParams) {
			$this->SubmitAllFields($rawRequestParams);
		} else if ($this->SubmitValidateMaxPostSizeIfNecessary()) {
			$this->application->ValidateCsrfProtection();
			$this->SubmitCsrfTokens($rawRequestParams);// deprecated, but working in all browsers
			if (!$this->application->GetTerminated())
				$this->SubmitAllFields($rawRequestParams);
		}
		if (!$this->application->GetTerminated())
			$this->SaveSession();
		return [
			$this->result,
			$this->values,
			$this->errors,
		];
	}

	/**
	 * @inheritDocs
	 * @param  array $rawRequestParams
	 * @return \MvcCore\Ext\Form
	 */
	public function SubmitSetStartResultState (array & $rawRequestParams = []) {
		if (!$this->customResultStates) {
			$this->result = \MvcCore\Ext\IForm::RESULT_SUCCESS;
		} else {
			// try to find if there is any field name (button:submit or input:submit)
			// in raw request params with submit start custom result state:
			$submitFields = array_intersect_key($this->submitFields, $rawRequestParams);
			if (count($submitFields) === 1) {
				$submitFieldsKeys = array_keys($submitFields);
				$submitField = $submitFields[$submitFieldsKeys[0]];
				$this->result = $submitField->GetCustomResultState();
				if ($this->result === NULL || $submitField->GetDisabled())
					$this->result = \MvcCore\Ext\IForm::RESULT_SUCCESS;
			} else {
				$this->result = \MvcCore\Ext\IForm::RESULT_SUCCESS;
			}
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return bool
	 */
	public function SubmitValidateMaxPostSizeIfNecessary () {
		if ($this->method != \MvcCore\Ext\IForm::METHOD_POST) 
			return TRUE;
		$contentLength = $this->request->GetContentLength();
		if ($contentLength === NULL) {
			$errorMsg = $this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::EMPTY_CONTENT);
			if ($this->translate)
				$errorMsg = call_user_func($this->translator, $errorMsg);
			$this->AddError($errorMsg);
		}
		$maxSize = $this->GetPhpIniSizeLimit('post_max_size');
		if ($maxSize !== NULL && $maxSize < $contentLength) {
			$viewClass = $this->viewClass;
			$displayErrors = @ini_get("display_errors");
			if ($displayErrors || strtolower($displayErrors) == 'on') {
				$obContent = ob_get_contents();
				if (preg_match(
					"#Warning([^\:]*)\:\s+POST Content\-Length of ([0-9]+) bytes exceeds the limit of ([0-9]+) bytes#", 
					$obContent
				)) ob_clean();
			}
			$errorMsg = $this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::MAX_POST_SIZE);
			if ($this->translate)
				$errorMsg = call_user_func($this->translator, $errorMsg);
			$this->AddError($viewClass::Format(
				$errorMsg,
				[static::ConvertBytesIntoHumanForm($maxSize)]
			));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * @inheritDocs
	 * @param  array $rawRequestParams
	 * @return \MvcCore\Ext\Form
	 */
	public function SubmitAllFields (array & $rawRequestParams = []) {
		$rawRequestParams = $this->submitAllFieldsEncodeAcceptCharsets($rawRequestParams);
		$this->submitAllFieldsDisabledByFieldsets();
		$this->values = [];
		foreach ($this->fields as $fieldName => $field) {
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
	 * @inheritDocs
	 * @return void
	 */
	public function SubmittedRedirect () {
		if ($this->application->GetTerminated())
			return;
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init(TRUE);
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->PreDispatch(TRUE);
		$urlPropertyName = '';
		$redirectMsg = '';
		if ($this->result === \MvcCore\Ext\IForm::RESULT_ERRORS) {
			$urlPropertyName = 'errorUrl';
			$redirectMsg = 'error URL';
		} else if (($this->result & \MvcCore\Ext\IForm::RESULT_SUCCESS) != 0) {
			$urlPropertyName = 'successUrl';
			$redirectMsg = 'success URL';
		} else if (($this->result & \MvcCore\Ext\IForm::RESULT_PREV_PAGE) != 0) {
			$urlPropertyName = 'prevStepUrl';
			$redirectMsg = 'previous step URL';
		} else if (($this->result & \MvcCore\Ext\IForm::RESULT_NEXT_PAGE) != 0) {
			$urlPropertyName = 'nextStepUrl';
			$redirectMsg = 'next step URL';
		} else {
			$customResultStates = array_unique(array_values($this->customResultStates));
			foreach ($customResultStates as $customResultState) {
				if (($this->result & $customResultState) != 0) {
					$urlPropertyName = 'successUrl';
					$redirectMsg = 'success URL by custom result state';
					break;
				}
			}
		}
		$url = isset($this->{$urlPropertyName}) ? $this->{$urlPropertyName} : NULL;
		$errorMsg = $url ? '' : 'Specify `' . $urlPropertyName . '` property.' ;
		$this->SaveSession();
		if (!$url && $this->result >= \MvcCore\Ext\IForm::RESULT_ERRORS && $this->result <= \MvcCore\Ext\IForm::RESULT_NEXT_PAGE)
			throw new \RuntimeException(
				'['.get_class().'] No url specified to redirect. ' . $errorMsg
			);
		if ($url) self::Redirect(
			$url,
			\MvcCore\IResponse::SEE_OTHER,
			'Form has been submitted and redirected to ' . $redirectMsg
		);
	}

	/**
	 * @inheritDocs
	 * @param  string $validatorName
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public function GetValidator ($validatorName) {
		if (isset($this->validators[$validatorName])) {
			$validator = $this->validators[$validatorName];
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
				"Validator `{$validatorName}` not found in any namespace: `"
				. implode('`, `', static::$validatorsNamespaces) . '`.'
			);
			$this->validators[$validatorName] = $validator;
		}
		return $validator;
	}

	/**
	 * @inheritDocs
	 * @param  int $index
	 * @return string
	 */
	public function GetDefaultErrorMsg ($index) {
		return static::$defaultErrorMessages[$index];
	}

	/**
	 * Go through all children and try to find disabled fieldset.
	 * If any fieldset is disabled - set it's fields also disabled 
	 * and also disable all nested fieldsets with it's fiels.
	 * @return void
	 */
	protected function submitAllFieldsDisabledByFieldsets () {
		if (count($this->fieldsets) === 0) return;
		foreach ($this->GetChildren() as $child) 
			/** @var \MvcCore\Ext\Forms\Fieldset $child */
			if ($child instanceof \MvcCore\Ext\Forms\IFieldset) 
				$this->submitAllFieldsDisabledByFieldsetRecursive($child, $child->GetDisabled());
	}

	/**
	 * Go through all nested fieldsets in given fieldset and call this method.
	 * If second param is true, set all fields in given fieldset disabled.
	 * @param  \MvcCore\Ext\Forms\IFieldset $fieldset 
	 * @param  bool|NULL $disabled 
	 * @return void
	 */
	protected function submitAllFieldsDisabledByFieldsetRecursive (\MvcCore\Ext\Forms\IFieldset $fieldset, $disabled = NULL) {
		if ($disabled) 
			foreach ($fieldset->GetFields() as $field) 
				if ($field instanceof \MvcCore\Ext\Forms\Fields\IVisibleField)
					$field->SetDisabled(TRUE);
		$nestedFieldsets = $fieldset->GetFieldsets();
		if (count($nestedFieldsets) > 0) {
			foreach ($nestedFieldsets as $nestedFieldset) {
				$disabledLocal = (
					$disabled || ($disabled === NULL && $nestedFieldset->GetDisabled())
				);
				$this->submitAllFieldsDisabledByFieldsetRecursive($nestedFieldset, $disabledLocal);
			}
		}
	}

	/**
	 * If form has defined any `accept-charset` attribute values,
	 * go through all accept charset(s) and try to transcode all raw values
	 * and collect translation statistics from this process. Then decode
	 * best translation charset and return by given param `$rawRequestParams`
	 * new translated raw values by first best charset in `accept-charset` attribute.
	 * @param  array $rawRequestParams
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
	 *  - `0` - H ow many items has been transcoded without error.
	 *  - `1` - How many items has been transcoded.
	 *  - `2` - Transcoded raw input string by `iconv()`.
	 * @param  string|\string[] $rawValue
	 * @param  string           $fromEncoding
	 * @param  string           $toEncoding
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
	 *  - `0` - How many items has been transcoded without error.
	 *  - `1` - How many items has been transcoded.
	 *  - `2` - Transcoded raw input string by `iconv()`.
	 * @param  string|\string[] $rawValue
	 * @param  string           $fromEncoding
	 * @param  string           $toEncoding
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
