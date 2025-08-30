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
trait FieldsetMethods {

	
	/**
	 * @inheritDoc
	 * @return ?\MvcCore\Ext\Forms\Fieldset
	 */
	public function GetParentFieldset () {
		return $this->parentFieldset;
	}
	
	/**
	 * @inheritDoc
	 * @internal
	 * @param  ?\MvcCore\Ext\Forms\Fieldset $fieldset
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetParentFieldset (/*\MvcCore\Ext\Forms\IFieldset*/ $fieldset = NULL) {
		if (
			$fieldset !== NULL && 
			!($fieldset instanceof \MvcCore\Ext\Forms\IFieldset) // @phpstan-ignore-line
		) throw new \RuntimeException(
			"[".get_class($this)."] Parent fieldset doesn't implement ".
			"interface `\MvcCore\Ext\Forms\IFieldset`."
		);
		if ($this->parentFieldset !== NULL) throw new \InvalidArgumentException(
			"[".get_class($this)."] Can NOT override parent fieldset. ".
			"Remove this fieldset from parent fieldset first by ".
			"`\$parentFieldset->RemoveFieldset(\$thisFieldset);`."
		);
		$this->parentFieldset = $fieldset;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldsets ($fieldsets) {
		$args = func_get_args();
		$fieldsets = is_array($args[0]) && count($args) === 1
			? $args[0]
			: $args;
		if (count($this->fieldsets) > 0) {
			$fieldsetNames = array_keys($this->fieldsets);
			// unset all fieldsets from not directly connected children fieldsets:
			$childrenFieldsetNames = array_intersect($fieldsetNames, array_keys($this->children));
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
			if ($this->form !== NULL) 
				foreach ($fieldsetNames as $fieldsetName)
					$this->form->RemoveFieldset($fieldsetName);
			$this->fieldsets = [];
		}
		foreach ($fieldsets as $fieldset)
			$this->AddFieldset($fieldset);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @param  ?string                 $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset
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
			$this->RemoveFieldset($fieldsetName);
		$this->AddFieldset($fieldset);
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
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
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets () {
		return $this->fieldsets;
	}
	
	/**
	 * @inheritDoc
	 * @param  string $fieldsetName
	 * @return ?\MvcCore\Ext\Forms\Fieldset
	 */
	public function GetFieldset ($fieldsetName) {
		$result = NULL;
		if (isset($this->fieldsets[$fieldsetName]))
			$result = $this->fieldsets[$fieldsetName];
		return $result;
	}
	
	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset) {
		/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
		if ($this->name === NULL || $fieldset->name === NULL)
			throw new \RuntimeException(
				"[".get_class($this)."] Fieldset has not configured name."
			);
		$fieldset->SetParentFieldset($this);
		$fieldsetName = $fieldset->GetName();
		if ($this->form !== NULL) {
			$this->form->AddFieldset($fieldset);
			$this->fieldsets[$fieldsetName] = $fieldset;
		} else {
			$alreadyRegistered = FALSE;
			if (isset($this->fieldsets[$fieldsetName])) {
				$alreadyRegistered = $fieldset === $this->fieldsets[$fieldsetName];
				if (!$alreadyRegistered)
					throw new \InvalidArgumentException(
						"[".get_class($this)."] Fieldset `{$this->name}` already contains fieldset with name: `{$fieldsetName}`."
					);
			}
			if (isset($this->fields[$fieldsetName]))
				throw new \InvalidArgumentException(
					"[".get_class($this)."] Fieldset `{$this->name}` already contains field with the same name as fieldset: `{$fieldsetName}`."
				);
			if (!$alreadyRegistered) {
				$this->fieldsets[$fieldsetName] = $fieldset;
			}
		}
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
		return $this;
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
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName) {
		$fieldsetName = NULL;
		if ($fieldsetOrFieldsetName instanceof \MvcCore\Ext\Forms\IFieldset) {
			$fieldsetName = $fieldsetOrFieldsetName->GetName();
		} else if (is_string($fieldsetOrFieldsetName)) {
			$fieldsetName = $fieldsetOrFieldsetName;
		}
		if (isset($this->fieldsets[$fieldsetName])) 
			unset($this->fieldsets[$fieldsetName]);
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
			$fieldsetIndex = array_search($fieldsetName, $orderCollection, TRUE);
			array_splice($orderCollection, $fieldsetIndex, 1);
		}
		return $this;
	}

}