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

class Date extends Core\Validator
{
	public static $ErrorMessagesformatReplacements = array(
		'd' => 'dd',
		'j' => 'd',
		'D' => 'Mon-Sun',
		'l' => 'Monday-Sunday',
		'm' => 'mm',
		'n' => 'm',
		'M' => 'Jan-Dec',
		'F' => 'January-December',
		'Y' => 'yyyy',
		'y' => 'yy',
		'a' => 'am',
		'A' => 'pm',
		'g' => '1-12',
		'h' => '01-12',
		'G' => '01-12',
		'H' => '00-23',
		'i' => '00-59',
		's' => '00-59',
		'u' => '0-999999',
	);
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field) {
		$submitValue = trim($submitValue);

		$safeValue = preg_replace("#[^a-zA-Z0-9\:\.\-\,/ ]#", '', $submitValue);

		$dateObj = @\DateTime::createFromFormat($field->Format, $safeValue);

		if ($dateObj === FALSE || mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError($field, Form::$DefaultMessages[Form::DATE], function ($msg, $args) use (& $field) {
				$format = $args->Format;
				foreach (Date::$ErrorMessagesformatReplacements as $key => $value) {
					$format = str_replace($key, $value, $format);
				}
				$args[] = $format;
				return Core\View::Format($msg, $args);
			});
		} else {
			$this->checkMinMax($field, $safeValue, Form::DATE_TO_LOW, Form::DATE_TO_HIGH);
		}
		return $safeValue;
	}

	protected function checkMinMax (\MvcCore\Ext\Form\Core\Field & $field, $safeValue, $minErrorMsgKey, $maxErrorMsgKey) {
		$minSet = !is_null($field->Min);
		$maxSet = !is_null($field->Max);
		if ($minSet || $maxSet) {
			$date = \DateTime::createFromFormat($field->Format, $safeValue);
			if ($minSet) {
				$minDate = \DateTime::createFromFormat($field->Format, $field->Min);
				if ($date < $minDate) {
					$this->addError(
						$field,
						Form::$DefaultMessages[$minErrorMsgKey],
						function ($msg, $args) use (& $field) {
							$args[] = $field->Min;
							return Core\View::Format($msg, $args);
						}
					);
				}
			}
			if ($maxSet) {
				$maxDate = \DateTime::createFromFormat($field->Format, $field->Max);
				if ($date > $maxDate) {
					$this->addError(
						$field,
						Form::$DefaultMessages[$maxErrorMsgKey],
						function ($msg, $args) use (& $field) {
							$args[] = $field->Max;
							return Core\View::Format($msg, $args);
						}
					);
				}
			}
		}
	}
}
