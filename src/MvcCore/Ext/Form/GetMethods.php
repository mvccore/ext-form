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
 * Trait for class `MvcCore\Ext\Form` containing getter methods for configurable properties.
 * @mixin \MvcCore\Ext\Form
 */
trait GetMethods {
	
	/**
	 * @inheritDoc
	 * @var \MvcCore\Controller
	 */
	public function GetController () {
		return $this->controller;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetId () {
		return $this->id;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetAction () {
		return $this->action;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetMethod () {
		return $this->method;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetTitle () {
		return $this->title;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetEnctype () {
		return $this->enctype;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetTarget () {
		return $this->target;
	}

	/**
	 * @inheritDoc
	 * @return bool|NULL
	 */
	public function GetAutoComplete () {
		return $this->autoComplete;
	}

	/**
	 * This Boolean attribute indicates that the form is not to be validated when
	 * submitted. If this attribute is not specified (and therefore the form is
	 * validated), this default setting can be overridden by a `formnovalidate`
	 * attribute on a `<button>` or `<input>` element belonging to the form.
	 * Only `TRUE` renders the form attribute.
	 * @return bool|NULL
	 */
	public function GetNoValidate () {
		return $this->noValidate;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public function GetAcceptCharsets () {
		return $this->acceptCharsets;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetLang () {
		return $this->lang;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetLocale () {
		return $this->locale;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetAttributes () {
		return $this->attributes;
	}

	/**
	 * @inheritDoc
	 * @return bool
	 */
	public function GetSubmit () {
		return $this->submit;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetSuccessUrl () {
		return $this->successUrl;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetPrevStepUrl () {
		return $this->prevStepUrl;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetNextStepUrl () {
		return $this->nextStepUrl;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetErrorUrl () {
		return $this->errorUrl;
	}

	/**
	 * @inheritDoc
	 * @param  bool $sorted
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ($sorted = TRUE) {
		if ($sorted && !$this->sorting->sorted)
			$this->SortChildren();
		return $this->children;
	}

	/**
	 * @inheritDoc
	 * @return int|NULL
	 */
	public function GetResult () {
		return $this->result;
	}

	/**
	 * @inheritDoc
	 * @return callable|NULL
	 */
	public function GetTranslator () {
		return $this->translator;
	}

	/**
	 * @inheritDoc
	 * @return bool
	 */
	public function GetTranslate () {
		return $this->translate;
	}

	/**
	 * @inheritDoc
	 * @return bool
	 */
	public function GetIntlExtLoaded () {
		return $this->intlExtLoaded;
	}

	/**
	 * @inheritDoc
	 * @return bool
	 */
	public function GetDefaultRequired () {
		return $this->defaultRequired;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetValues () {
		if ($this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $this->submit)) 
			$this->preDispatchLoadValues($this->getSession());
		return $this->values;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetErrors () {
		if ($this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $this->submit)) 
			$this->preDispatchLoadErrors($this->getSession());
		return $this->errors;
	}

	/**
	 * @inheritDoc
	 * @return int|NULL
	 */
	public function GetSessionExpiration () {
		if ($this->sessionExpiration === NULL) {
			$sessionClass = $this->application->GetSessionClass();
			$this->sessionExpiration = $sessionClass::GetSessionCsrfMaxTime();
		}
		return $this->sessionExpiration;
	}

	/**
	 * @inheritDoc
	 * @return int|NULL
	 */
	public function GetBaseTabIndex () {
		return $this->baseTabIndex;
	}
	
	/**
	 * @inheritDoc
	 * @return int
	 */
	public function GetFormRenderMode () {
		return $this->formRenderMode;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return int
	 */
	public function GetFieldNextAutoTabIndex () {
		$this->fieldsAutoTabIndex += 1;
		return $this->fieldsAutoTabIndex;
	}

	/**
	 * @inheritDoc
	 * @return int
	 */
	public function GetFieldsRenderModeDefault () {
		return $this->fieldsRenderModeDefault;
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function GetFieldsLabelSideDefault () {
		return $this->fieldsLabelSideDefault;
	}

	/**
	 * @inheritDoc
	 * @return int
	 */
	public function GetErrorsRenderMode () {
		return $this->errorsRenderMode;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetViewScript () {
		return $this->viewScript;
	}

	/**
	 * @inheritDoc
	 * @return string|\MvcCore\Ext\Forms\View
	 */
	public function GetViewClass () {
		return $this->viewClass;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetJsSupportFiles () {
		return $this->jsSupportFiles;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public function & GetCssSupportFiles () {
		return $this->cssSupportFiles;
	}

	/**
	 * @inheritDoc
	 * @return callable|NULL
	 */
	public function GetJsSupportFilesRenderer () {
		return $this->jsSupportFilesRenderer;
	}

	/**
	 * @inheritDoc
	 * @return callable|NULL
	 */
	public function GetCssSupportFilesRenderer () {
		return $this->cssSupportFilesRenderer;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return bool
	 */
	public function GetFormTagRenderingStatus () {
		return $this->formTagRendergingStatus;
	}
	
	/**
	 * @inheritDoc
	 * @param  string $iniVarName
	 * @return int|NULL
	 */
	public function GetPhpIniSizeLimit ($iniVarName) {
		$rawIniValue = @ini_get($iniVarName);
		if (!$rawIniValue) return NULL;
		return static::ConvertBytesFromHumanForm($rawIniValue);
	}
	

	/**
	 * @inheritDoc
	 * @param  int $index
	 * @return string
	 */
	public static function GetDefaultErrorMsg ($index) {
		return static::$defaultErrorMessages[$index];
	}

	/**
	 * @inheritDoc
	 * @param  int $bytes The number of bytes.
	 * @param  int $precision Default `1`.
	 * @return string
	 */
	public static function ConvertBytesIntoHumanForm ($bytes, $precision = 1) {
		$level = floor(log($bytes) / log(1024));
		$result = ($bytes / pow(1024, $level)) * 1;
		return sprintf(
			"%.0{$precision}F", 
			$result
		) . ' ' . static::$fileSizeUnits[$level];
	}
	
	/**
	 * @inheritDoc
	 * @param  string $humanValue Readable bytes format e.g KB, MB, GB, TB, YB.
	 * @return int
	 */
	public static function ConvertBytesFromHumanForm ($humanValue) {
		if (is_numeric($humanValue)) {
			return intval($humanValue);
		} else {
			$rawNumeric = preg_replace("#[^\.e\-E0-9]#", '', $humanValue);
			if (!is_numeric($rawNumeric)) $rawNumeric = '0';
			$numericValue = floatval($rawNumeric);
			$rawUnits = strtoupper(preg_replace("#[^kmgtpebKMGTPEB]#", '', $humanValue));
			$rawUnitsLength = strlen($rawUnits);
			if ($rawUnitsLength > 2) $rawUnits = substr($rawUnits, -2, 2);
			if ($rawUnitsLength == 1 && $rawUnits != 'B') $rawUnits .= 'B';
			$unitsLevel = array_search($rawUnits, static::$fileSizeUnits, TRUE);
			if ($unitsLevel === 0) return $numericValue;
			return intval(ceil($numericValue * pow(1024, $unitsLevel)));
		}
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public static function GetJsSupportFilesRootDir () {
		return static::$jsSupportFilesRootDir;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public static function GetCssSupportFilesRootDir () {
		return static::$cssSupportFilesRootDir;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public static function GetValidatorsNamespaces () {
		return static::$validatorsNamespaces;
	}

	/**
	 * @inheritDoc
	 * @param  string $formId
	 * @throws \RuntimeException
	 * @return \MvcCore\Ext\Form
	 */
	public static function GetById ($formId) {
		if (isset(self::$instances[$formId])) {
			return self::$instances[$formId];
		} else {
			throw new \RuntimeException(
				'No form instance exists under form id `'.$formId.'`.'
				. ' Check if searched form instance has been already initialized'
				. ' or if form id has been already set.'
			);
		}
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public static function GetAutoFocusedFormField () {
		if (self::$autoFocusedFormField) {
			list ($currentFormId, $currentFieldName) = self::$autoFocusedFormField;
			return self::GetById($currentFormId)->GetField($currentFieldName);
		}
		return NULL;
	}
}
