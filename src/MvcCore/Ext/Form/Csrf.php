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

/**
 * Trait for class `MvcCore\Ext\Form` containing methods to create, get and 
 * verify CSRF tokens and to process CSRF error handlers if tokens are not valid.
 */
trait Csrf
{
	/**
	 * Call all CSRF (Cross Site Request Forgery) error handlers in static queue.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form The form instance where CSRF error happened.
	 * @param string $errorMsg Translated error message about CSRF invalid tokens.
	 * @return void
	 */
	public static function ProcessCsrfErrorHandlersQueue (\MvcCore\Ext\Forms\IForm $form, $errorMsg) {
		$request = $form->GetRequest();
		$response = $form->GetResponse();
		foreach (static::$csrfErrorHandlers as $handlersRecord) {
			list ($handler, $isClosure) = $handlersRecord;
			try {
				if ($isClosure) {
					$handler($form, $request, $response, $errorMsg);
				} else {
					call_user_func($handler, $form, $request, $response, $errorMsg);
				}
			} catch (\Exception $e) {
				$debugClass = $form->GetApplication()->GetDebugClass();
				$debugClass::Log($e, \MvcCore\IDebug::CRITICAL);
			}
		}
	}

	/**
	 * Enable or disable CSRF checking, enabled by default.
	 * @param bool $enabled 
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SetEnableCsrf ($enabled = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->csrfEnabled = $enabled;
		return $this;
	}

	/**
	 * Return current CSRF (Cross Site Request Forgery) hidden
	 * input name and it's value as `\stdClass`with  keys `name` and `value`.
	 * @return \stdClass
	 */
	public function GetCsrf () {
		$session = & $this->getSession();
		list($name, $value) = $session->csrf;
		return (object) ['name' => $name, 'value' => $value];
	}

	/**
	 * Check CSRF (Cross Site Request Forgery) sent tokens from user with session tokens.
	 * If tokens are different, add form error and process CSRF error handlers queue.
	 * If there is any exception caught in CSRF error handlers queue, it's logged
	 * by configured core debug class with `CRITICAL` flag.
	 * @param array $rawRequestParams Raw request params given into `Submit()` method or all `\MvcCore\Request` params.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SubmitCsrfTokens (array & $rawRequestParams = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if (!$this->csrfEnabled) return $this;
		$result = FALSE;
		$session = & $this->getSession();
		list($name, $value) = $session->csrf 
			? $session->csrf : 
			[NULL, NULL];
		if ($name !== NULL && $value !== NULL)
			if (isset($rawRequestParams[$name]) && $rawRequestParams[$name] === $value)
				$result = TRUE;
		if (!$result) {
			$errorMsg = $this->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::CSRF);
			if ($this->translate)
				$errorMsg = call_user_func($this->translator, $errorMsg);
			$this->AddError($errorMsg);
			static::ProcessCsrfErrorHandlersQueue($this, $errorMsg);
		}
		return $this;
	}

	/**
	 * Create new fresh CSRF (Cross Site Request Forgery) tokens,
	 * store them in current form session namespace and return them.
	 * @return \string[]
	 */
	public function SetUpCsrf () {
		$requestUrl = $this->request->GetBaseUrl() . $this->request->GetPath();
		if (function_exists('openssl_random_pseudo_bytes')) {
			$randomHash = bin2hex(openssl_random_pseudo_bytes(32));
		} else if (PHP_VERSION_ID >= 70000) {
			$randomHash = bin2hex(random_bytes(32));
		} else {
			$randomHash = '';
			for ($i = 0; $i < 32; $i++) 
				$randomHash .= str_pad(dechex(rand(0,255)),2,'0',STR_PAD_LEFT);
		}
		$nowTime = (string)time();
		$name = '____'.sha1($this->id . $requestUrl . 'name' . $nowTime . $randomHash);
		$value = sha1($this->id . $requestUrl . 'value' . $nowTime . $randomHash);
		$session = & $this->getSession();
		$session->csrf = [$name, $value];
		return [$name, $value];
	}
}
