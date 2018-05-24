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

namespace MvcCore\Ext\Forms\Validators;

require_once(__DIR__.'/../../Form.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class NumberField extends Core\Validator
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Forms\IField & $field) {
		$submitValue = trim((string) $submitValue);
		$intValueStr = preg_replace("#[^0-9]#", '', $submitValue);
		$floatValueStr = preg_replace("#[^0-9\.]#", '', str_replace(',','.',$submitValue));
		$errorMsgKeyCommon = '';
		$errorMsgKey = '';
		if (strlen($intValueStr) === 0) {
			if ($field->Required) $errorMsgKey = Form::NUMBER;
			$safeValue = '';
		} else {
			if ($floatValueStr === $intValueStr) {
				$safeValue = intval($intValueStr);
				$errorMsgKeyCommon = Form::INTEGER;
			} else {
				$safeValue = floatval($intValueStr);
				$errorMsgKeyCommon = Form::FLOAT;
			}
			$errorMsgKey = '';
			if (isset($this->Min) && !is_null($field->Min)) {
				if ($safeValue < $field->Min) {
					$errorMsgKey = !is_null($this->Max) ? Form::RANGE : Form::GREATER;
				}
			}
			if (isset($this->Max) && !is_null($this->Max)) {
				if ($safeValue > $field->Max) {
					$errorMsgKey = !is_null($this->Min) ? Form::RANGE : Form::LOWER;
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
			if ($errorMsgKey == Form::RANGE) {
				$errorReplacements[] = $field->Min;
				$errorReplacements[] = $field->Max;
			} else if ($errorMsgKey == Form::GREATER) {
				$errorReplacements[] = $field->Min;
			} else if ($errorMsgKey == Form::LOWER) {
				$errorReplacements[] = $field->Max;
			}

			$this->addError(
				$field,
				Form::$DefaultMessages[$errorMsgKey],
				function ($msg, $args) use (& $errorReplacements) {
					$args = array_merge($args, $errorReplacements);
					return Core\View::Format($msg, $args);
				}
			);
		}
		return $safeValue;
	}
}
