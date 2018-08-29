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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 */
trait RowsColsWrap
{
	/**
	 * The number of visible text lines for the control. `NULL` value means no `cols`
	 * attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @var int|NULL
	 */
	protected $rows = NULL;

	/**
	 * The visible width of the text control, in average character widths. If it is 
	 * specified, it must be a positive integer. If it is not specified, the default 
	 * browser's value is `20`. `NULL` value means no `cols` attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @var int|NULL
	 */
	protected $cols = NULL;

	/**
	 * Indicates how the control wraps text. Possible values are:
	 * - `'hard'`: The browser automatically inserts line breaks (`CR+LF`) so that each
	 * 			line has no more than the width of the control; the `cols` attribute
	 * 			must also be specified for this to take effect.
	 * - `'soft'`: The browser ensures that all line breaks in the value consist of 
	 * 			a `CR+LF` pair, but does not insert any additional line breaks.
	 * - `'off '`: Like `soft` but changes appearance to `white-space: pre;` so line 
	 * 			segments exceeding `cols` are not wrapped and the `<textarea>` 
	 * 			becomes horizontally scrollable.
	 * If this attribute is not specified, `soft` is its default browser`s value.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @var string|NULL
	 */
	protected $wrap = NULL;
	
	/**
	 * The number of visible text lines for the control. `NULL` value means no `cols`
	 * attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @return int|NULL
	 */
	public function GetRows () {
		return $this->rows;
	}

	/**
	 * The number of visible text lines for the control. `NULL` value means no `cols`
	 * attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-rows
	 * @param int|NULL $rows 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRows ($rows) {
		$this->rows = $rows;
		return $this;
	}

	/**
	 * The visible width of the text control, in average character widths. If it is 
	 * specified, it must be a positive integer. If it is not specified, the default 
	 * browser's value is `20`. `NULL` value means no `cols` attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @return int|NULL
	 */
	public function GetCols () {
		return $this->cols;
	}

	/**
	 * The visible width of the text control, in average character widths. If it is 
	 * specified, it must be a positive integer. If it is not specified, the default 
	 * browser's value is `20`. `NULL` value means no `cols` attribute will bee rendered.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-cols
	 * @param int|NULL $columns 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCols ($columns = 20) {
		$this->cols = $columns;
		return $this;
	}

	/**
	 * Indicates how the control wraps text. Possible values are:
	 * - `'hard'`: The browser automatically inserts line breaks (`CR+LF`) so that each
	 * 			line has no more than the width of the control; the `cols` attribute
	 * 			must also be specified for this to take effect.
	 * - `'soft'`: The browser ensures that all line breaks in the value consist of 
	 * 			a `CR+LF` pair, but does not insert any additional line breaks.
	 * - `'off '`: Like `soft` but changes appearance to `white-space: pre;` so line 
	 * 			segments exceeding `cols` are not wrapped and the `<textarea>` 
	 * 			becomes horizontally scrollable.
	 * If this attribute is not specified, `soft` is its default browser`s value.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @return string|NULL
	 */
	public function GetWrap () {
		return $this->wrap;
	}

	/**
	 * Indicates how the control wraps text. Possible values are:
	 * - `'hard'`: The browser automatically inserts line breaks (`CR+LF`) so that each
	 * 			line has no more than the width of the control; the `cols` attribute
	 * 			must also be specified for this to take effect.
	 * - `'soft'`: The browser ensures that all line breaks in the value consist of 
	 * 			a `CR+LF` pair, but does not insert any additional line breaks.
	 * - `'off '`: Like `soft` but changes appearance to `white-space: pre;` so line 
	 * 			segments exceeding `cols` are not wrapped and the `<textarea>` 
	 * 			becomes horizontally scrollable.
	 * If this attribute is not specified, `soft` is its default browser`s value.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-wrap
	 * @param string|NULL $wrap 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetWrap ($wrap = 'soft') {
		$this->wrap = $wrap;
		return $this;
	}
}
