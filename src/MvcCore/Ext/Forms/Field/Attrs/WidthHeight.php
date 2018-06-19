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

trait WidthHeight
{
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @var int|NULL
	 */
	protected $width = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @var int|NULL
	 */
	protected $height = NULL;
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @return int|NULL
	 */
	public function GetWidth () {
		return $this->width;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @param int $width
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetWidth ($width) {
		$this->width = $width;
		return $this;
	}
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @return int|NULL
	 */
	public function GetHeight () {
		return $this->height;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @param int $height 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetHeight ($height) {
		$this->height = $height;
		return $this;
	}
}
