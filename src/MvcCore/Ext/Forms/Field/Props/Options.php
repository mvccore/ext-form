<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\DataList`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait Options
{
	/**
	 * Form group control options to render
	 * more sub-control attributes for specified
	 * submitted values (array keys).
	 * This property configuration is required.
	 * 
	 * Example:
	 * ```
	 * // To configure for example radio buttons named: `gender` for `Female` and `Male`:
	 * //   <label for="gender-f">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" />
	 * //   <label for="gender-m">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" />
	 * // use this configuration:
	 * $field->name = 'gender';
	 * $field->options = array(
	 *	 'f' => 'Female',
	 *	 'm' => 'Male',
	 * );
	 *
	 * // Or you can use more advanced configuration with css class names 
	 * // and html element attributes, let's consider html code like this:
	 * //   <label for="gender-f" class="female">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 * //   <label for="gender-m" class="male">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 * // For that use this configuration:
	 * $field->name = 'gender';
	 * $field->options = array(
	 *	 'f' => array(
	 *		 'text'  => 'Female',	// text key will be also automatically translated
	 *		 'class' => 'female',
	 *		 'attrs' => array('data-any' => 'something-for-females'),
	 *	 ),
	 *	 'm' => array(
	 *		 'text'  => 'Male', // text key will be also automatically translated
	 *		 'class' => 'male',
	 *		 'attrs' => array('data-any' => 'something-for-males'),
	 *	 ),
	 * ));
	 * ```
	 * @requires
	 * @var array
	 */
	protected $options = [];

	/**
	 * Temp flatten key/value array to cache flatten options for submit checking.
	 * @var array|NULL
	 */
	protected $flattenOptions = NULL;

	/**
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @var bool
	 */
	protected $translateOptions = TRUE;

	/**
	 * Set form control or group control options to render
	 * more values for more specified submitted keys.
	 * 
	 * Example:
	 * ```
	 * // To configure for example radio buttons named: `gender` for `Female` and `Male`:
	 * //   <label for="gender-f">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" />
	 * //   <label for="gender-m">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" />
	 * // use this configuration:
	 * $field->SetName('gender')->SetOptions(array(
	 *	 // field values will be automatically translated, 
	 *	 // if form has configured translator `callable`
	 *	 'f' => 'Female',
	 *	 'm' => 'Male',
	 * ));
	 * 
	 * // Or you can use more advanced configuration with css class names 
	 * // and html element attributes, let's consider html code like this:
	 * //   <label for="gender-f" class="female">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 * //   <label for="gender-m" class="male">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 * // For that use this configuration:
	 * $field->SetName('gender')->SetOptions(array(
	 *	 'f' => array(
	 *		 'text'  => 'Female',	// text key will be also automatically translated
	 *		 'class' => 'female',
	 *		 'attrs' => array('data-any' => 'something-for-females'),
	 *	 ),
	 *	 'm' => array(
	 *		 'text'  => 'Male', // text key will be also automatically translated
	 *		 'class' => 'male',
	 *		 'attrs' => array('data-any' => 'something-for-males'),
	 *	 ),
	 * ));
	 * ```
	 * @param array $options
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetOptions (array $options = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->options = & $options;
		return $this;
	}

	/**
	 * Add form control or group control options to render
	 * more values for more specified submitted keys.
	 * Previous options will be merged with given options.
	 * @param array $options
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & AddOptions (array $options = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->options = array_merge($this->options, $options);
		return $this;
	}

	/**
	 * Return reference to configured options array.
	 * @return array
	 */
	public function & GetOptions () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->options;
	}

	/**
	 * Set `FALSE` if you don't want to translate options texts, default `TRUE`.
	 * @param bool $translateOptions 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetTranslateOptions ($translateOptions = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->translateOptions = $translateOptions;
		return $this;
	}

	/**
	 * Return boolean if options are translated or not.
	 * @return bool
	 */
	public function GetTranslateOptions () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->translateOptions;
	}

	/**
	 * Merge given field options with possible grouped options into single 
	 * level flatten array for submit checking purposes.
	 * @param array|NULL $fieldOptions
	 * @return array
	 */
	public function & GetFlattenOptions (array $fieldOptions = NULL) {
		if ($fieldOptions === NULL && $this->flattenOptions !== NULL)
			return $this->flattenOptions;
		$this->flattenOptions = [];
		/** @var $this \MvcCore\Ext\Forms\Field */
		$fieldOptions = $fieldOptions === NULL
			? $this->options
			: $fieldOptions;
		foreach ($fieldOptions as $key1 => $value1) {
			if (is_scalar($value1)) {
				// most simple key/value array options configuration
				$this->flattenOptions[$key1] = $value1;
			} else if (is_array($value1)) {
				if (array_key_exists('options', $value1) && is_array($value1['options'])) {
					// `<optgroup>` options configuration
					$subOptions = $value1['options'];
					foreach ($subOptions as $key2 => $value2) {
						if (is_scalar($value2)) {
							// most simple key/value array options configuration
							$this->flattenOptions[$key2] = $value2;
						} else if (is_array($value2)) {
							// advanced configuration with key, text, cs class, 
							// and any other attributes for single option tag
							$value = array_key_exists('value', $value2) 
								? $value2['value'] 
								: $key2;
							$text = array_key_exists('text', $value2) 
								? $value2['text'] 
								: $key2;
							$this->flattenOptions[$value] = $text;
						}
					}
				} else {
					// advanced configuration with `value`, `text`, `attrs` or css class, 
					// and any other attributes for single option tag:
					$value = array_key_exists('value', $value1) 
						? $value1['value'] 
						: $key1;
					$text = array_key_exists('text', $value1) 
						? $value1['text'] 
						: $key1;
					$this->flattenOptions[$value] = $text;
				}
			}
		}
		return $this->flattenOptions;
	}
}
