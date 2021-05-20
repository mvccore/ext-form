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

namespace MvcCore\Ext\Forms\Fieldset;

/**
 * @mixin \MvcCore\Ext\Forms\Fieldset
 */
trait FieldMethods {
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFields ($fields) {
		$args = func_get_args();
		$fields = is_array($args[0]) && count($args) === 1
			? $args[0]
			: $args;
		if (count($this->fields) > 0) {
			$fieldNames = array_keys($this->fields);
			// unset all fields from not directly connected children fieldsets:
			$fieldsetsFields = array_diff_key($this->fields, $this->children);
			foreach ($fieldsetsFields as $fieldName => $fieldsetsField) {
				/** @var \MvcCore\Ext\Forms\Field $fieldsetsField */
				/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
				$fieldsetName = $fieldsetsField->GetFieldsetName();
				if (!isset($this->fieldsets[$fieldsetName])) continue;
				$fieldset = $this->fieldsets[$fieldsetName];
				if ($fieldset->RemoveField($fieldName));
			}
			$childrenFieldsNames = array_intersect($fieldNames, array_keys($this->children));
			// unset all naturally sorted fields:
			$newNaturallySortingNames = array_diff($this->sorting->naturally, $childrenFieldsNames);
			$numberedSortingNames = array_diff($childrenFieldsNames, $this->sorting->naturally);
			$this->sorting->naturally = $newNaturallySortingNames;
			// unset all numbered sorting fields:
			foreach ($numberedSortingNames as $numberedSortingName) {
				/** @var \MvcCore\Ext\Forms\Field $numberedSortingField */
				$numberedSortingField = $this->fields[$numberedSortingName];
				$fieldOrder = $numberedSortingField->GetFieldOrder();
				if ($fieldOrder !== NULL && isset($this->sorting->numbered[$fieldOrder])) {
					$numberedFieldNames = & $this->sorting->numbered[$fieldOrder];
					$index = array_search($numberedSortingName, $numberedFieldNames);
					if ($index !== FALSE) unset($numberedFieldNames[$index]);
				}
			}
			// unset all fields from children (keep fieldsets only):
			$this->children = array_diff_key($this->children, $this->fields);
			// clean fields:
			if ($this->form !== NULL) 
				foreach ($fieldNames as $fieldName)
					$this->form->RemoveField($fieldName);
			$this->fields = [];
		}
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @param  string|NULL              $fieldName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field, $fieldName = NULL) {
		if ($fieldName === NULL) {
			$fieldName = $field->GetName();
			if ($fieldName === NULL) throw new \InvalidArgumentException(
				"[".get_class($this)."] Field has not defined name."
			);
		} else {
			$field->SetName($fieldName);
		}
		if (isset($this->fields[$fieldName]) && $this->fields[$fieldName] !== $field)
			$this->RemoveField($field);
		$this->AddField($field);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFields ($fields) {
		$fields = func_get_args();
		if (count($fields) === 1 && is_array($fields[0])) $fields = $fields[0];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field $field 
	 * @throws \RuntimeException|\InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field) {
		if ($this->name === NULL)
			throw new \RuntimeException(
				"[".get_class($this)."] Fieldset has not configured name."
			);
		$fieldName = $field->GetName();
		if ($this->form !== NULL) {
			$field->SetFieldsetName($this->name);
			$this->form->AddField($field);
			$this->fields[$fieldName] = $field;
		} else {
			$alreadyRegistered = FALSE;
			if (isset($this->fields[$fieldName])) {
				$alreadyRegistered = $field === $this->fields[$fieldName];
				if (!$alreadyRegistered)
					throw new \InvalidArgumentException(
						"[".get_class($this)."] Fieldset `{$this->name}` already contains field with name: `{$fieldName}`."
					);
			}
			if (isset($this->fieldsets[$fieldName]))
				throw new \InvalidArgumentException(
					"[".get_class($this)."] Fieldset `{$this->name}` already contains fieldset with the same name as field: `{$fieldName}`."
				);
			if (!$alreadyRegistered) {
				$field->SetFieldsetName($this->name);
				$this->fields[$fieldName] = $field;
			}
		}
		$alreadyInChildren = isset($this->children[$fieldName]);
		if (!$alreadyInChildren)
			$this->children[$fieldName] = $field;
		$fieldOrder = $field->GetFieldOrder();
		if (is_numeric($fieldOrder)) {
			if (!isset($this->sorting->numbered[$fieldOrder]))
				$this->sorting->numbered[$fieldOrder] = [];
			$sortCollection = & $this->sorting->numbered[$fieldOrder];
		} else {
			$sortCollection = & $this->sorting->naturally;
		}
		if (
			!$alreadyInChildren || (
				$alreadyInChildren &&
				!in_array($fieldName, $sortCollection, TRUE)
			)
		) {
			$sortCollection[] = $fieldName;
			$this->sorting->sorted = FALSE;
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasField ($fieldOrFieldName) {
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
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function RemoveField ($fieldOrFieldName) {
		$fieldName = NULL;
		if ($fieldOrFieldName instanceof \MvcCore\Ext\Forms\IField) {
			$fieldName = $fieldOrFieldName->GetName();
		} else if (is_string($fieldOrFieldName)) {
			$fieldName = $fieldOrFieldName;
		}
		if (isset($this->fields[$fieldName]))
			unset($this->fields[$fieldName]);
		if ($this->form !== NULL)
			$this->form->RemoveField($fieldName);
		if (isset($this->children[$fieldName])) {
			$field = $this->children[$fieldName];
			unset($this->children[$fieldName]);
			$fieldOrder = $field->GetFieldOrder();
			$this->sorting->sorted = FALSE;
			if (is_numeric($fieldOrder)) {
				$orderCollection = & $this->sorting->numbered[$fieldOrder];
			} else {
				$orderCollection = & $this->sorting->naturally;
			}
			$fieldIndex = array_search($fieldName, $orderCollection, TRUE);
			array_splice($orderCollection, $fieldIndex, 1);
		}
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields () {
		return $this->fields;
	}
	
	/**
	 * @inheritDocs
	 * @param  string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetField ($fieldName) {
		$result = NULL;
		if (isset($this->fields[$fieldName]))
			$result = $this->fields[$fieldName];
		return $result;
	}
	
}