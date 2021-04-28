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
 * Trait for class `MvcCore\Ext\Form` containing setter methods for configurable properties.
 * @mixin \MvcCore\Ext\Form
 */
trait SetMethods {
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\IController $controller 
	 * @return \MvcCore\Ext\Form
	 */
	public function SetController (\MvcCore\IController $controller) {
		/** @var \MvcCore\Controller $controller */
		$this->controller = $controller;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @requires
	 * @param  string $id
	 * @return \MvcCore\Ext\Form
	 */
	public function SetId ($id) {
		$this->id = $id;
		self::$instances[$id] = $this;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $url
	 * @return \MvcCore\Ext\Form
	 */
	public function SetAction ($url = NULL) {
		$this->action = $url;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $method
	 * @return \MvcCore\Ext\Form
	 */
	public function SetMethod ($method = \MvcCore\Ext\IForm::METHOD_POST) {
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL  $title
	 * @param  boolean|NULL $translateTitle
	 * @return \MvcCore\Ext\Form
	 */
	public function SetTitle ($title, $translateTitle = NULL) {
		$this->title = $title;
		if ($translateTitle !== NULL)
			$this->translateTitle = $translateTitle;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $enctype
	 * @return \MvcCore\Ext\Form
	 */
	public function SetEnctype ($enctype = \MvcCore\Ext\IForm::ENCTYPE_URLENCODED) {
		$this->enctype = $enctype;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Form
	 */
	public function SetTarget ($target = '_self') {
		$this->target = $target;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-autocomplete
	 * @param  bool|string $autoComplete Possible values are `'on' | TRUE | 'off' | FALSE | NULL`.
	 * @return \MvcCore\Ext\Form
	 */
	public function SetAutoComplete ($autoComplete = FALSE) {
		if ($autoComplete === 'off' || $autoComplete === FALSE) {
			$this->autoComplete = FALSE;
		} else if ($autoComplete === 'on' || $autoComplete === TRUE) {
			$this->autoComplete = TRUE;
		} else {
			$this->autoComplete = NULL;
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  bool|NULL $noValidate Only `TRUE` renders the form attribute.
	 * @return \MvcCore\Ext\Form
	 */
	public function SetNoValidate ($noValidate = TRUE) {
		if ($noValidate === TRUE) {
			$this->noValidate = TRUE;
		} else {
			$this->noValidate = NULL;
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \string[] $acceptCharsets
	 * @return \MvcCore\Ext\Form
	 */
	public function SetAcceptCharsets ($acceptCharsets = []) {
		$this->acceptCharsets = $acceptCharsets;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $lang
	 * @return \MvcCore\Ext\Form
	 */
	public function SetLang ($lang = NULL) {
		$this->lang = $lang;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $locale
	 * @return \MvcCore\Ext\Form
	 */
	public function SetLocale ($locale = NULL) {
		$this->locale = strtoupper($locale);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form
	 */
	public function SetCssClasses ($cssClasses) {
		$cssClassesArr = is_array($cssClasses)
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = $cssClassesArr;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $attributes
	 * @return \MvcCore\Ext\Form
	 */
	public function SetAttributes (array $attributes = []) {
		$this->attributes = $attributes;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $url
	 * @return \MvcCore\Ext\Form
	 */
	public function SetSuccessUrl ($url = NULL) {
		$this->successUrl = $url;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $url
	 * @return \MvcCore\Ext\Form
	 */
	public function SetPrevStepUrl ($url = NULL) {
		$this->nextStepUrl = $url;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $url
	 * @return \MvcCore\Ext\Form
	 */
	public function SetNextStepUrl ($url = NULL) {
		$this->nextStepUrl = $url;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $url
	 * @return \MvcCore\Ext\Form
	 */
	public function SetErrorUrl ($url = NULL) {
		$this->errorUrl = $url;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  int|NULL $result
	 * @return \MvcCore\Ext\Form
	 */
	public function SetResult ($result = \MvcCore\Ext\IForm::RESULT_SUCCESS) {
		$this->result = $result;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  callable|NULL $handler
	 * @return \MvcCore\Ext\Form
	 */
	public function SetTranslator (callable $translator = NULL) {
		if ($translator !== NULL && is_callable($translator))
			$this->translator = $translator;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  bool $defaultRequired
	 * @return \MvcCore\Ext\Form
	 */
	public function SetDefaultRequired ($defaultRequired = TRUE) {
		$this->defaultRequired = $defaultRequired;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $values                     Key value array with keys as field names and values for fields.
	 * @param  bool  $caseInsensitive            If `TRUE`, set up values from `$values` with keys in case insensitive mode.
	 * @param  bool  $clearPreviousSessionValues If `TRUE`, clear all previous data records for this form from session.
	 * @return \MvcCore\Ext\Form
	 */
	public function SetValues (array $values = [], $caseInsensitive = FALSE, $clearPreviousSessionValues = FALSE) {
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init();
		if ($clearPreviousSessionValues) $this->ClearSession();
		$defaultsKeys = $caseInsensitive
			? ',' . implode(',', array_keys($values)) . ','
			: '' ;
		foreach ($this->fields as $fieldName => $field) {
			if (isset($values[$fieldName])) {
				$fieldValue = $values[$fieldName];
			} else if ($caseInsensitive) {
				$defaultsKeyPos = stripos($defaultsKeys, ','.$fieldName.',');
				if ($defaultsKeyPos === FALSE) continue;
				$defaultsKey = substr($defaultsKeys, $defaultsKeyPos + 1, strlen($fieldName));
				$fieldValue = $values[$defaultsKey];
			} else {
				continue;
			}
			$fieldValuesIsStr = is_string($fieldValue);
			if (
				$fieldValue !== NULL && (
					!$fieldValuesIsStr || ($fieldValuesIsStr && $fieldValue != '')
				)
			) {
				$field->SetValue($fieldValue);
				$this->values[$fieldName] = $fieldValue;
			}
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $errorsCollection
	 * @return \MvcCore\Ext\Form
	 */
	public function SetErrors ($errorsCollection = []) {
		$this->errors = [];
		foreach ($errorsCollection as $errorMsgAndFieldNames) {
			list ($errorMsg, $fieldNames) = $errorMsgAndFieldNames;
			$this->AddError(
				$errorMsg, is_array($fieldNames) ? $fieldNames : [$fieldNames]
			);
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  int $seconds
	 * @return \MvcCore\Ext\Form
	 */
	public function SetSessionExpiration ($seconds = 0) {
		$this->sessionExpiration = $seconds;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  int $baseTabIndex
	 * @return \MvcCore\Ext\Form
	 */
	public function SetBaseTabIndex ($baseTabIndex = 0) {
		$this->baseTabIndex = $baseTabIndex;
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @param  int $formRenderMode
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFormRenderMode ($formRenderMode = \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE) {
		$this->formRenderMode = $formRenderMode;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  int $fieldsRenderModeDefault
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFieldsRenderModeDefault ($fieldsRenderModeDefault = \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL) {
		$this->fieldsRenderModeDefault = $fieldsRenderModeDefault;
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @param  string $fieldsLabelSideDefault
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFieldsLabelSideDefault ($fieldsLabelSideDefault = \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
		$this->fieldsLabelSideDefault = $fieldsLabelSideDefault;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  int $errorsRenderMode
	 * @return \MvcCore\Ext\Form
	 */
	public function SetErrorsRenderMode ($errorsRenderMode = \MvcCore\Ext\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
		$this->errorsRenderMode = $errorsRenderMode;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form
	 */
	public function SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $viewClass
	 * @return \MvcCore\Ext\Form
	 */
	public function SetViewClass ($viewClass = 'MvcCore\\Ext\\Forms\\View') {
		$this->viewClass = $viewClass;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $jsFilesClassesAndConstructorParams
	 * @return \MvcCore\Ext\Form
	 */
	public function SetJsSupportFiles (array $jsRelPathsClassNamesAndParams = []) {
		$this->jsSupportFiles = [];
		foreach ($jsRelPathsClassNamesAndParams as $jsRelPathClassNameAndParams) {
			list ($jsRelativePath, $jsClassName, $constructorParams) = $jsRelPathClassNameAndParams;
			$this->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \string[] $cssRelativePaths
	 * @return \MvcCore\Ext\Form
	 */
	public function SetCssSupportFiles (array $cssRelativePaths = []) {
		$this->cssSupportFiles = [];
		foreach ($cssRelativePaths as $cssRelativePath)
			$this->AddCssSupportFile($cssRelativePath);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  callable|NULL $jsSupportFilesRenderer
	 * @return \MvcCore\Ext\Form
	 */
	public function SetJsSupportFilesRenderer (callable $jsSupportFilesRenderer) {
		$this->jsSupportFilesRenderer = $jsSupportFilesRenderer;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  callable|NULL $cssSupportFilesRenderer
	 * @return \MvcCore\Ext\Form
	 */
	public function SetCssSupportFilesRenderer (callable $cssSupportFilesRenderer) {
		$this->cssSupportFilesRenderer = $cssSupportFilesRenderer;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @internal
	 * @param  bool $formTagRenderingStatus
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFormTagRenderingStatus ($formTagRenderingStatus = TRUE) {
		$this->formTagRendergingStatus = $formTagRenderingStatus;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $jsSupportFilesRootDir
	 * @return string
	 */
	public static function SetJsSupportFilesRootDir ($jsSupportFilesRootDir) {
		if ($jsSupportFilesRootDir)
			static::$jsSupportFilesRootDir = $jsSupportFilesRootDir;
		return static::$jsSupportFilesRootDir;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $cssSupportFilesRootDir
	 * @return string
	 */
	public static function SetCssSupportFilesRootDir ($cssSupportFilesRootDir) {
		if ($cssSupportFilesRootDir)
			static::$cssSupportFilesRootDir = $cssSupportFilesRootDir;
		return static::$cssSupportFilesRootDir;
	}

	/**
	 * @inheritDocs
	 * @param  \string[] $validatorsNamespaces
	 * @return int New validators namespaces count.
	 */
	public static function SetValidatorsNamespaces (array $validatorsNamespaces = []) {
		static::$validatorsNamespaces = [];
		return static::AddValidatorsNamespaces($validatorsNamespaces);
	}

	/**
	 * @inheritDocs
	 * @param  string $formId
	 * @param  string $fieldName
	 * @param  int $duplicateBehaviour
	 * @throws \RuntimeException
	 * @return bool
	 */
	public static function SetAutoFocusedFormField ($formId = NULL, $fieldName = NULL, $duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION) {
		if (self::$autoFocusedFormField) {
			// if any global autofocus record is already defined
			if ($formId === NULL && $fieldName === NULL) {
				// unset old every time form id and field name is both `NULL`
				list ($oldFormId, $oldFieldName) = self::$autoFocusedFormField;
				self::GetById($oldFormId)->GetField($oldFieldName)->SetAutoFocus(FALSE);
				self::$autoFocusedFormField = [];
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION) {
				// thrown an runtime exception
				list ($currentFormId, $currentFieldName) = self::$autoFocusedFormField;
				throw new \RuntimeException(
					'Another form field has already defined `autofocus` attribute.'
					. ' Form id: `' . $currentFormId . '`, field name: `' . $currentFieldName . '`.'
				);
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW) {
				// quietly set (could be useful to render something in the background)
				self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
					TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
				);
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW) {
				// unset previous and set new
				list ($oldFormId, $oldFieldName) = self::$autoFocusedFormField;
				self::GetById($oldFormId)->GetField($oldFieldName)->SetAutoFocus(FALSE);
				self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
					TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
				);
			}
		} else {
			// if no global autofocus record is defined
			self::$autoFocusedFormField = [$formId, $fieldName];
			self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
				TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
			);
		}
		return TRUE;
	}
}
