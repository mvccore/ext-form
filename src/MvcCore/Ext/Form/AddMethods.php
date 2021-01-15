<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Form;

/**
 * Trait for class `MvcCore\Ext\Form` containing adding methods for configurable 
 * properties except methods manipulating with form field instances, for those 
 * methods, there is another trait `\MvcCore\Ext\Form\FieldMethods`.
 */
trait AddMethods {

	/**
	 * Add into list of character encodings that the server accepts. The 
	 * browser uses them in the order in which they are listed. The default 
	 * value,the reserved string `'UNKNOWN'`, indicates the same encoding 
	 * as that of the document containing the form element.
	 * @param string $charset 
	 * @return \MvcCore\Ext\Form
	 */
	public function AddAcceptCharset ($charset) {
		/** @var $this \MvcCore\Ext\Form */
		$this->acceptCharsets[] = $charset;
		return $this;
	}

	/**
	 * Add css classes strings for HTML element attribute `class`.
	 * Given css classes will be added after previously defined css classes.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form
	 */
	public function AddCssClasses ($cssClasses) {
		/** @var $this \MvcCore\Ext\Form */
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}

	/**
	 * Add form submit error and switch form result to zero - to error state.
	 * @param string $errorMsg Any error message, translated if necessary. All html tags from error message will be removed automatically.
	 * @param string|array|NULL $fieldNames Optional, field name string or array with field names where error happened.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddError ($errorMsg, $fieldNames = NULL) {
		/** @var $this \MvcCore\Ext\Form */
		$errorMsgUtf8 = iconv(
			mb_detect_encoding($errorMsg, mb_detect_order(), TRUE),
			"UTF-8",
			$errorMsg
		);
		$fieldNamesArr = $fieldNames === NULL ? [] : (gettype($fieldNames) == 'array' ? $fieldNames : [$fieldNames]);
		$newErrorRec = [$errorMsgUtf8, $fieldNamesArr];
		if ($fieldNamesArr) {
			foreach ($fieldNamesArr as $fieldName) {
				if (isset($this->fields[$fieldName])) {
					$field = & $this->fields[$fieldName];
					$field
						->AddError($errorMsgUtf8)
						->AddCssClasses('error');
					if ($field instanceof \MvcCore\Ext\Forms\IFieldsGroup)
						$field->AddGroupLabelCssClasses('error');
				}
			}
		}
		$this->errors[] = $newErrorRec;
		$this->result = \MvcCore\Ext\IForm::RESULT_ERRORS;
		return $this;
	}

	/**
	 * Add supporting javascript file.
	 * @param string $jsRelativePath	Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 * @param string $jsClassName		Supporting javascript full class name inside supporting file.
	 * @param array  $constructorParams	Supporting javascript constructor params.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddJsSupportFile (
		$jsRelativePath = '/fields/custom-type.js', 
		$jsClassName = 'MvcCoreForm.FieldType', 
		$constructorParams = []
	) {
		/** @var $this \MvcCore\Ext\Form */
		$this->jsSupportFiles[] = [$jsRelativePath, $jsClassName, $constructorParams];
		return $this;
	}

	/**
	 * Add supporting css file.
	 * @param string $cssRelativePath Supporting css file relative path from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddCssSupportFile (
		$cssRelativePath = '/fields/custom-type.css'
	) {
		/** @var $this \MvcCore\Ext\Form */
		$this->cssSupportFiles[] = [$cssRelativePath];
		return $this;
	}

	/**
	 * Add CSRF (Cross Site Request Forgery) error handler.
	 * If CSRF submit comparison fails, it's automatically processed
	 * queue with this handlers, you can put here for example handler
	 * to de-authenticate your user or anything else to more secure your application.
	 * Params in `callable` should be two with following types:
	 *	- `\MvcCore\Ext\Form`	- Form instance where error happened.
	 *	- `\MvcCore\Request`	- Current request object.
	 *	- `\MvcCore\Response`	- Current response object.
	 *	- `string`				- Translated error message string.
	 * Example:
	 * `\MvcCore\Ext\Form::AddCsrfErrorHandler(function($form, $request, $response, $errorMsg) {
	 *		// ... anything you want to do, for example to sign out user.
	 * });`
	 * @param callable $handler
	 * @param int|NULL $priorityIndex
	 * @return int New CSRF error handlers count.
	 */
	public static function AddCsrfErrorHandler (callable $handler, $priorityIndex = NULL) {
		if (!is_callable($handler)) 
			throw new \InvalidArgumentException(
				'['.get_class().'] Given argument is not callable: `'.serialize($handler).'`.'
			);
		if (is_array($handler) || (is_string($handler) && mb_strpos($handler, '::') !== FALSE)) {
			$isClosure = FALSE;
		} else {
			$reflection = new \ReflectionFunction($handler);
			$isClosure = $reflection->isClosure();
		}
		if ($priorityIndex === NULL) {
			static::$csrfErrorHandlers[] = [$handler, $isClosure];
		} else {
			if (isset(static::$csrfErrorHandlers[$priorityIndex])) {
				array_splice(static::$csrfErrorHandlers, $priorityIndex, 0, [$handler, $isClosure]);
			} else {
				static::$csrfErrorHandlers[$priorityIndex] = [$handler, $isClosure];
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
	public static function AddValidatorsNamespaces ($validatorsNamespaces) {
		$validatorsNamespaces = func_get_args();
		if (count($validatorsNamespaces) === 1 && is_array($validatorsNamespaces[0])) 
			$validatorsNamespaces = $validatorsNamespaces[0];
		foreach ($validatorsNamespaces as $validatorsNamespace)
			static::$validatorsNamespaces[] = '\\' . trim($validatorsNamespace, '\\') . '\\';
		return count(static::$validatorsNamespaces);
	}
}
