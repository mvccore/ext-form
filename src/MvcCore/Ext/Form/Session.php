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

namespace MvcCore\Ext\Forms\Core;

require_once(__DIR__.'/../../Form.php');
require_once('Base.php');

use \MvcCore\Ext\Form;

class Helpers
{
	/**
	 * All forms session namespace storrage reference for values.
	 * @var \MvcCore\Interfaces\ISession
	 */
	protected static $allFormsSessionValues = NULL;

	/**
	 * All forms session namespace storrage reference for CSRF tokens.
	 * @var \MvcCore\Interfaces\ISession
	 */
	protected static $allFormsSessionCsrf = NULL;

	/**
	 * All forms session namespace storrage reference for errors.
	 * @var \MvcCore\Interfaces\ISession
	 */
	protected static $allFormsSessionErrors = NULL;

	/**
	 * Cached value from `\MvcCore\Application::GetInstance()->GetSessionClass();`
	 * @var string
	 */
	private static $_sessionClass = NULL;

	/**
	 * Get form values session namespace storrage reference for current form.
	 * @return array
	 */
	protected function & getSessionValues () {
		if (self::$allFormsSessionValues === NULL)
			self::$allFormsSessionValues = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Data');
		$result = array();
		if (isset(self::$allFormsSessionValues->{$this->id})) {
			$result = & self::$allFormsSessionValues->{$this->id};
		} else {
			self::$allFormsSessionValues->{$this->id} = & $result;
		}
		return $result;
	}

	/**
	 * Set form values in session namespace storrage for current form.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function & setSessionValues (& $values = array()) {
		if (self::$allFormsSessionValues === NULL)
			self::$allFormsSessionValues = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Data');
		self::$allFormsSessionValues->{$this->id} = & $values;
		return $this;
	}

	/**
	 * Get form CSRF (Cross Site Request Forgery) protecting tokens
	 * session namespace storrage reference for current form.
	 * @return array
	 */
	protected function & getSessionCsrf () {
		if (self::$allFormsSessionCsrf === NULL)
			self::$allFormsSessionCsrf = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Csrf');
		$result = array();
		if (isset(self::$allFormsSessionCsrf->{$this->id})) {
			$result = & self::$allFormsSessionCsrf->{$this->id};
		} else {
			self::$allFormsSessionCsrf->{$this->id} = & $result;
		}
		return $result;
	}

	/**
	 * Get form CSRF (Cross Site Request Forgery) protecting tokens
	 * in session namespace storrage for current form.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function & setSessionCsrf (& $tokens = array()) {
		if (self::$allFormsSessionCsrf === NULL)
			self::$allFormsSessionCsrf = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Csrf');
		self::$allFormsSessionCsrf->{$this->id} = & $tokens;
		return $this;
	}

	/**
	 * Get forms error(s) session namespace storrage reference for current form.
	 * @return array
	 */
	protected function & getSessionErrors () {
		if (self::$allFormsSessionErrors === NULL)
			self::$allFormsSessionErrors = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Errors');
		$result = array();
		if (isset(self::$allFormsSessionErrors->{$this->id})) {
			$result = & self::$allFormsSessionErrors->{$this->id};
		} else {
			self::$allFormsSessionErrors->{$this->id} = & $result;
		}
		return $result;
	}

	/**
	 * Set forms error(s) in session namespace storrage reference for current form.
	 * @return \stdClass
	 */
	protected function & setSessionErrors (& $errors = array()) {
		if (self::$allFormsSessionErrors === NULL)
			self::$allFormsSessionErrors = & $this->getSessionNamespace('\\MvcCore\\Ext\\Form\\Errors');
		self::$allFormsSessionErrors->{$this->id} = & $errors;
		return $this;
	}

	/**
	 * Get session namespace reference with configured expiration.
	 * @param string $namespaceName
	 * @return \MvcCore\Interfaces\ISession
	 */
	protected function & getSessionNamespace ($namespaceName) {
		if (self::$_sessionClass === NULL)
			self::$_sessionClass = $this->application->GetSessionClass();
		$sessionClass = self::$_sessionClass;
		$sessionNamespace = $sessionClass::GetNamespace($namespaceName);
		// Do not use hoops expiration, because there is better
		// to set up any large value into session namespace
		// or zero value to browser close and after rendered
		// errors just clear the errors.
		//$sessionNamespace->SetExpirationHoops(1);
		$sessionNamespace->SetExpirationSeconds($this->sessionExpirationSeconds);
		return $sessionNamespace;
	}
}
