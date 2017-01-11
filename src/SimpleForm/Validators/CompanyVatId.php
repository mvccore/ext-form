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

require_once('/../../SimpleForm.php');
require_once('CompanyTaxId.php');

class SimpleForm_Validators_CompanyVatId extends SimpleForm_Validators_CompanyTaxId
{
	protected static $exceptionMessage = "No company VAT ID verification method for language: '{lang}'.";
	protected static $errorMessageKey = SimpleForm::VAT_ID;
	protected function validate_CS ($id = '')
	{
		$id = preg_replace('#\s+#', '', $id);
		if (substr($id, 0, 2) == 'CZ') {
			$id = substr($id, 2);
			return parent::validate_CS($id);
		} else {
			return FALSE;
		}
	}
}
