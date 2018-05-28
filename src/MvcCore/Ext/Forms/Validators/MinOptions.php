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
require_once('ValueInOptions.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class MinOptions extends ValueInOptions
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Forms\IField & $field) {
		$safeValue = is_array($submitValue) ? $submitValue : array();
		$safeValueCount = count($safeValue);
		// check if there is enough options checked
		if ($field->MinSelectedOptionsCount > 0 && $safeValueCount < $field->MinSelectedOptionsCount) {
			$this->addError(
				$field,
				Form::$DefaultMessages[Form::CHOOSE_MIN_OPTS],
				function ($msg, $args) use (& $field) {
					$args[] = $field->MinSelectedOptionsCount;
					return Core\View::Format($msg, $args);
				}
			);
		}
		return $safeValue;
	}
}
