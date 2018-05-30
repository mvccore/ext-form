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

namespace MvcCore\Ext\Forms\Field\Attrs;

trait Options
{
	/**
	 * Form group control options to render
	 * more subcontrol attributes for specified
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
	 *     'f' => 'Female',
	 *     'm' => 'Male',
	 * );
	 *
	 * // Or you can use more adwanced configuration with css class names 
	 * // and html element attributes, let's consider html code like this:
	 * //   <label for="gender-f" class="female">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 * //   <label for="gender-m" class="male">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 * // For that use this configuration:
	 * $field->name = 'gender';
	 * $field->options = array(
	 *     'f' => array(
	 *         'text'  => 'Female',	// text key will be also automaticly translated
	 *         'class' => 'female',
	 *         'attrs' => array('data-any' => 'something-for-females'),
	 *     ),
	 *     'm' => array(
	 *         'text'  => 'Male', // text key will be also automaticly translated
	 *         'class' => 'male',
	 *         'attrs' => array('data-any' => 'something-for-males'),
	 *     ),
	 * ));
	 * ```
	 * @requires
	 * @var array
	 */
	protected $options = array();

	/**
	 * Set form group control options to render
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
	 *     // field values will be automaticly translated, 
	 *     // if form has configured translator `callable`
	 *     'f' => 'Female',
	 *     'm' => 'Male',
	 * ));
	 * 
	 * // Or you can use more adwanced configuration with css class names 
	 * // and html element attributes, let's consider html code like this:
	 * //   <label for="gender-f" class="female">Female:</label>
	 * //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 * //   <label for="gender-m" class="male">Male:</label>
	 * //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 * // For that use this configuration:
	 * $field->SetName('gender')->SetOptions(array(
	 *     'f' => array(
	 *         'text'  => 'Female',	// text key will be also automaticly translated
	 *         'class' => 'female',
	 *         'attrs' => array('data-any' => 'something-for-females'),
	 *     ),
	 *     'm' => array(
	 *         'text'  => 'Male', // text key will be also automaticly translated
	 *         'class' => 'male',
	 *         'attrs' => array('data-any' => 'something-for-males'),
	 *     ),
	 * ));
	 * ```
	 * @param array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function & SetOptions (array $options = array()) {
		$this->options = & $options;
		return $this;
	}

	/**
	 * Return reference to configured options array.
	 * @return array
	 */
	public function & GetOptions () {
		return $this->options;
	}

	/**
	 * Return all options keys as array of strings.
	 * @return \string[]
	 */
	public function & GetAllOptionsKeys () {
		$allOptionsKeys = array();
		foreach ($this->options as $firstLevelKey => & $firstLevelOption) {
			if (isset($firstLevelOption['options']) && gettype($firstLevelOption['options']) == 'array') {
				foreach ($firstLevelOption['options'] as $secondLevelKey => & $secondLevelOption) {
					$allOptionsKeys[(string) $secondLevelKey] = TRUE;
				}
			} else {
				$allOptionsKeys[(string) $firstLevelKey] = TRUE
			}
		}
		return array_keys($allOptionsKeys);
	}
}
