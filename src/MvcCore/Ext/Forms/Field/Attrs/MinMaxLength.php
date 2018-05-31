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

trait MinMaxLength
{
	protected $minLength = NULL;
	protected $maxLength = NULL;

	public function GetMinLength () {
		return $this->minLength;
	}

	public function & SetMinLength ($minLength) {
		$this->minLength = $minLength;
		return $this;
	}

	public function GetMaxLength () {
		return $this->maxLength;
	}

	public function & SetMaxLength ($maxLength) {
		$this->maxLength = $maxLength;
		return $this;
	}

	protected function checkValidatorsMinMaxLength () {
		if ($this->minLength && !isset($this->validators['MinLength']))
			$this->validators['MinLength'] = 'MinLength';
		if ($this->maxLength && !isset($this->validators['MaxLength']))
			$this->validators['MaxLength'] = 'MaxLength';
	}
}
