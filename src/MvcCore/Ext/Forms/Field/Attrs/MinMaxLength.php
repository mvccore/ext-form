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
	/**
	 * Minimum characters length. Default value is `NULL`.
	 * @var int|NULL
	 */
	protected $minLength = NULL;

	/**
	 * Maximum characters length. Default value is `NULL`.
	 * @var int|NULL
	 */
	protected $maxLength = NULL;
	
	/**
	 * Get minimum characters length. Default value is `NULL`.
	 * @return int|NULL
	 */
	public function GetMinLength () {
		return $this->minLength;
	}

	/**
	 * Set minimum characters length. Default value is `NULL`.
	 * @param int $minLength 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMinLength ($minLength) {
		$this->minLength = $minLength;
		return $this;
	}
	
	/**
	 * Get maximum characters length. Default value is `NULL`.
	 * @return int|NULL
	 */
	public function GetMaxLength () {
		return $this->maxLength;
	}

	/**
	 * Set maximum characters length. Default value is `NULL`.
	 * @param int $minLength 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxLength ($maxLength) {
		$this->maxLength = $maxLength;
		return $this;
	}

	/**
	 * Check if field has proper validators if any value for minimum 
	 * or maximum characters count is set.
	 * @return void
	 */
	protected function setFormMinMaxLength () {
		if (
			($this->minLength !== NULL || $this->maxLength !== NULL) && 
			!isset($this->validators['MinMaxLength'])
		)
			$this->validators['MinMaxLength'] = 'MinMaxLength';
	}
}
