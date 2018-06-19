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

trait RowsColsWrap
{
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @var int|NULL
	 */
	protected $rows = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @var int|NULL
	 */
	protected $cols = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @var string|NULL
	 */
	protected $wrap = NULL;
	
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @return int|NULL
	 */
	public function GetRows () {
		return $this->rows;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @param int $rows 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRows ($rows) {
		$this->rows = $rows;
		return $this;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @return int|NULL
	 */
	public function GetCols () {
		return $this->cols;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @param int $columns 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCols ($columns) {
		$this->cols = $columns;
		return $this;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @return string|NULL
	 */
	public function GetWrap () {
		return $this->wrap;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @param string $wrap 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetWrap ($wrap) {
		$this->wrap = $wrap;
		return $this;
	}
}
