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

trait AccessKey
{
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey
	 * @var string|NULL
	 */
	protected $accessKey = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey
	 * @return string|NULL
	 */
	public function GetAccessKey () {
		return $this->accessKey;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey
	 * @param string $accessKey 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetAccessKey ($accessKey) {
		$this->accessKey = $accessKey;
		return $this;
	}
}
