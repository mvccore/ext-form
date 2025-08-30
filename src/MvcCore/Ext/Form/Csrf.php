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
 * @mixin \MvcCore\Ext\Form
 */
trait Csrf {

	/**
	 * @inheritDoc
	 * @param  bool $enabled 
	 * @return \MvcCore\Ext\Form
	 */
	public function SetCsrfEnabled ($enabled = TRUE) {
		$this->csrfEnabled = $enabled;
		return $this;
	}

	/**
	 * @inheritDoc
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
	 * @param  array<string,mixed> $rawRequestParams Raw request params given into `Submit()` method or all `\MvcCore\Request` params.
	 * @throws \MvcCore\Application\TerminateException
	 * @return ?bool
	 */
	public function SubmitCsrfTokens (array & $rawRequestParams = []) {
		if (!$this->csrfEnabled) return NULL;
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
			$securityErrorHandlers = $this->application->__get('securityErrorHandlers');
			if (count($securityErrorHandlers) > 0) {
				$errorHandlersArgs = [$this->request, $this->response, $this];
				$handlersResult = !$this->application->ProcessCustomHandlers($securityErrorHandlers, $errorHandlersArgs);
				// there is possible to continue if any handler doesn't return `FALSE` and
				// application is not terminated, this protection is not so strict
				if (!$handlersResult) $this->Terminate();
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @throws \Exception
	 * @return array<?string>
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
