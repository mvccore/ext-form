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

namespace MvcCore\Ext\Form\Validators;

require_once(__DIR__.'/../../Form.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class Pattern extends Core\Validator
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field) {
		$safeValue = '';
		$submitValue = trim($submitValue);
		if (isset($field->Pattern) && !is_null($field->Pattern)) {
			$pattern = $field->Pattern;
			if (mb_strpos($pattern, "#") !== 0) {
				$pattern = "#" . $pattern . "#";
			}
			preg_match($pattern, $submitValue, $matches);
			if ($matches) {
				$safeValue = $submitValue;
			}
		} else {
			$safeValue = $submitValue;
		}
		if (mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError(
				$field,
				Form::$DefaultMessages[Form::INVALID_FORMAT],
				function ($msg, $args) use (& $field) {
					$args[] = $field->Pattern;
					return Core\View::Format($msg, $args);
				}
			);
		}
		return $safeValue;
	}
}
