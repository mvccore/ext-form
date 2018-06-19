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

trait TabIndex
{
	/**
	 * An integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. It can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @var int|NULL
	 */
	protected $tabIndex = NULL;

	/**
	 * Get an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. It can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @return int|NULL
	 */
	public function GetTabIndex () {
		return $this->tabIndex;
	}

	/**
	 * Set an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. It can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @param int $tabIndex 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetTabIndex ($tabIndex) {
		$this->tabIndex = $tabIndex;
		return $this;
	}
}
