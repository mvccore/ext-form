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

trait Csrf
{
	/**
	 * Return current cross site request forgery hidden
	 * input name and it's value as stdClass.
	 * Result stdClass elements has keys 'name' and 'value'.
	 * @return \stdClass
	 */
	public function GetCsrf () {
		include_once('Form/Core/Helpers.php');
		list($name, $value) = Form\Core\Helpers::GetSessionCsrf($this->Id);
		return (object) array('name' => $name, 'value' => $value);
	}
	/**
	 * Check cross site request forgery sended tokens from user with session tokens.
	 * If tokens are diferent, add form error and process csrf error handlers queue.
	 * @param array $rawRequestParams
	 * @return bool
	 */
	public function ValidateCsrf ($rawRequestParams = array()) {
		$result = FALSE;
		include_once('Form/Core/Helpers.php');
		$sessionCsrf = Form\Core\Helpers::GetSessionCsrf($this->Id);
		list($name, $value) = $sessionCsrf ? $sessionCsrf : array(NULL, NULL);
		if (!is_null($name) && !is_null($value)) {
			if (isset($rawRequestParams[$name]) && $rawRequestParams[$name] === $value) {
				$result = TRUE;
			}
		}
		if (!$result) {
			$errorMsg = Form::$DefaultMessages[Form::CSRF];
			if ($this->Translate) {
				$errorMsg = call_user_func($this->Translator, $errorMsg);
			}
			$this->AddError($errorMsg);
			foreach (static::$csrfErrorHandlers as $handler) {
				if (is_callable($handler)) {
					$handler($this, $errorMsg);
				}
			}
		}
		return $result;
	}
	/**
	 * Create new fresh cross site request forgery tokens,
	 * store them into session under $form->Id and return them.
	 * @return string[]
	 */
	public function SetUpCsrf () {
		$requestPath = $this->getRequestPath();
		$randomHash = bin2hex(openssl_random_pseudo_bytes(32));
		$nowTime = (string)time();
		$name = '____'.sha1($this->Id . $requestPath . 'name' . $nowTime . $randomHash);
		$value = sha1($this->Id . $requestPath . 'value' . $nowTime . $randomHash);
		include_once('Form/Core/Helpers.php');
		Form\Core\Helpers::SetSessionCsrf($this->Id, array($name, $value));
		return array($name, $value);
	}
	/**
	 * Get request path with protocol, domain, port, part but without any possible query string.
	 * @return string
	 */
	protected function getRequestPath () {
		$requestUri = $_SERVER['REQUEST_URI'];
		$lastQuestionMark = mb_strpos($requestUri, '?');
		if ($lastQuestionMark !== FALSE) $requestUri = mb_substr($requestUri, 0, $lastQuestionMark);
		$protocol = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https:' : 'http:';
		return $protocol . '//' . $_SERVER['HTTP_HOST'] . $requestUri;
	}
}
