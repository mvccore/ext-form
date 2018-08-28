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
 * - `\MvcCore\Ext\Forms\Fields\Image`
 */
trait WidthHeight
{
	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `width` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @var int|NULL
	 */
	protected $width = NULL;

	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `height` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @var int|NULL
	 */
	protected $height = NULL;
	
	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `width` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @return int|NULL
	 */
	public function GetWidth () {
		return $this->width;
	}

	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `width` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-width
	 * @param int $width
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetWidth ($width) {
		$this->width = $width;
		return $this;
	}
	
	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `height` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @return int|NULL
	 */
	public function GetHeight () {
		return $this->height;
	}

	/**
	 * If the value of the `type` attribute is `image`, this attribute defines
	 * the `height` of the image displayed for the button in pixels.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-height
	 * @param int $height 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetHeight ($height) {
		$this->height = $height;
		return $this;
	}
}
