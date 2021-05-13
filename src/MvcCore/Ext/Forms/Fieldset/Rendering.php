<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Fieldset;

/**
 * @mixin \MvcCore\Ext\Forms\Fieldset
 */
trait Rendering {

	/**
	 * @return string
	 */
	public function Render () {
		$result = ['<fieldset>'];
		$result[] = $this->RenderLegend();
		$result[] = $this->RenderErrors();
		$result[] = $this->RenderContent();
		$result[] = '</fieldset>';
		return implode('', $result);
	}
	
	/**
	 * @return string
	 */
	public function RenderErrors () {
		return '';
	}
	
	/**
	 * @return string
	 */
	public function RenderLegend () {
		$legendContent = strip_tags($this->legend, static::ALLOWED_LEGEND_ELEMENTS);
		return '<legend>' . $legendContent . '</legend>';
	}
	
	/**
	 * @return string
	 */
	public function RenderContent () {
		return '';
	}
}
