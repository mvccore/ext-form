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

namespace MvcCore\Ext\Form;

use \MvcCore\Application\IConstants as AppConsts;

/**
 * Trait for class `MvcCore\Ext\Form` containing logic and methods to work with
 * values necessary store in session. It use configured core class `\MvcCore\Session`.
 * @mixin \MvcCore\Ext\Form
 */
trait Session {

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Form
	 */
	public function ClearSession () {
		$this->values = [];
		$this->errors = [];
		$session = & $this->getSession();
		$session->values = [];
		if ($this->csrfEnabled)
			$session->csrf = [];
		$session->errors = [];
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Form
	 */
	public function SaveSession () {
		$session = & $this->getSession();
		$session->errors = $this->errors;
		$session->values = $this->values;
		return $this;
	}

	/**
	 * Get session namespace reference with configured expiration
	 * and predefined fields `values`, `csrf` and `errors` as arrays.
	 * @return \MvcCore\Session
	 */
	protected function & getSession () {
		if (isset(self::$allFormsSessions[$this->id])) {
			$sessionNamespace = & self::$allFormsSessions[$this->id];
		} else {
			$sessionClass = self::$sessionClass;
			$toolClass = self::$toolClass;
			$formIdPc = $this->id;
			if (strpos($formIdPc, '-') !== FALSE)
				$formIdPc = $toolClass::GetPascalCaseFromDashed(str_replace('-', '/', $formIdPc));
			if (strpos($formIdPc, '_') !== FALSE)
				$formIdPc = $toolClass::GetPascalCaseFromUnderscored(str_replace('_', '/', $formIdPc));
			$formIdPcUc = ucfirst($formIdPc);
			$formIdPcUc = str_replace('/', '\\', $formIdPcUc);
			$namespaceName = "\\MvcCore\\Ext\Form\\{$formIdPcUc}";
			$sessionNamespace = $sessionClass::GetNamespace($namespaceName);
			// Do not use hoops expiration, because there is better
			// to set up any large value into session namespace
			// or zero value to browser close and after rendered
			// errors just clear the errors.
			//$sessionNamespace->SetExpirationHoops(1);
			$sessionNamespace->SetExpirationSeconds($this->GetSessionExpiration());
			if (!isset($sessionNamespace->values)) $sessionNamespace->values = [];
			if ($this->csrfEnabled)
				if (!isset($sessionNamespace->csrf)) $sessionNamespace->csrf = [];
			if (!isset($sessionNamespace->errors)) $sessionNamespace->errors = [];
			self::$allFormsSessions[$this->id] = & $sessionNamespace;
		}
		return $sessionNamespace;
	}

	/**
	 * If application security mode is configured with security cookie token,
	 * regenerate this token if necessary by min/max token time settings.
	 * If token is regenerated, return `TRUE` or `FALSE`. If application is not
	 * configured like that, return `NULL`.
	 * @return ?bool
	 */
	protected function regenerateSecurityToken () {
		$securityCookieMode = (
			($this->application->GetSecurityProtection() & AppConsts::SECURITY_PROTECTION_COOKIE) != 0
		);
		if (!$securityCookieMode)
			return NULL;
		$sessionClass = self::$sessionClass;
		return $sessionClass::RegenerateSecurityToken(FALSE, TRUE);
	}
}
