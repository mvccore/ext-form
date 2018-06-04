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

namespace MvcCore\Ext\Forms\Validators;


/**
 * Responsibility - Validate company VAT ID for EU states by requesting VEIS EU system by SOAP (or by GET if SOAP not implemented).
 * - This class adds dependence on your application to be online to submit your forms!
 * @see http://ec.europa.eu/taxation_customs/vies/
 */
class CompanyVatIdEu extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Main SOAP uri to EU VEIS system.
	 * @see http://ec.europa.eu/taxation_customs/vies/
	 */
	const SOAP_URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

	/**
	 * Backup GET request uri to EU VEIS system if `\SoapClient` PHP class doesn't exist.
	 * @see http://ec.europa.eu/taxation_customs/vies/
	 */
	const GET_URL = 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html?&action=check&check=Verify&memberStateCode={0}&number={1}';
	
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_VAT_ID = 0;
	const ERROR_TAX_ID = 1;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_VAT_ID	=> "Field '{0}' requires a valid VAT ID.",
		self::ERROR_TAX_ID	=> "Field '{0}' requires a valid TAX ID.",
	];

	/**
	 * Validate ZIP code by form internal localization property `$form->GetLocale()`.
	 * Validate EU company VAT ID by form internal localization property `$form->GetLocale()`
	 * and by SOAP or GET response from VIES system.
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$formLocale = $this->form->GetLocale();
		if (!$formLocale) {
			return $this->throwNewInvalidArgumentException(
				'Unable to validate ZIP code without configured '
				.'form `locale` property. Use `$form->SetLocale(\'[A-Z]{2}\');` '
				.'to internaly create proper ZIP code validator.'
			);
		} else {
			// remove all chars except: 'a-zA-Z0-9', spaces, dots and '-'
			$notCheckedValue = preg_replace('#^[^a-zA-Z0-9\-\. ]$#', '', mb_strtoupper(trim((string) $rawSubmittedValue)));
			if (!$notCheckedValue) return NULL;
			$result = $this->checkEuVatNumber($formLocale, (string) $notCheckedValue);
			if (!$result)
				$this->field->AddValidationError(
					static::GetErrorMessage(self::ERROR_VAT_ID)
				);
			return $result;
		}
	}

	/**
	 * Decode if to validate by SOAP or by GET, SOAP is more confident source.
	 * If you want to assign values into another form fields by SOAP or GET response,
	 * you can extend this class and this method to do it.
	 * @param string $localeCode 
	 * @param string $notCheckedValue 
	 * @return string|NULL
	 */
	protected function checkEuVatNumber ($localeCode, $notCheckedValue) {
		if (class_exists('\\SoapClient')) {
			$companyInfo = $this->checkEuVatNumberBySoap($localeCode, $notCheckedValue);
		} else {
			$companyInfo = $this->checkEuVatNumberByGet($localeCode, $notCheckedValue);
		}
		// var_dump($companyInfo); // `\stdClass` with: countryCode, vatNumber, name, address
		if (isset($companyInfo->vatNumber) && $companyInfo->vatNumber)
			return $companyInfo->vatNumber;
		return NULL;
	}

	/**
	 * Request VEIS by SOAP and get company info as `\stdClass` with: 
	 * `countryCode`, `vatNumber`, `name` and `address`.
	 * @param string $localeCode 
	 * @param string $notCheckedValue 
	 * @return \stdClass|NULL
	 */
	protected function checkEuVatNumberBySoap ($localeCode, $notCheckedValue) {
		try {
			$client = new \SoapClient(static::SOAP_URL, ['trace' => TRUE]);
		} catch(Exception $e) {
			$this->field->AddValidationError('VAT number validation SOAP error.');
			return NULL;
		}
		$response = $client->checkVat(['countryCode' => $localeCode, 'vatNumber' => $notCheckedValue]);
		if ($response->valid) {
			return (object) [
				'countryCode'	=> $response->countryCode,
				'vatNumber'		=> $response->vatNumber,
				'name'			=> trim($response->name), 
				'address'		=> trim($response->address),
			];
		} else {
			return NULL;
		}
	}

	/**
	 * Request VEIS by GET, parse HTML response and get company info as `\stdClass` with: 
	 * `countryCode`, `vatNumber`, `name` and `address`.
	 * @param string $localeCode 
	 * @param string $notCheckedValue 
	 * @return \stdClass|NULL
	 */
	protected function checkEuVatNumberByGet ($localeCode, $notCheckedValue) {
		$result = NULL;
		$url = str_replace(['{0}','{1}'], [$localeCode,$notCheckedValue], static::GET_URL);
		$valid = 'Yes, valid VAT number';
		$response = @file_get_contents($url);
		if (!$response) {
			$this->field->AddValidationError('VAT number validation error response.');
			return $result;
		}
		if (mb_strpos($response, $valid) !== FALSE) {
			$tableBegin = '<table id="vatResponseFormTable">';
			$tableEnd = '</table>';
			$parsingError = 'VAT number validation parsing error.';
			$tableBeginPos = mb_strpos($response, $tableBegin);
			if ($tableBeginPos === FALSE) {
				$this->field->AddValidationError($parsingError);
				return $result;
			}
			$tableEndPos = mb_strpos($response, $tableEnd, $tableBeginPos + mb_strlen($tableBegin));
			if ($tableEndPos === FALSE) {
				$this->field->AddValidationError($parsingError);
				return $result;
			}
			$table = mb_substr($response, $tableBeginPos, $tableEndPos + mb_strlen($tableEnd) - $tableBeginPos);
			$tableXml = @simplexml_load_string($table);
			if (!$tableXml) return $result;
			$result = [];
			$processing = [
				'Member State'					=> 'countryCode',
				'VAT Number'					=> 'vatNumber',
				'Name'							=> 'name',
				'Address'						=> 'address'
			];
			foreach ($tableXml->tr as $tr) {
				$tds = $tr->td;
				if (count($tds) > 1) {
					$cols = array();
					foreach ($tds as $td) $cols[] = trim((string) $td);
					$key = $cols[0];
					if (isset($processing[$cols[0]])) {
						$key = $processing[$cols[0]];
					} else {
						continue;
					}
					$val = $cols[1];
					$result[$key] = $key == 'vatNumber'
						? str_replace(' ', '', $val)
						: $val ;
				}
			}
			if (count($processing) === count($result)) {
				$result = (object) $result;
			} else {
				$this->field->AddValidationError($parsingError);
				return NULL;
			}
		}
		return $result;
	}
}
