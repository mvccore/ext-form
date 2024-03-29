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

/**
 * Trait for class `MvcCore\Ext\Form` containing methods to create, get and 
 * verify CSRF tokens and to process CSRF error handlers if tokens are not valid.
 * @deprecated
 * @mixin \MvcCore\Ext\Form
 */
trait Csrf {

	/**
	 * @inheritDoc
	 * @deprecated
	 * @param  \MvcCore\Ext\Form $form     The form instance where CSRF error happened.
	 * @param  string            $errorMsg Translated error message about CSRF invalid tokens.
	 * @return void
	 */
	public static function ProcessCsrfErrorHandlersQueue (\MvcCore\Ext\IForm $form, $errorMsg) {
		/** @var \MvcCore\Ext\Form $form */
		if (!$form->csrfEnabled) return;
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
			} catch (\Throwable $e) {
				$debugClass = $form->GetApplication()->GetDebugClass();
				$debugClass::Log($e, \MvcCore\IDebug::CRITICAL);
			}
		}
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @throws \Exception
	 * @param  bool $enabled 
	 * @return \MvcCore\Ext\Form
	 */
	public function SetEnableCsrf ($enabled = TRUE) {
		$csrfMode = $this->application->GetCsrfProtection();
		if (($csrfMode & \MvcCore\IApplication::CSRF_PROTECTION_DISABLED) != 0) {
			throw new \Exception("CSRF protection disabled globally.");
		} else if (($csrfMode & \MvcCore\IApplication::CSRF_PROTECTION_COOKIE) != 0) {
			throw new \Exception("CSRF protection mode configured as http cookie mode.");
		}
		$this->csrfEnabled = $enabled;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @throws \Exception
	 * @return \stdClass
	 */
	public function GetCsrf () {
		if (!$this->csrfEnabled) throw new \Exception(
			"[".get_class($this)."] CSRF protection is disabled for this form."
		);
		$this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $this->submit);
		$session = & $this->getSession();
		list($name, $value) = $session->csrf;
		return (object) ['name' => $name, 'value' => $value];
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @param  array $rawRequestParams Raw request params given into `Submit()` method or all `\MvcCore\Request` params.
	 * @return \MvcCore\Ext\Form
	 */
	public function SubmitCsrfTokens (array & $rawRequestParams = []) {
		if (!$this->csrfEnabled) return $this;
		$this->DispatchStateCheck(static::DISPATCH_STATE_SUBMITTED, TRUE);
		$result = FALSE;
		list($name, $value) = count($this->csrfValue) > 0
			? $this->csrfValue
			: [NULL, NULL];
		if ($name !== NULL && $value !== NULL)
			if (isset($rawRequestParams[$name]) && $rawRequestParams[$name] === $value)
				$result = TRUE;
		if (!$result) {
			$errorMsg = static::GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::CSRF);
			if ($this->translate)
				$errorMsg = call_user_func($this->translator, $errorMsg);
			$this->AddError($errorMsg);
			static::ProcessCsrfErrorHandlersQueue($this, $errorMsg);
		}
		return $this;
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @throws \Exception
	 * @return list<string|NULL>
	 */
	public function SetUpCsrf () {
		if (!$this->csrfEnabled) throw new \Exception(
			"[".get_class($this)."] CSRF protection is disabled for this form."
		);
		$session = $this->getSession();
		$this->csrfValue = $session->csrf;
		$sessionHasValues = is_array($this->csrfValue) && count($this->csrfValue) > 0;
		// do not regenerate form CSRF tokens for already regenerated and for 404 or 500 requests
		if (!$sessionHasValues)
			$this->csrfValue = [NULL, NULL];
		if ($sessionHasValues || $this->response->GetCode() >= 400) 
			return $this->csrfValue;
		// generate new CSRF tokens into session
		$toolClass = $this->application->GetToolClass();
		$randomHash = $toolClass::GetRandomHash(64);
		$requestUrl = $this->request->GetBaseUrl() . $this->request->GetPath();
		$nowTime = (string) time();
		$name = '____'.sha1($randomHash . 'name' . $this->id . $requestUrl . $nowTime);
		$value = sha1($randomHash . 'value' . $this->id . $requestUrl . $nowTime);
		$session->csrf = [$name, $value];
		// return current CSRF tokens
		return $this->csrfValue;
	}
}
