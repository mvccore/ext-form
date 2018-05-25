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

trait RowsCols
{
	protected $rows = NULL;
	protected $cols = NULL;
	
	public function GetRows () {
		return $this->rows;
	}

	public function & SetRows ($rows) {
		$this->rows = $rows;
		return $this;
	}

	public function GetCols () {
		return $this->cols;
	}

	public function & SetCols ($columns) {
		$this->cols = $columns;
		return $this;
	}
}
