<?php

require_once('/../../SimpleForm.php');
require_once('Base.php');
require_once('Zend/Session/Namespace.php');

class SimpleForm_Core_Helpers
{
	public static $controllerViewGetters = array('GetView', 'getView');
	public static $controllerViewProperties = array('View', 'view', 'Template', 'template');
	protected static $sessionData = NULL;
	protected static $sessionErrors = NULL;
	protected static $yearSeconds = 32872500; // (60 * 60 * 24 * 365.25);
	/* session ***********************************************************************/
	public static function GetControllerView (& $controller) {
		$result = NULL;
		$found = FALSE;
		foreach (self::$controllerViewGetters as $viewGetter) {
			if (method_exists($controller, $viewGetter)) {
				$result = $controller->$viewGetter();
				$found = TRUE;
				break;
			}
		}
		if (!$found) {
			foreach (self::$controllerViewProperties as $viewProperty) {
				if (property_exists($controller, $viewProperty)) {
					$result = $controller->$viewProperty;
					break;
				}
			}
		}
		return $result;
	}
	public static function GetSessionData ($formId = '') {
		$sessionData = & self::_getSessionData();
		if ($formId && isset($sessionData->$formId)) {
			$rawResult = $sessionData->$formId;
			return $rawResult;
		} else {
			return array();
		}
	}
    public static function GetSessionErrors ($formId = '') {
		$sessionErrors = & self::_getSessionErrors();
		if ($formId && isset($sessionErrors->$formId)) {
			$rawResult = $sessionErrors->$formId;
			return $rawResult;
		} else {
			return array();
		}
	}
	public static function SetSessionData ($formId = '', $data = array()) {
		$sessionData = & self::_getSessionData();
		if ($formId) $sessionData->$formId = $data;
	}
	public static function SetSessionErrors ($formId = '', $errors = array()) {
		$sessionErrors = & self::_getSessionErrors();
		if ($formId) $sessionErrors->$formId = $errors;
	}
	private static function & _getSessionData () {
		if (self::$sessionData == null) {
			self::$sessionData = new Zend_Session_Namespace('SimpleForm_Data');
			self::$sessionData->setExpirationSeconds(self::$yearSeconds);
		}
		return self::$sessionData;
	}
	private static function & _getSessionErrors () {
		if (self::$sessionErrors == null) {
			self::$sessionErrors = new Zend_Session_Namespace('SimpleForm_Errors');
			// do not use this, because all page elements should be requested throw php script in MvcCore package, including all assets
			// self::$sessionErrors->SetExpirationHoops(1);
			self::$sessionErrors->setExpirationSeconds(self::$yearSeconds);
		}
		return self::$sessionErrors;
	}
	/* common helpers ********************************************************************/
	public static function ValidateMaxPostSizeIfNecessary(SimpleForm & $form) {
		if (strtolower($form->Method) != 'post') return;
		$maxSize = ini_get('post_max_size');
		if (empty($_SERVER['CONTENT_LENGTH'])) {
			$form->AddError(
				sprintf(SimpleForm_Core_Base::$DefaultMessages[SimpleForm_Core_Base::EMPTY_CONTENT], $maxSize)
			);
			$form->Result = SimpleForm::RESULT_ERRORS;
		}
		$units = array('k' => 10, 'm' => 20, 'g' => 30);
		if (isset($units[$ch = strtolower(substr($maxSize, -1))])) {
			$maxSize <<= $units[$ch];
		}
		if ($maxSize > 0 && isset($_SERVER['CONTENT_LENGTH']) && $maxSize < $_SERVER['CONTENT_LENGTH']) {
			$form->AddError(
				sprintf(SimpleForm_Core_Base::$DefaultMessages[SimpleForm_Core_Base::MAX_POST_SIZE], $maxSize)
			);
			$form->Result = SimpleForm::RESULT_ERRORS;
		}
	}
}
