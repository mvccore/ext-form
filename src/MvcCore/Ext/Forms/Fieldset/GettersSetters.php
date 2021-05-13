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
trait GettersSetters {

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * @inheritDocs
	 * @param  string $name 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetName ($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @return int|NULL
	 */
	public function GetFieldOrder () {
		return $this->fieldOrder;
	}

	/**
	 * @inheritDocs
	 * @param  int $fieldOrder 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldOrder ($fieldOrder) {
		$this->fieldOrder = $fieldOrder;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetLegend () {
		return $this->legend;
	}

	/**
	 * @inheritDocs
	 * @param  string|NULL $legend 
	 * @param  bool|NULL   $translateLegend
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetLegend ($legend, $translateLegend = NULL) {
		$this->legend = $legend;
		if ($translateLegend !== NULL)
			$this->translateLegend = $translateLegend;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return bool
	 */
	public function GetDisabled () {
		return $this->disabled;
	}

	/**
	 * @inheritDocs
	 * @param  bool $disabled 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetDisabled ($disabled) {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

	/**
	 * @inheritDocs
	 * @param  string|\string[] $cssClasses 
	 * @return \MvcCore\Ext\Forms\Fieldset
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
	 * @param  string|\string[] $cssClasses 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddCssClasses ($cssClasses) {
		$cssClassesArr = is_array($cssClasses)
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetTitle () {
		return $this->title;
	}
	
	/**
	 * @inheritDocs
	 * @param  string|NULL $title
	 * @param  bool|NULL   $translateTitle
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetTitle ($title, $translateTitle = NULL) {
		$this->title = $title;
		if ($translateTitle !== NULL)
			$this->translateTitle = $translateTitle;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetControlAttrs () {
		return $this->controlAttrs;
	}

	/**
	 * @inheritDocs
	 * @param  string $name 
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * @inheritDocs
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetControlAttrs (array $attrs = []) {
		$this->controlAttrs = $attrs;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $name 
	 * @param  mixed  $value 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetControlAttr ($name, $value) {
		$this->controlAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $attrs 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddControlAttrs (array $attrs = []) {
		$this->controlAttrs = array_merge($this->controlAttrs, $attrs);
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

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetParentFieldset () {
		return $this->parentFieldset;
	}
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetParentFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset) {
		// TODO: podobně jako u fieldu SetFieldset
		return $this;
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
	
	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets () {
		return $this->fieldsets;
	}
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldsets ($fieldsets) {
		$this->fieldsets = [];
		foreach ($fieldsets as $fieldset)
			$this->AddFieldset($fieldset);
		return $this;
	}
	
	/**
	 * @inheritDocs
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
	 * @inheritDocs
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetFieldset ($fieldsetName) {
		$result = NULL;
		if (isset($this->fields[$fieldsetName]))
			$result = $this->fields[$fieldsetName];
		return $result;
	}
	
	/**
	 * @inheritDocs
	 * @param  string                      $fieldsetName
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldset ($fieldsetName, \MvcCore\Ext\Forms\IFieldset $fieldset) {
		$this->fields[$fieldsetName] = $fieldset;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset) {
		// TODO
		return $this;
	}
	
	/**
	 * @inheritDocs
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
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName) {
		// TODO
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  bool $sorted
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ($sorted = TRUE) {
		if ($sorted && !$this->sorting->sorted)
			$this->SortChildren();
		return $this->children;
	}
	
	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm () {
		return $this->form;
	}
}
