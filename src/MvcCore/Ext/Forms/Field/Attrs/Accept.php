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

trait Accept
{
	/**
	 * List of allowed file extensions or file mime types.
	 * If you are using file extensions to allow file to upload,
	 * you need to install extension `mvccore/ext-form-field-file-exts-and-mimes`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
	 * @var \string[]
	 */
	protected $accept = [];

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
	 * @return \string[]
	 */
	public function & GetAccept () {
		return $this->accept;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
	 * @param \string[] $accept 
	 * @return Accept
	 */
	public function & SetAccept (array $accept = []) {
		$this->accept = $accept;
		return $this;
	}
}
