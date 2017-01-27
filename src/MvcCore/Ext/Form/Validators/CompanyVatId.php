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

require_once(__DIR__.'/../../Form.php');
require_once('CompanyId.php');

namespace MvcCore\Ext\Form\Validators;

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class CompanyVatId extends CompanyId
{
	/**
	 * Error message key
	 * @var string
	 */
	protected static $errorMessageKey = \MvcCore\Ext\Form::VAT_ID;
	/**
	 * Check company ID by regular expression base.
	 * Return true if company ID matches.
	 * @param string $regExpBase
	 * @param string $locale
	 * @param string $id
	 * @return bool
	 */
	protected function checkCompanyIdByRegExpBase ($regExpBase, $locale, $id) {
		preg_match("#^($locale)$regExpBase$#", $id, $matches);
		return count($matches) > 0;
	}
}
