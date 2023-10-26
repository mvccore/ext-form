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
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * @inheritDoc
	 * @requires
	 * @param  string $name 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetName ($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return int|NULL
	 */
	public function GetFieldOrder () {
		return $this->fieldOrder;
	}

	/**
	 * @inheritDoc
	 * @param  int $fieldOrder 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldOrder ($fieldOrder) {
		$this->fieldOrder = $fieldOrder;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetLegend () {
		return $this->legend;
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 * @return bool
	 */
	public function GetDisabled () {
		return $this->disabled;
	}

	/**
	 * @inheritDoc
	 * @param  bool $disabled 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetDisabled ($disabled) {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
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
	 * @inheritDoc
	 * @return string|NULL
	 */
	public function GetTitle () {
		return $this->title;
	}
	
	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 * @return array
	 */
	public function & GetControlAttrs () {
		return $this->controlAttrs;
	}

	/**
	 * @inheritDoc
	 * @param  string $name 
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * @inheritDoc
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetControlAttrs (array $attrs = []) {
		$this->controlAttrs = $attrs;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $name 
	 * @param  mixed  $value 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetControlAttr ($name, $value) {
		$this->controlAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  array $attrs 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddControlAttrs (array $attrs = []) {
		$this->controlAttrs = array_merge($this->controlAttrs, $attrs);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return int|NULL
	 */
	public function GetFormRenderMode () {
		if ($this->formRenderMode === NULL && $this->form !== NULL)
			return $this->form->GetFormRenderMode();
		return $this->formRenderMode;
	}
	
	/**
	 * @inheritDoc
	 * @param  int $formRenderMode
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFormRenderMode ($formRenderMode = \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE) {
		$this->formRenderMode = $formRenderMode;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  bool $sorted
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ($sorted = TRUE) {
		if ($sorted && !$this->sorting->sorted)
			$this->SortChildren();
		return $this->children;
	}
	
	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm () {
		return $this->form;
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function GetTemplate () {
		return $this->template;
	}

	/**
	 * @inheritDoc
	 * @param  string $template Template HTML code with prepared replacements.
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetTemplate ($template) {
		$this->template = $template;
		return $this;
	}
}
