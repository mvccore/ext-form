<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once(__DIR__.'/../../SimpleForm.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');

class SimpleForm_Validators_NumberField extends SimpleForm_Core_Validator
{
	public function Validate ($submitValue, $fieldName, SimpleForm_Core_Field & $field) {
		$submitValue = trim((string) $submitValue);
		$intValueStr = preg_replace("#[^0-9]#", '', $submitValue);
		$floatValueStr = preg_replace("#[^0-9\.]#", '', str_replace(',','.',$submitValue));
		$errorMsgKeyCommon = '';
		$errorMsgKey = '';
		if (strlen($intValueStr) === 0) {
			if ($field->Required) $errorMsgKey = SimpleForm::NUMBER;
			$safeValue = '';
		} else {
			if ($floatValueStr === $intValueStr) {
				$safeValue = intval($intValueStr);
				$errorMsgKeyCommon = SimpleForm::INTEGER;
			} else {
				$safeValue = floatval($intValueStr);
				$errorMsgKeyCommon = SimpleForm::FLOAT;
			}
			$errorMsgKey = '';
			if (isset($this->Min) && !is_null($field->Min)) {
				if ($safeValue < $field->Min) {
					$errorMsgKey = !is_null($this->Max) ? SimpleForm::RANGE : SimpleForm::GREATER;
				}
			}
			if (isset($this->Max) && !is_null($this->Max)) {
				if ($safeValue > $field->Max) {
					$errorMsgKey = !is_null($this->Min) ? SimpleForm::RANGE : SimpleForm::LOWER;
				}
			}
			if (isset($this->Pattern) && !is_null($this->Pattern)) {
				preg_match("#^".$this->Pattern."$#", (string)$safeValue, $matches);
				if (!$matches) {
					$errorMsgKey = $errorMsgKeyCommon;
				}
			}
		}
		if (mb_strlen($safeValue) !== mb_strlen($submitValue) || $errorMsgKey) {
			$errorMsgKey = $errorMsgKey ? $errorMsgKey : $errorMsgKeyCommon ;
			
			$errorReplacements = array();
			if ($errorMsgKey == SimpleForm::RANGE) {
				$errorReplacements[] = $field->Min;
				$errorReplacements[] = $field->Max;
			} else if ($errorMsgKey == SimpleForm::GREATER) {
				$errorReplacements[] = $field->Min;
			} else if ($errorMsgKey == SimpleForm::LOWER) {
				$errorReplacements[] = $field->Max;
			}

			$this->addError(
				$field,
				SimpleForm::$DefaultMessages[$errorMsgKey],
				function ($msg, $args) use (& $errorReplacements) {
					$args = array_merge($args, $errorReplacements);
					return SimpleForm_Core_View::Format($msg, $args);
				}
			);
		}
		return $safeValue;
	}
}
