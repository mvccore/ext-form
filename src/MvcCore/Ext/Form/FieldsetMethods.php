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
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets () {
		return $this->fieldsets;
	}
	
	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets 
	 * @return \MvcCore\Ext\Form
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
	 * @inheritDocs
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetFieldset ($fieldsetName) {
		$result = NULL;
		if (isset($this->fieldsets[$fieldsetName]))
			$result = $this->fieldsets[$fieldsetName];
		return $result;
	}
	
	/**
	 * @inheritDocs
	 * @param  string                      $fieldsetName
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFieldset ($fieldsetName, \MvcCore\Ext\Forms\IFieldset $fieldset) {
		$this->fieldsets[$fieldsetName] = $fieldset;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset 
	 * @param  bool                        $autoInit
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Form
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset, $autoInit = TRUE) {
		/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
		if ($autoInit && $this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) 
			$this->Init();
		$fieldsetName = $fieldset->GetName();
		if (isset($this->fields[$fieldsetName]))
			throw new \InvalidArgumentException(
				"[".get_class($this)."] Form already contains field with the same name as fieldset: `{$fieldsetName}`."
			);
		if (isset($this->fieldsets[$fieldsetName]))
			throw new \InvalidArgumentException(
				"[".get_class($this)."] Form already contains fieldset with name: `{$fieldsetName}`."
			);
		$fieldset->SetForm($this);
		$this->fieldsets[$fieldsetName] = $fieldset;
		// fields:

		// ordering:

		xxx($this);
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
	 * @param  bool                               $autoInit
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName, $autoInit = TRUE) {
		// TODO
		return $this;
	}

}
