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
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields () {
		return $this->fields;
	}
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field[] $fields
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFields ($fields) {
		$this->fields = [];
		$this->sorting = (object) [
			'sorted'	=> FALSE,
			'numbered'	=> [],
			'naturally'	=> [],
		];
		foreach ($fields as $field)
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
		if ($this->form !== NULL) {
			$field->SetFieldsetName($this->name);
			$this->form->AddField($field);
		} else {
			$fieldName = $field->GetName();
			$alreadyRegistered = FALSE;
			if (isset($this->fields[$fieldName])) {
				$alreadyRegistered = $field === $this->fields[$fieldName];
				if (!$alreadyRegistered)
					throw new \InvalidArgumentException(
						"[".get_class($this)."] Fieldset already contains field with name: `{$fieldName}`."
					);
			}
			if (isset($this->fieldsets[$fieldName]))
				throw new \InvalidArgumentException(
					"[".get_class($this)."] Fieldset already contains fieldset with the same name as field: `{$fieldName}`."
				);
			if (!$alreadyRegistered) {
				$field->SetFieldsetName($this->name);
				$this->fields[$fieldName] = $field;
			}
		}
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
		if (isset($this->fields[$fieldName])) {
			$field = $this->fields[$fieldName];
			unset($this->fields[$fieldName]);
		}
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

}