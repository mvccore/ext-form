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
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Form $form     The form instance where CSRF error happened.
	 * @param  string            $errorMsg Translated error message about CSRF invalid tokens.
	 * @return void
	 */
	public static function ProcessCsrfErrorHandlersQueue (\MvcCore\Ext\IForm $form, $errorMsg) {
		/** @var \MvcCore\Ext\Form $form */
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
	 * @inheritDocs
	 * @param  bool $enabled 
	 * @return \MvcCore\Ext\Form
	 */
	public function SetEnableCsrf ($enabled = TRUE) {
		$this->csrfEnabled = $enabled;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return \stdClass
	 */
	public function GetCsrf () {
		$session = & $this->getSession();
		list($name, $value) = $session->csrf;
		return (object) ['name' => $name, 'value' => $value];
	}

	/**
	 * @inheritDocs
	 * @param  array $rawRequestParams Raw request params given into `Submit()` method or all `\MvcCore\Request` params.
	 * @return \MvcCore\Ext\Form
	 */
	public function SubmitCsrfTokens (array & $rawRequestParams = []) {
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
	 * @inheritDocs
	 * @return \string[]
	 */
	public function SetUpCsrf () {
		$session = & $this->getSession();
		if ($this->response->GetCode() >= 400 && is_array($session->csrf)) {
			// do not regenerate form CSRF tokens for 404 or 500 requests
			return $session->csrf;
		}
		$requestUrl = $this->request->GetBaseUrl() . $this->request->GetPath();
		if (function_exists('openssl_random_pseudo_bytes')) {
			$randomHash = bin2hex(openssl_random_pseudo_bytes(32));
		} else if (PHP_VERSION_ID >= 70000) {
			$randomHash = bin2hex(random_bytes(32));
		} else {
			$randomHash = '';
			for ($i = 0; $i < 32; $i++) 
				/** @see https://github.com/php/php-src/blob/master/ext/standard/mt_rand.c */
				$randomHash .= str_pad(dechex(rand(0,255)),2,'0',STR_PAD_LEFT);
		}
		$nowTime = (string)time();
		$name = '____'.sha1($this->id . $requestUrl . 'name' . $nowTime . $randomHash);
		$value = sha1($this->id . $requestUrl . 'value' . $nowTime . $randomHash);
		
		$session->csrf = [$name, $value];
		return [$name, $value];
	}
}
