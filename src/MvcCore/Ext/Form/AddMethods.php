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
		$fieldNamesArr = $fieldNames === NULL ? array() : (gettype($fieldNames) == 'array' ? $fieldNames : array($fieldNames));
		$newErrorRec = array(strip_tags($errorMsgUtf8), $fieldNamesArr);
		if ($fieldNamesArr) {
			foreach ($fieldNamesArr as $fieldName) {
				if (isset($this->fields[$fieldName])) {
					$field = & $this->fields[$fieldName];
					$field
						->AddError($errorMsgUtf8)
						->AddCssClass('error');
					if ($field instanceof \MvcCore\Ext\Forms\IFieldGroup)
						$field->AddGroupCssClass('error');
				}
			}
		}
		$this->errors[] = $newErrorRec;
		$this->result = \MvcCore\Ext\Forms\IForm::RESULT_ERRORS;
		return $this;
	}

	/**
	 * Add supporting javascript file.
	 * @param string $jsRelativePath	Supporting javascript file relative path from protected `$form->jsAssetsRootDir`.
	 * @param string $jsClassName		Supporting javascript full class name inside supporting file.
	 * @param array  $constructorParams	Supporting javascript constructor params.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddJsSupportFile (
		$jsRelativePath = '/fields/custom-type.js', 
		$jsClassName = 'MvcCoreForm.FieldType', 
		$constructorParams = array()
	) {
		$this->jsSupportFiles[] = array($jsRelativePath, $jsClassName, $constructorParams);
		return $this;
	}

	/**
	 * Add supporting css file.
	 * @param string $cssRelativePath Supporting css file relative path from protected `$form->cssAssetsRootDir`.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddCssSupportFile (
		$cssRelativePath = '/fields/custom-type.css'
	) {
		$this->cssSupportFile[] = array($cssRelativePath);
		return $this;
	}

	/**
	 * Add CSRF (Cross Site Request Forgery) error handler.
	 * If CSRF submit comparation fails, it's automaticly processed
	 * queue with this handlers, you can put here for example handler
	 * to deauthenticate your user or anything else to more secure your application.
	 * Params in `callable` should be two with following types:
	 *	- `\MvcCore\Ext\Form`	- Form instance where error happend.
	 *	- `\MvcCore\Request`	- Current request object.
	 *	- `string`				- Translated error meessage string.
	 * Example:
	 * `\MvcCore\Ext\Form::AddCsrfErrorHandler(function($form, $request, $errorMsg) {
	 *		// ... anything you want to do, for example to sign out user.
	 * });`
	 * @param callable $handler
	 * @param int|NULL $priorityIndex
	 * @return int New CSRF error handlers count.
	 */
	public static function AddCsrfErrorHandler (callable $handler, $priorityIndex = NULL) {
		if (!is_callable($handler)) throw new \InvalidArgumentException(
			'['.__CLASS__.'] Given argument is not callable: `'.serialize($handler).'`.'
		);
		$reflection = new \ReflectionFunction($handler);
		$isClosure = $reflection->isClosure();
		if ($priorityIndex === NULL) {
			static::$csrfErrorHandlers[] = array($handler, $isClosure);
		} else {
			if (isset(static::$csrfErrorHandlers[$priorityIndex])) {
				array_splice(static::$csrfErrorHandlers, $priorityIndex, 0, array($handler, $isClosure));
			} else {
				static::$csrfErrorHandlers[$priorityIndex] = array($handler, $isClosure);
			}
		}
		return count(static::$csrfErrorHandlers);
	}

	/**
	 * Add form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * Validators namespaces array configured by default: `array('\\MvcCore\\Ext\\Forms\\Validators\\');`.
	 * @param \string[] $validatorsNamespaces,...
	 * @return int New validators namespaces count.
	 */
	public static function AddValidatorsNamespaces (/* ...$validatorsNamespaces */) {
		$validatorsNamespaces = func_get_args();
		foreach ($validatorsNamespaces as $validatorsNamespace)
			static::$validatorsNamespaces[] = '\\' . trim($validatorsNamespace, '\\') . '\\';
		return count(static::$validatorsNamespaces);
	}
}
