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

// TODO: hodně
class MinLength extends \MvcCore\Ext\Forms\Validator
{
	public function Validate ($rawSubmittedValue) {
		$submitValue = trim($submitValue);
		if (isset($field->Maxlength) && !is_null($field->Maxlength) && $field->Maxlength > 0) {
			$safeValue = mb_substr($submitValue, 0, $field->Maxlength);
		} else {
			$safeValue = $submitValue;
		}
		if (mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError(
				$field, 
				Form::$DefaultMessages[Form::MIN_LENGTH], 
				function ($msg, $args) use (& $field) {
					$args[] = $field->Maxlength;
					return Core\View::Format($msg, $args);
				}
			);
		}
		return $safeValue;
	}
}
