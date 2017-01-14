<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once(__DIR__.'/../../SimpleForm.php');
require_once('CompanyId.php');

class SimpleForm_Validators_CompanyVatId extends SimpleForm_Validators_CompanyId
{
	/**
	 * Error message key
	 * @var string
	 */
	protected static $errorMessageKey = SimpleForm::VAT_ID;
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
