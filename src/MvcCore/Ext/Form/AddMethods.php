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
 * Trait for class `MvcCore\Ext\Form` containing adding methods for configurable 
 * properties except methods manipulating with form field instances, for those 
 * methods, there is another trait `\MvcCore\Ext\Form\FieldMethods`.
 * @mixin \MvcCore\Ext\Form
 */
trait AddMethods {

	/**
	 * @inheritDocs
	 * @param  string $charset 
	 * @return \MvcCore\Ext\Form
	 */
	public function AddAcceptCharset ($charset) {
		$this->acceptCharsets[] = $charset;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form
	 */
	public function AddCssClasses ($cssClasses) {
		$cssClassesArr = is_array($cssClasses)
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string            $errorMsg   Any error message, translated if necessary. All html tags from error message will be removed automatically.
	 * @param  string|array|NULL $fieldNames Optional, field name string or array with field names where error happened.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddError ($errorMsg, $fieldNames = NULL) {
		$errorMsgUtf8 = iconv(
			mb_detect_encoding($errorMsg, mb_detect_order(), TRUE),
			"UTF-8",
			$errorMsg
		);
		$fieldNamesArr = $fieldNames === NULL 
			? [] 
			: (is_array($fieldNames)
				? $fieldNames 
				: [$fieldNames]
			);
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
	 * @inheritDocs
	 * @param  string $jsRelativePath    Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 * @param  string $jsClassName       Supporting javascript full class name inside supporting file.
	 * @param  array  $constructorParams Supporting javascript constructor params.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddJsSupportFile (
		$jsRelativePath = '/fields/custom-type.js', 
		$jsClassName = 'MvcCoreForm.FieldType', 
		$constructorParams = []
	) {
		$this->jsSupportFiles[] = [$jsRelativePath, $jsClassName, $constructorParams];
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $cssRelativePath Supporting css file relative path from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @return \MvcCore\Ext\Form
	 */
	public function AddCssSupportFile (
		$cssRelativePath = '/fields/custom-type.css'
	) {
		$this->cssSupportFiles[] = [$cssRelativePath];
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  callable $handler
	 * @param  int|NULL $priorityIndex
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
	 * @inheritDocs
	 * @param  \string[] $validatorsNamespaces,...
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
