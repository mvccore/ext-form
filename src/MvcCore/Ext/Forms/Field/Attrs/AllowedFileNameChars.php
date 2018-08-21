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

trait AllowedFileNameChars
{
	const ALLOWED_FILE_NAME_CHARS_DEFAULT = '-a-zA-Z0-9@%&,~`._ !#$^()+={}[]<>\'';

	/**
	 * @var string|NULL
	 */
	protected $allowedFileNameChars = NULL;

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
