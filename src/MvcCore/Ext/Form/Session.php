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

namespace MvcCore\Ext\Form;

trait Session
{
	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & ClearSession () {
		$this->values = array();
		$this->errors = array();
		$session = & $this->getSession();
		$session->values = array();
		$session->csrf = array();
		$session->errors = array();
		return $this;
	}

	/**
	 * Get session namespace reference with configured expiration
	 * and predefined fields `values`, `csrf` and `errors` as arrays.
	 * @return \MvcCore\Interfaces\ISession
	 */
	protected function & getSession () {
		if (isset(self::$allFormsSessions[$this->id])) {
			$sessionNamespace = & self::$allFormsSessions[$this->id];
		} else {
			$sessionClass = self::$sessionClass;
			$toolClass = self::$toolClass;
			$formIdPc = $this->id;
			if (strpos($formIdPc, '-') !== FALSE)
				$formIdPc = $toolClass::GetPascalCaseFromDashed($formIdPc);
			if (strpos($formIdPc, '_') !== FALSE)
				$formIdPc = $toolClass::GetPascalCaseFromUnderscored($formIdPc);
			$namespaceName = '\\MvcCore\\Ext\\Form\\' . ucfirst($formIdPc);
			$sessionNamespace = $sessionClass::GetNamespace($namespaceName);
			// Do not use hoops expiration, because there is better
			// to set up any large value into session namespace
			// or zero value to browser close and after rendered
			// errors just clear the errors.
			//$sessionNamespace->SetExpirationHoops(1);
			$sessionNamespace->SetExpirationSeconds($this->sessionExpiration);
			if (!isset($sessionNamespace->values)) $sessionNamespace->values = array();
			if (!isset($sessionNamespace->csrf)) $sessionNamespace->csrf = array();
			if (!isset($sessionNamespace->errors)) $sessionNamespace->errors = array();
			self::$allFormsSessions[$this->id] = & $sessionNamespace;
		}
		return $sessionNamespace;
	}
}
