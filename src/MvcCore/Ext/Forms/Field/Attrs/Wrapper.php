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

trait Wrapper
{
	public $wrapper = '{control}';

	public function GetWrapper () {
		return $this->wrapper;
	}

	public function & SetWrapper ($wrapper) {
		$this->wrapper = $wrapper;
		return $this;
	}

	protected function renderControlWrapper ($renderedCode) {
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($wrapperReplacement, $this->wrapper) !== FALSE 
			? $this->wrapper 
			: $wrapperReplacement;
		return str_replace($wrapperReplacement, $renderedCode, $wrapper);
	}
}
