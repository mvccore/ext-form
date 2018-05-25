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

trait MinMaxStepNumber
{
	protected $min = NULL;
	protected $max = NULL;
	protected $step = NULL;

	public function GetMin () {
		return $this->min;
	}

	public function & SetMin ($min) {
		$this->min = $min;
		return $this;
	}

	public function GetMax () {
		return $this->max;
	}

	public function & SetMax ($max) {
		$this->max = $max;
		return $this;
	}

	public function GetStep () {
		return $this->step;
	}

	public function & SetStep ($step) {
		$this->step = $step;
		return $this;
	}
}
