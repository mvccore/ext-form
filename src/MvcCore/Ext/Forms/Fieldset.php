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

namespace MvcCore\Ext\Forms;

class Fieldset implements \MvcCore\Ext\Forms\IFieldset {

	use \MvcCore\Ext\Forms\Fieldset\Props,
		\MvcCore\Ext\Forms\Fieldset\GettersSetters,
		\MvcCore\Ext\Forms\Fieldset\FieldMethods,
		\MvcCore\Ext\Forms\Fieldset\FieldsetMethods,
		\MvcCore\Ext\Forms\Fieldset\Rendering;

	/**
	 * 
	 * @param array     $cfg 
	 * @param string    $name 
	 * @param int       $fieldOrder 
	 * @param string    $legend 
	 * @param bool      $translateLegend 
	 * @param bool      $disabled 
	 * @param \string[] $cssClasses 
	 * @param string    $title 
	 * @param bool      $translateTitle 
	 * @param array     $controlAttrs 
	 */
	public function __construct (
		array $cfg = [],
		$name = NULL,
		$fieldOrder = NULL,
		$legend = NULL,
		$translateLegend = TRUE,
		$disabled = FALSE,
		array $cssClasses = [],
		$title = NULL,
		$translateTitle = TRUE,
		array $controlAttrs = []
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		foreach ($cfg as $propertyName => $propertyValue) {
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				$this->throwNewInvalidArgumentException(
					'Property `'.$propertyName.'` is not possible '
					.'to configure by constructor `$cfg` param.'
				);
			} else {
				$this->{$propertyName} = $propertyValue;
			}
		}
		$this->sorting = (object) $this->sorting;
	}
	
	/**
	 * Consolidate all named constructor params (except first 
	 * agument `$cfg` array) into first agument `$cfg` array.
	 * @param  array $cfg 
	 * @param  array $args 
	 * @param  int   $argsCnt 
	 * @return void
	 */
	protected function consolidateCfg (array & $cfg, array $args, $argsCnt): void {
		if ($argsCnt < 2) return;
		/** @var \ReflectionParameter[] $params */
		$params = (new \ReflectionClass($this))->getConstructor()->getParameters();
		array_shift($params); // remove first `$cfg` param
		array_shift($args);   // remove first `$cfg` param
		/** @var \ReflectionParameter $param */
		foreach ($params as $index => $param) {
			if (
				!isset($args[$index]) ||
				$args[$index] === $param->getDefaultValue()
			) continue;
			$cfg[$param->name] = $args[$index];
		}
	}

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SortChildren () {
		if ($this->sorting->sorted)
			return $this;
		if (count($this->sorting->numbered) > 0) {
			$naturallySortedNames = & $this->sorting->naturally;
			ksort($this->sorting->numbered);
			foreach ($this->sorting->numbered as $fieldOrderNumber => $numberSortedNames) 
				array_splice($naturallySortedNames, $fieldOrderNumber, 0, $numberSortedNames);
			$this->sorting->numbered = [];
			$fields = [];
			$fieldsets = [];
			$children = [];
			foreach ($naturallySortedNames as $childName) {
				if (isset($this->fields[$childName])) {
					/** @var \MvcCore\Ext\Forms\Field $field */
					$field = $this->fields[$childName];
					$fields[$childName] = $field;
					$children[$childName] = $field;
				} else if (isset($this->fieldsets[$childName])) {
					/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
					$fieldset = $this->fieldsets[$childName];
					$fieldsets[$childName] = $fieldset;
					$children[$childName] = $fieldset;
				}
			}
			$this->fields = $fields;
			$this->fieldsets = $fieldsets;
			$this->children = $children;
		}
		$this->sorting->sorted = TRUE;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\IForm $form 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		if (!$this->name) $this->throwNewInvalidArgumentException(
			'No `name` property defined.'
		);
		/** @var \MvcCore\Ext\Form $form */
		$this->form = $form;
		$formTranslate = $form->GetTranslate();
		if ($this->translateLegend === NULL)
			$this->translateLegend = $formTranslate;
		if ($this->translateTitle === NULL)
			$this->translateTitle = $formTranslate;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @template
	 * @return void
	 */
	public function PreDispatch () {
		$form = $this->form;
		if ($this->translate) {
			if ($this->translateLegend && $this->legend !== NULL)
				$this->legend = $form->Translate($this->legend);
			if ($this->translateTitle && $this->title !== NULL)
				$this->title = $form->Translate($this->title);
		}
	}
}