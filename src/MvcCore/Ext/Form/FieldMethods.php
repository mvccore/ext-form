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
 * @mixin \MvcCore\Ext\Form
 */
trait FieldMethods {

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields() {
		return $this->fields;
	}
	
	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Fields\ISubmit[]
	 */
	public function GetSubmitFields () {
		return $this->submitFields;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,... Array with `\MvcCore\Ext\Forms\IField` instances to set into form.
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFields ($fields) {
		$args = func_get_args();
		$fields = is_array($args[0]) && count($args) === 1
			? $args[0]
			: $args;
		if (count($this->fields) > 0) {
			$childrenFieldsNames = array_intersect(array_keys($this->fields), array_keys($this->children));
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
			$this->fields = [];
		}
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @param  string|NULL              $fieldName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Form
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
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field $fields,... Any `\MvcCore\Ext\Forms\IField` fully configured instance to add into form.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddFields ($fields) {
		$fields = func_get_args();
		if (count($fields) === 1 && is_array($fields[0])) $fields = $fields[0];
		foreach ($fields as $field)
			$this->AddField($field);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @throws \InvalidArgumentException Form already contains field with name `...`.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field, $autoInit = TRUE) {
		$this->DispatchStateCheck(static::DISPATCH_STATE_INITIALIZED, $this->submit);
		
		// throw an exception if field is already registered, if not, continue:
		$this->addFieldCheckRegistered($field);

		// register field:
		$fieldName = $field->GetName();
		$field->SetForm($this);
		$this->fields[$fieldName] = $field;
		// submits:
		if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) {
			/** @var \MvcCore\Ext\Forms\Fields\ISubmit $field */
			$this->submitFields[$fieldName] = $field;
			$fieldCustomResultState = $field->GetCustomResultState();
			if ($fieldCustomResultState !== NULL)
				$this->customResultStates[$fieldName] = $fieldCustomResultState;
		}

		// fieldset and sorting (root form level only):
		if ($field->GetFieldsetName() === NULL) {
			$alreadyInChildren = isset($this->children[$fieldName]);
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
		}
		return $this;
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @param  bool                            $autoInit
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveField ($fieldOrFieldName, $autoInit = TRUE) {
		$this->DispatchStateCheck(static::DISPATCH_STATE_INITIALIZED, $this->submit);
		$fieldName = NULL;
		if ($fieldOrFieldName instanceof \MvcCore\Ext\Forms\IField) {
			$fieldName = $fieldOrFieldName->GetName();
		} else if (is_string($fieldOrFieldName)) {
			$fieldName = $fieldOrFieldName;
		}
		if (isset($this->fields[$fieldName])) {
			$field = $this->fields[$fieldName];
			unset($this->fields[$fieldName]);
			if ($field instanceof \MvcCore\Ext\Forms\Fields\ISubmit) {
				/** @var \MvcCore\Ext\Forms\Fields\ISubmit $field */
				unset($this->submitFields[$fieldName]);
				$fieldCustomResultState = $field->GetCustomResultState();
				if ($fieldCustomResultState !== NULL)
					unset($this->customResultStates[$fieldName]);
			}
			$fieldsetName = $field->GetFieldsetName();
			if (isset($this->fieldsets[$fieldsetName])) {
				$fieldset = $this->fieldsets[$fieldsetName];
				if ($fieldset) $fieldset->RemoveField($fieldName);
			} else if ($fieldsetName === NULL) {
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
		}
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetField ($fieldName) {
		$result = NULL;
		if (isset($this->fields[$fieldName]))
			$result = $this->fields[$fieldName];
		return $result;
	}

	/**
	 * @inheritDoc
	 * @param  string $fieldType
	 * @return \MvcCore\Ext\Forms\Field[]|array
	 */
	public function GetFieldsByType ($fieldType) {
		$result = [];
		foreach ($this->fields as $field) {
			if ($field->GetType() === $fieldType)
				$result[$field->GetName()] = $field;
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @param  string $fieldType
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetFirstFieldByType ($fieldType) {
		$result = NULL;
		foreach ($this->fields as $field) {
			if ($field->GetType() === $fieldType) {
				$result = $field;
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @param  string $fieldClassName  Full php class name or full interface name.
	 * @param  bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field[]|array
	 */
	public function GetFieldsByPhpClass ($fieldClassName, $directTypesOnly = FALSE) {
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
	 * @inheritDoc
	 * @param  string $fieldClassName  Full php class name or full interface name.
	 * @param  bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetFirstFieldByPhpClass ($fieldClassName, $directTypesOnly = FALSE) {
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

	/**
	 * Check if the currently added field is already registered.
	 * @param  \MvcCore\Ext\Forms\IField $field 
	 * @throws \InvalidArgumentException 
	 * @return void
	 */
	protected function addFieldCheckRegistered (\MvcCore\Ext\Forms\IField $field) {
		$fieldName = $field->GetName();
		if (!isset($this->fields[$fieldName])) {
			if ($this->environment->IsDevelopment()) {
				$backtraceItemPrev = NULL;
				$backtraceItems = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
				for ($i = 1; $i < 5; $i++) {
					$backtraceItem = $backtraceItems[$i];
					$className = isset($backtraceItem['class']) ? $backtraceItem['class'] : NULL;
					if ($className === NULL || mb_strpos($className, 'MvcCore\\Ext\\Form') === FALSE) {
						$this->fieldsEntries[$fieldName] = [$backtraceItem, $backtraceItemPrev];
						break;
					}
					$backtraceItemPrev = $backtraceItem;
				}
			}
		} else {
			$alreadyRegistered = $field === $this->fields[$fieldName];
			if (!$alreadyRegistered) {
				$prevFieldType = get_class($this->fields[$fieldName]);
				$fieldEntry = '';
				if ($this->environment->IsDevelopment() && isset($this->fieldsEntries[$fieldName])) {
					list($backtraceItem, $backtraceItemPrev) = $this->fieldsEntries[$fieldName];
					if (isset($backtraceItem['class']) && isset($backtraceItem['function'])) {
						$className = $backtraceItem['class'];
						$funcName = $backtraceItem['function'];
						$fieldEntry = "{$className}::{$funcName}";
					}
					if (isset($backtraceItemPrev['file']) && isset($backtraceItemPrev['line'])) {
						$file = str_replace('\\', '/', $backtraceItemPrev['file']);
						$line = $backtraceItemPrev['line'];
						$fieldEntry = strlen($fieldEntry) > 0
							? "`{$fieldEntry}` (`{$file}:{$line}`)"
							: "`{$file}:{$line}`";
					}
					$fieldEntry = "entry: {$fieldEntry}";
				}
				throw new \InvalidArgumentException(
					"[".get_class($this)."] Form already contains field with name: `{$fieldName}`, type: `{$prevFieldType}`{$fieldEntry}."
				);
			}
		}
		if (isset($this->fieldsets[$fieldName]))
			throw new \InvalidArgumentException(
				"[".get_class($this)."] Form already contains fieldset with the same name as field: `{$fieldName}`."
			);
	}

}
