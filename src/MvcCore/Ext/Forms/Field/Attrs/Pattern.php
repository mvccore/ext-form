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

trait Pattern
{
	protected $pattern = NULL;

	public function GetPattern () {
		return $this->pattern;
	}

	public function & SetPattern ($pattern) {
		$this->pattern = $pattern;
		return $this;
	}

	
	protected function checkValidatorsPattern () {
		if ($this->pattern && !in_array('Pattern', $this->validators))
			$this->validators[] = 'Pattern';
	}
}
