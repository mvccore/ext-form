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
 */
trait GetMethods {
	
	/**
	 * @inheritDocs
	 * @var \MvcCore\Controller
	 */
	public function GetController () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->controller;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetId () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->id;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetAction () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->action;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetMethod () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->method;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetTitle () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->title;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetEnctype () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->enctype;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetTarget () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->target;
	}

	/**
	 * @inheritDocs
	 * @return bool|NULL
	 */
	public function GetAutoComplete () {
		/** @var $this \MvcCore\Ext\Form */
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
		/** @var $this \MvcCore\Ext\Form */
		return $this->noValidate;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function GetAcceptCharsets () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->acceptCharsets;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetLang () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->lang;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetLocale () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->locale;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->cssClasses;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetAttributes () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->attributes;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetSuccessUrl () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->successUrl;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetPrevStepUrl () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->prevStepUrl;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetNextStepUrl () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->nextStepUrl;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetErrorUrl () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->errorUrl;
	}

	/**
	 * @inheritDocs
	 * @return int|NULL
	 */
	public function GetResult () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->result;
	}

	/**
	 * @inheritDocs
	 * @return callable|NULL
	 */
	public function GetTranslator () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->translator;
	}

	/**
	 * @inheritDocs
	 * @return bool
	 */
	public function GetTranslate () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->translate;
	}

	/**
	 * @inheritDocs
	 * @return bool
	 */
	public function GetDefaultRequired () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->defaultRequired;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetValues () {
		/** @var $this \MvcCore\Ext\Form */
		if (
			$this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED
		) $this->preDispatchLoadValues($this->getSession());
		return $this->values;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetErrors () {
		/** @var $this \MvcCore\Ext\Form */
		if (
			$this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED
		) $this->preDispatchLoadErrors($this->getSession());
		return $this->errors;
	}

	/**
	 * @inheritDocs
	 * @return int|NULL
	 */
	public function GetSessionExpiration () {
		/** @var $this \MvcCore\Ext\Form */
		if ($this->sessionExpiration === NULL) {
			$authClassesFullNames = [
				"\\MvcCore\\Ext\\Auth",
				"\\MvcCore\\Ext\\Auths\\Basic"
			];
			/** @var $auth \MvcCore\Ext\Auths\Basic */
			$auth = NULL;
			foreach ($authClassesFullNames as $authClassFullName) {
				if (class_exists($authClassFullName, TRUE)) {
					$auth = $authClassFullName::GetInstance();
					break;
				}
			}
			// If there is any authentication class, try to get
			// authenticated user and authorization expiration seconds value:
			if ($auth !== NULL) {
				$user = $auth->GetUser();
				if ($user !== NULL)
					$this->sessionExpiration = $auth->GetExpirationAuthorization();
			}
			// If there is nothing like that, set expiration until browser close:
			if ($this->sessionExpiration === NULL)
				$this->sessionExpiration = 0;
		}
		return $this->sessionExpiration;
	}

	/**
	 * @inheritDocs
	 * @return int|NULL
	 */
	public function GetBaseTabIndex () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->baseTabIndex;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @return int
	 */
	public function GetFieldNextAutoTabIndex () {
		/** @var $this \MvcCore\Ext\Form */
		$this->fieldsAutoTabIndex += 1;
		return $this->fieldsAutoTabIndex;
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function GetDefaultFieldsRenderMode () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->defaultFieldsRenderMode;
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function GetErrorsRenderMode () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->errorsRenderMode;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetViewScript () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->viewScript;
	}

	/**
	 * @inheritDocs
	 * @return string|\MvcCore\Ext\Forms\View
	 */
	public function GetViewClass () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->viewClass;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetJsSupportFiles () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->jsSupportFiles;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function & GetCssSupportFiles () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->cssSupportFiles;
	}

	/**
	 * @inheritDocs
	 * @return callable|NULL
	 */
	public function GetJsSupportFilesRenderer () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->jsSupportFilesRenderer;
	}

	/**
	 * @inheritDocs
	 * @return callable|NULL
	 */
	public function GetCssSupportFilesRenderer () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->cssSupportFilesRenderer;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @return bool
	 */
	public function GetFormTagRenderingStatus () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->formTagRendergingStatus;
	}
	
	/**
	 * @inheritDocs
	 * @param string $iniVarName
	 * @return int|NULL
	 */
	public function GetPhpIniSizeLimit ($iniVarName) {
		/** @var $this \MvcCore\Ext\Form */
		$rawIniValue = @ini_get($iniVarName);
		if (!$rawIniValue) return NULL;
		return static::ConvertBytesFromHumanForm($rawIniValue);
	}
	
	/**
	 * @inheritDocs
	 * @param int $bytes The number of bytes.
	 * @param int $precision Default `1`.
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
	 * @inheritDocs
	 * @param string $humanValue Readable bytes format e.g KB, MB, GB, TB, YB.
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
	 * @inheritDocs
	 * @return string|NULL
	 */
	public static function GetJsSupportFilesRootDir () {
		return static::$jsSupportFilesRootDir;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public static function GetCssSupportFilesRootDir () {
		return static::$cssSupportFilesRootDir;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public static function GetValidatorsNamespaces () {
		return static::$validatorsNamespaces;
	}

	/**
	 * @inheritDocs
	 * @param string $formId
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
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public static function GetAutoFocusedFormField () {
		if (self::$autoFocusedFormField) {
			list ($currentFormId, $currentFieldName) = self::$autoFocusedFormField;
			return self::GetById($currentFormId)->GetField($currentFieldName);
		}
		return NULL;
	}
}
