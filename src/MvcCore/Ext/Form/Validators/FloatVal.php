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

class FloatVal extends Core\Validator
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Form\Interfaces\IField & $field) {
		$submitValue = trim($submitValue);
		$floatValStr = preg_replace("#[^0-9\.,]#", '', $submitValue);
		$safeValue = (float) str_replace(",", '.', $floatValStr);
		if (mb_strlen($floatValStr) !== mb_strlen($submitValue)) {
			$this->addError($field, Form::$DefaultMessages[Form::FLOAT], function ($msg, $args) {
				return Core\View::Format($msg, $args);
			});
		}
		return $safeValue;
	}
}
