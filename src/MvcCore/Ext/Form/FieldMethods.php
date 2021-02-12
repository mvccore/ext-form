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
 * Trait for class `MvcCore\Ext\Form` containing getters and setters methods for 
 * form field instances and methods to add, search or remove field instance from 
 * form.
 */
trait FieldMethods {

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields() {
		return $this->fields;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field[] $fields Array with `\MvcCore\Ext\Forms\IField` instances to set into form.
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFields ($fields = []) {
		/** @var $this \MvcCore\Ext\Form */
		$this->fields = [];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,... Any `\MvcCore\Ext\Forms\IField` fully configured instance to add into form.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddFields ($fields) {
		/** @var $this \MvcCore\Ext\Form */
		$fields = func_get_args();
		if (count($fields) === 1 && is_array($fields[0])) $fields = $fields[0];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Form
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field) {
		/** @var $this \MvcCore\Ext\Form */
		/** @var $field \MvcCore\Ext\Forms\Field */
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init();
		$fieldName = $field->GetName();
		$field->SetForm($this);
		$this->fields[$fieldName] = $field;
		if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) {
			/** @var $field \MvcCore\Ext\Forms\Fields\ISubmit */
			$this->submitFields[$fieldName] = $field;
			$fieldCustomResultState = $field->GetCustomResultState();
			if ($fieldCustomResultState !== NULL)
				$this->customResultStates[$fieldName] = $fieldCustomResultState;
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasField ($fieldOrFieldName = NULL) {
		$fieldName = NULL;
		if ($fieldOrFieldName instanceof \MvcCore\Ext\Forms\IField) {
			$fieldName = $fieldOrFieldName->GetName();
		} else if (is_string($fieldOrFieldName)) {
			$fieldName = $fieldOrFieldName;
		}
		return isset($this->fields[$fieldName]);
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveField ($fieldOrFieldName = NULL) {
		/** @var $this \MvcCore\Ext\Form */
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init();
		$fieldName = NULL;
		if ($fieldOrFieldName instanceof \MvcCore\Ext\Forms\IField) {
			$fieldName = $fieldOrFieldName->GetName();
		} else if (is_string($fieldOrFieldName)) {
			$fieldName = $fieldOrFieldName;
		}
		if (isset($this->fields[$fieldName]))
			unset($this->fields[$fieldName]);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetField ($fieldName = '') {
		$result = NULL;
		if (isset($this->fields[$fieldName]))
			$result = $this->fields[$fieldName];
		return $result;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldType
	 * @return \MvcCore\Ext\Forms\Field[]|array
	 */
	public function GetFieldsByType ($fieldType = '') {
		$result = [];
		foreach ($this->fields as $field) {
			if ($field->GetType() == $fieldType)
				$result[$field->GetName()] = $field;
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldType
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetFirstFieldByType ($fieldType = '') {
		$result = NULL;
		foreach ($this->fields as $field) {
			if ($field->GetType() == $fieldType) {
				$result = $field;
			}
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldClassName  Full php class name or full interface name.
	 * @param  bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field[]|array
	 */
	public function GetFieldsByPhpClass ($fieldClassName = '', $directTypesOnly = FALSE) {
		$result = [];
		foreach ($this->fields as $field) {
			if (is_a($field, $fieldClassName)) {
				if ($directTypesOnly)
					if (is_subclass_of($field, $fieldClassName))
						continue;
				$result[$field->GetName()] = $field;
			}
		}
		return $result;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldClassName  Full php class name or full interface name.
	 * @param  bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetFirstFieldByPhpClass ($fieldClassName = '', $directTypesOnly = FALSE) {
		$result = NULL;
		foreach ($this->fields as $field) {
			if (is_a($field, $fieldClassName)) {
				if ($directTypesOnly)
					if (is_subclass_of($field, $fieldClassName))
						continue;
				$result = $field;
				break;
			}
		}
		return $result;
	}
}
