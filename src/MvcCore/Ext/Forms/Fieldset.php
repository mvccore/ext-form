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
	 * Create new form fieldset instance.
	 * 
	 * @param  array     $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * @param  string    $name 
	 * Form fieldset specific name, used to identify 
	 * fieldset between each other and between fields.
	 * This value is required and it's used mostly internally.
	 * @param  int       $fieldOrder 
	 * Fixed fieldset order number, `NULL` by default.
	 * @param  string    $legend 
	 * Form fieldset `<legend>` tag content, it could 
	 * contains HTML code, default `NULL`.
	 * Allowed HTML tags are container in constant:
	 * `\MvcCore\Ext\Forms\IFielset::ALLOWED_LEGEND_ELEMENTS`.
	 * @param  bool      $translateLegend 
	 * Boolean to translate legend text, `TRUE` by default.
	 * @param  bool      $disabled 
	 * Form fieldset `disabled` boolean attribute, 
	 * default `FALSE`. Browsers render all fields 
	 * disabled in `<fieldset>` with disabled attribute.
	 * @param  \string[] $cssClasses 
	 * Form fieldset HTML element css classes strings. Default 
	 * value is an empty array to not render HTML `class` attribute.
	 * @param  string    $title 
	 * Fieldset title, global HTML attribute, optional.
	 * @param  bool      $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array     $controlAttrs 
	 * Collection with fieldset HTML element additional attributes 
	 * by array keys/values. Do not use system attributes as: 
	 * `name`, `disabled`, `class` or `title` ... Those attributes 
	 * has it's own configurable properties by setter methods or 
	 * by constructor config array. Default value is an empty array 
	 * to not  render any additional attributes.
	 * @return void
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
	protected function consolidateCfg (array & $cfg, array $args, $argsCnt) {
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
		if ($this->form !== NULL) return $this;
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
		if ($form->GetTranslate()) {
			if ($this->translateLegend && $this->legend !== NULL)
				$this->legend = $form->Translate($this->legend);
			if ($this->translateTitle && $this->title !== NULL)
				$this->title = $form->Translate($this->title);
		}
	}
}