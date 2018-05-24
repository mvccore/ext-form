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
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');
require_once('Date.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class Time extends Date
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Forms\IField & $field) {
		$submitValue = trim($submitValue);
		$safeValue = preg_replace("#[^0-9\:]#", '', $submitValue);
		// http://stackoverflow.com/questions/11296536/regex-for-time-validation
		@preg_match("#([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?#", $safeValue, $matches);
		if (!$matches || mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError($field, Form::$DefaultMessages[Form::TIME], function ($msg, $args) {
				return Core\View::Format($msg, $args);
			});
		} else {
			$this->checkMinMax($field, $safeValue, Form::TIME_TO_LOW, Form::TIME_TO_HIGH);
		}
		return $safeValue;
	}
}
