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
 * Trait for class `MvcCore\Ext\Form` containing getters and setters methods for 
 * form field instances and methods to add, search or remove field instance from 
 * form.
 */
trait FieldMethods
{
	/**
	 * Get all form field controls.
	 * After adding any field into form instance by `$form->AddField()` method
	 * field is added under it's name into this array with all another form fields 
	 * except CSRF `input:hidden`s. Fields are rendered by order in this array.
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[]
	 */
	public function & GetFields() {
		return $this->fields;
	}

	/**
	 * Replace all previously configured fields with given fully configured fields array.
	 * This method is dangerous - it will remove all previously added form fields
	 * and add given fields. If you want only to add another field(s) into form,
	 * use functions:
	 * - `$form->AddField($field);`
	 * - `$form->AddFields($field1, $field2, $field3...);`
	 * @param \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[] $fields Array with `\MvcCore\Ext\Forms\IField` instances to set into form.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SetFields ($fields = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->fields = [];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}

	/**
	 * Add multiple fully configured form field instances,
	 * function have infinite params with new field instances.
	 * @param \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[] $fields,... Any `\MvcCore\Ext\Forms\IField` fully configured instance to add into form.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function AddFields ($fields) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$fields = func_get_args();
		if (count($fields) === 1 && is_array($fields[0])) $fields = $fields[0];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}

	/**
	 * Add fully configured form field instance.
	 * @param \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField $field
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		/** @var $field \MvcCore\Ext\Forms\Field */
		if ($this->dispatchState < 1) $this->Init();
		$fieldName = $field->GetName();
		$field->SetForm($this);
		$this->fields[$fieldName] = $field;
		if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) {
			$this->submitFields[$fieldName] = $field;
			$fieldCustomResultState = $field->GetCustomResultState();
			if ($fieldCustomResultState !== NULL)
				$this->customResultStates[$fieldName] = $fieldCustomResultState;
		}
		return $this;
	}

	/**
	 * If `TRUE` if given field instance or given
	 * field name exists in form, `FALSE` otherwise.
	 * @param \MvcCore\Ext\Forms\IField|string $fieldOrFieldName
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
	 * Remove configured form field instance by given instance or given field name.
	 * If field is not found by it's name, no error happened.
	 * @param \MvcCore\Ext\Forms\IField|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function RemoveField ($fieldOrFieldName = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($this->dispatchState < 1) $this->Init();
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
	 * Return form field instance by form field name if it exists, else return null;
	 * @param string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField|NULL
	 */
	public function GetField ($fieldName = '') {
		$result = NULL;
		if (isset($this->fields[$fieldName]))
			$result = $this->fields[$fieldName];
		return $result;
	}

	/**
	 * Return form field instances by given field type string.
	 * If no field(s) found, it's returned empty array.
	 * Result array is keyed by field names.
	 * @param string $fieldType
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[]|array
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
	 * Return first caught form field instance by given field type string.
	 * If no field found, `NULL` is returned.
	 * @param string $fieldType
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField|NULL
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
	 * Return form field instances by field class name
	 * compared by `is_a($field, $fieldClassName)` check.
	 * If no field(s) found, it's returned empty array.
	 * Result array is keyed by field names.
	 * @param string $fieldClassName Full php class name or full interface name.
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[]|array
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
	 * Return first caught form field instance by field class name
	 * compared by `is_a($field, $fieldClassName)` check.
	 * If no field found, it's returned `NULL`.
	 * @param string $fieldClassName Full php class name or full interface name.
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField|NULL
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
