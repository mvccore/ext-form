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

trait AddMethods
{
	/**
	 * Add css class (or classes separated by space) and add new value(s)
	 * after previous css class(es) attribute values. Value is used for
	 * standard css class attribute for HTML `<form>` tag.
	 * @param string $cssClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddCssClass ($cssClass = '') {
		$this->cssClass .= (strlen($this->cssClass) > 0 ? ' ' : '') . $cssClass;
		return $this;
	}

	/**
	 * Add form submit error and switch form result to zero - to error state.
	 * @param string $errorMsg Any error message, translated if necessary. All html tags from error message will be removed automaticly.
	 * @param string|array|NULL $fieldNames Optional, field name string or array with field names where error happend.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function AddError ($errorMsg, $fieldNames = NULL) {
		$errorMsgUtf8 = iconv(
			mb_detect_encoding($errorMsg, mb_detect_order(), TRUE),
			"UTF-8",
			$errorMsg
		);
		$newErrorRec = array(strip_tags($errorMsgUtf8));
		/** @var int $fieldNameType 0 - NULL, 1 - string, 2 - array */
		$fieldNameType = $fieldNames === NULL ? 0 : (gettype($fieldNames) == 'array' ? 2 : 1);
		if ($fieldNameType > 0) {
			$newErrorRec[] = $fieldNames;
			if ($fieldNameType === 1) {
				if (isset($this->fields[$fieldNames]))
					$this->fields[$fieldNames]->AddError($errorMsgUtf8);
			} else if ($fieldNameType === 2) {
				foreach ($fieldNames as $fieldName)
					if (isset($this->fields[$fieldName]))
						$this->fields[$fieldName]->AddError($errorMsgUtf8);
			}
			$this->errors[] = $newErrorRec;
		}
		$this->result = \MvcCore\Ext\Forms\IForm::RESULT_ERRORS;
		return $this;
	}

	/**
	 * Add CSRF (Cross Site Request Forgery) error handler.
	 * If CSRF submit comparation fails, it's automaticly processed
	 * queue with this handlers, you can put here for example handler
	 * to deauthenticate your user or anything else to more secure your application.
	 * @param callable $handler
	 * @param int|NULL $priorityIndex
	 * @return void
	 */
	public static function AddCsrfErrorHandler (callable $handler, $priorityIndex = NULL) {
		if ($priorityIndex !== NULL && is_numeric($priorityIndex)) {
			$index = intval($priorityIndex);
			static::$csrfErrorHandlers[$index] = $handler;
		} else {
			static::$csrfErrorHandlers[] = $handler;
		}
	}

	/**
	 * Add supporting javascript file.
	 * @param string $jsRelativePath	Supporting javascript file relative path from protected `$form->jsAssetsRootDir`.
	 * @param string $jsClassName		Supporting javascript full class name inside supporting file.
	 * @param array  $constructorParams	Supporting javascript constructor params.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddJsSupportFile ($jsRelativePath = '/fields/custom-type.js', $jsClassName = 'MvcCoreForm.FieldType', $constructorParams = array()) {
		$this->jsSupportFiles[] = array($jsRelativePath, $jsClassName, $constructorParams);
		return $this;
	}

	/**
	 * Add supporting css file.
	 * @param string $cssRelativePath Supporting css file relative path from protected `$form->cssAssetsRootDir`.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddCssSupportFile ($cssRelativePath = '/fields/custom-type.css') {
		$this->cssSupportFile[] = array($cssRelativePath);
		return $this;
	}
}
