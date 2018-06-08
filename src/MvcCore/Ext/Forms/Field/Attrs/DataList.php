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

trait DataList
{
	/**
	 * @var string|NULL
	 */
	protected $list = NULL;

	/**
	 * @return \string[]
	 */
	public function & GetList () {
		return $this->list;
	}

	/**
	 * @param string $dataListId 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetList ($dataListId) {
		$this->list = $dataListId;
		return $this;
	}
}
