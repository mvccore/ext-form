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

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Number`
 * Trait contains properties, getters and setters for 
 * protected properties `min`, `max` and `step`.
 */
trait MinMaxStepNumbers
{
	/**
	 * Minimum value for `Number` field(s) in `float` or in `integer`.
	 * @var float|NULL
	 */
	protected $min = NULL;

	/**
	 * Maximum value for `Number` field(s) in `float` or in `integer`.
	 * @var float|NULL
	 */
	protected $max = NULL;

	/**
	 * Step value for `Number` in `float` or in `integer`.
	 * @var float|NULL
	 */
	protected $step = NULL;

	/**
	 * Get minimum value for `Number` field(s) in `float`.
	 * @return float|NULL
	 */
	public function GetMin () {
		return $this->min;
	}

	/**
	 * Set minimum value for `Number` field(s) in `float` or in `integer`.
	 * @param float|int|NULL $min
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMin ($min) {
		$this->min = floatval($min);
		return $this;
	}

	/**
	 * Get maximum value for `Number` field(s) in `float`.
	 * @return float|NULL
	 */
	public function GetMax () {
		return $this->max;
	}

	/**
	 * Set maximum value for `Number` field(s) in `float` or in `integer`.
	 * @param float|NULL $max
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMax ($max) {
		$this->max = floatval($max);
		return $this;
	}

	/**
	 * Get step value for `Number` in `float`.
	 * @return float|NULL
	 */
	public function GetStep () {
		return $this->step;
	}

	/**
	 * Set step value for `Number` in `float` or in `integer`.
	 * @param float|int|NULL $step
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetStep ($step) {
		$this->step = floatval($step);
		return $this;
	}
}
