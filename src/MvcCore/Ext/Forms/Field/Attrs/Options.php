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
	 * @var array
	 */
	protected $options = array();

	/**
	 * @param array $options 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function & SetOptions (array $options = array()) {
		$this->options = & $options;
		return $this;
	}

	/**
	 * @return array
	 */
	public function GetOptions () {
		return $this->options;
	}
}
