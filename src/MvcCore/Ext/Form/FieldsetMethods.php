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
 * form fieldset instances and methods to add, update or remove fieldset instance 
 * from form.
 * @mixin \MvcCore\Ext\Form
 */
trait FieldsetMethods {

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets ,...
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFieldsets ($fieldsets) {
		$args = func_get_args();
		$fieldsets = is_array($args[0]) && count($args) === 1
			? $args[0]
			: $args;
		if (count($this->fieldsets) > 0) {
			// unset all fieldsets from not directly connected children fieldsets:
			$fieldsetsFieldsets = array_diff_key($this->fieldsets, $this->children);
			foreach ($fieldsetsFieldsets as $fieldsetName => $fieldsetsFieldset) {
				/** @var \MvcCore\Ext\Forms\Fieldset $fieldsetsFieldset */
				/** @var \MvcCore\Ext\Forms\Fieldset $parentFieldset */
				$parentFieldset = $fieldsetsFieldset->GetParentFieldset();
				$fieldsetsFieldset->SetParentFieldset(NULL);
				$parentFieldset->RemoveFieldset($fieldsetName);
			}
			$childrenFieldsetNames = array_intersect(array_keys($this->fieldsets), array_keys($this->children));
			// unset all naturally sorted fieldsets:
			$newNaturallySortingNames = array_diff($this->sorting->naturally, $childrenFieldsetNames);
			$numberedSortingNames = array_diff($childrenFieldsetNames, $this->sorting->naturally);
			$this->sorting->naturally = $newNaturallySortingNames;
			// unset all numbered sorting fieldsets:
			foreach ($numberedSortingNames as $numberedSortingName) {
				/** @var \MvcCore\Ext\Forms\Fieldset $numberedSortingFieldset */
				$numberedSortingFieldset = $this->fieldsets[$numberedSortingName];
				$fieldOrder = $numberedSortingFieldset->GetFieldOrder();
				if ($fieldOrder !== NULL && isset($this->sorting->numbered[$fieldOrder])) {
					$numberedFieldNames = & $this->sorting->numbered[$fieldOrder];
					$index = array_search($numberedSortingName, $numberedFieldNames);
					if ($index !== FALSE) unset($numberedFieldNames[$index]);
				}
			}
			// unset all fieldsets from children (keep fields only):
			$this->children = array_diff_key($this->children, $this->fieldsets);
			// clean fieldsets:
			$this->fieldsets = [];
		}
		foreach ($fieldsets as $fieldset)
			$this->AddFieldset($fieldset);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @param  string|NULL                 $fieldsetName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset, $fieldsetName = NULL) {
		if ($fieldsetName === NULL) {
			$fieldsetName = $fieldset->GetName();
			if ($fieldsetName === NULL) throw new \InvalidArgumentException(
				"[".get_class($this)."] Fieldset has not defined name."
			);
		} else {
			$fieldset->SetName($fieldsetName);
		}
		if (isset($this->fieldsets[$fieldsetName]) && $this->fieldsets[$fieldsetName] !== $fieldset)
			$this->RemoveFieldset($fieldset);
		$this->AddFieldset($fieldset);
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Form
	 */
	public function AddFieldsets ($fieldsets) {
		$fieldsets = func_get_args();
		if (count($fieldsets) === 1 && is_array($fieldsets[0])) $fieldsets = $fieldsets[0];
		foreach ($fieldsets as $fieldset)
			$this->AddFieldset($fieldset);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset 
	 * @param  bool                        $autoInit
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Form
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset, $autoInit = TRUE) {
		/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
		$this->DispatchStateCheck(static::DISPATCH_STATE_INITIALIZED, $this->submit);
		$fieldsetName = $fieldset->GetName();
		$alreadyRegistered = FALSE;
		if (isset($this->fields[$fieldsetName]))
			throw new \InvalidArgumentException(
				"[".get_class($this)."] Form already contains field with the same name as fieldset: `{$fieldsetName}`."
			);
		if (isset($this->fieldsets[$fieldsetName])) {
			$alreadyRegistered = $fieldset === $this->fieldsets[$fieldsetName];
			if (!$alreadyRegistered)
				throw new \InvalidArgumentException(
					"[".get_class($this)."] Form already contains fieldset with name: `{$fieldsetName}`."
				);
		}
		if (!$alreadyRegistered) {
			$fieldset->SetForm($this);
			$this->fieldsets[$fieldsetName] = $fieldset;
		}
		// add nested fieldsets and fields in nested fieldsets:
		$this->addFieldsetFieldsRecursive($fieldset);
		// fieldset and sorting (root form level only):
		if ($fieldset->GetParentFieldset() === NULL) {
			$alreadyInChildren = isset($this->children[$fieldsetName]);
			$this->children[$fieldsetName] = $fieldset;
			$fieldOrder = $fieldset->GetFieldOrder();
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
					!in_array($fieldsetName, $sortCollection, TRUE)
				)
			) {
				$sortCollection[] = $fieldsetName;
				$this->sorting->sorted = FALSE;
			}
		}
		return $this;
	}

	/**
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset 
	 * @return void
	 */
	protected function addFieldsetFieldsRecursive (\MvcCore\Ext\Forms\IFieldset $fieldset) {
		$fieldsetFields = $fieldset->GetFields();
		foreach ($fieldsetFields as $fieldsetFieldName => $fieldsetField) 
			if (!isset($this->fields[$fieldsetFieldName]))
				$this->AddField($fieldsetField);
		foreach ($fieldset->GetFieldsets() as $fieldsetInFieldset) {
			$this->addFieldset($fieldsetInFieldset);
		}
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasFieldset ($fieldsetOrFieldsetName) {
		$fieldsetName = NULL;
		if ($fieldsetOrFieldsetName instanceof \MvcCore\Ext\Forms\IField) {
			$fieldsetName = $fieldsetOrFieldsetName->GetName();
		} else if (is_string($fieldsetOrFieldsetName)) {
			$fieldsetName = $fieldsetOrFieldsetName;
		}
		return isset($this->fieldsets[$fieldsetName]);
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldsetOrFieldsetName
	 * @param  bool                               $autoInit
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName, $autoInit = TRUE) {
		$fieldsetName = NULL;
		if ($fieldsetOrFieldsetName instanceof \MvcCore\Ext\Forms\IFieldset) {
			$fieldsetName = $fieldsetOrFieldsetName->GetName();
		} else if (is_string($fieldsetOrFieldsetName)) {
			$fieldsetName = $fieldsetOrFieldsetName;
		}
		if (isset($this->fieldsets[$fieldsetName])) {
			/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
			$fieldset = $this->fieldsets[$fieldsetName];
			unset($this->fieldsets[$fieldsetName]);
			// fields:
			$fieldsetFields = $fieldset->GetFields();
			foreach ($fieldsetFields as $fieldsetFieldName => $fieldsetField) 
				if (!isset($this->fields[$fieldsetFieldName]))
					$this->RemoveField($fieldsetField);
		}
		if (isset($this->children[$fieldsetName])) {
			$fieldset = $this->children[$fieldsetName];
			unset($this->children[$fieldsetName]);
			$fieldOrder = $fieldset->GetFieldOrder();
			$this->sorting->sorted = FALSE;
			if (is_numeric($fieldOrder)) {
				$orderCollection = & $this->sorting->numbered[$fieldOrder];
			} else {
				$orderCollection = & $this->sorting->naturally;
			}
			$fieldIndex = array_search($fieldsetName, $orderCollection, TRUE);
			array_splice($orderCollection, $fieldIndex, 1);
		}
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets () {
		return $this->fieldsets;
	}
	
	/**
	 * @inheritDoc
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetFieldset ($fieldsetName) {
		$result = NULL;
		if (isset($this->fieldsets[$fieldsetName]))
			$result = $this->fieldsets[$fieldsetName];
		return $result;
	}
	
}
