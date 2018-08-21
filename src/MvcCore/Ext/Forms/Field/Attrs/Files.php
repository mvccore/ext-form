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

trait Files
{
	const ALLOWED_FILE_NAME_CHARS_DEFAULT = '-a-zA-Z0-9@%&,~`._ !#$^()+={}[]<>\'';

	/**
	 * List of allowed file extensions or file mime types.
	 * If you are using file extensions to allow file to upload,
	 * you need to install extension `mvccore/ext-form-field-file-exts-and-mimes`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
	 * @var \string[]
	 */
	protected $accept = [];

	/**
	 * Boolean attribute indicates that capture of media directly from the 
	 * device's sensors using a media capture mechanism is preferred, 
	 * such as a webcam or microphone.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-capture
	 * @see https://www.w3.org/TR/html-media-capture/#dfn-media-capture-mechanism
	 * @var string|NULL
	 */
	protected $capture = NULL;

	/**
	 * @var string|NULL
	 */
	protected $allowedFileNameChars = NULL;

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
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetAccept (array $accept = []) {
		$this->accept = $accept;
		return $this;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-capture
	 * @return \string[]
	 */
	public function & GetCapture () {
		return $this->capture;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-capture
	 * @param \string[] $capture 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCapture ($capture = 'camera') {
		$this->capture = $capture;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetAllowedFileNameChars () {
		return $this->allowedFileNameChars;
	}

	/**
	 * @param string $allowedFileNameChars
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetAllowedFileNameChars ($allowedFileNameChars) {
		$this->allowedFileNameChars = $allowedFileNameChars;
		return $this;
	}
}
