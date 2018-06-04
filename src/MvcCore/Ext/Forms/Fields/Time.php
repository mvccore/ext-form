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

namespace MvcCore\Ext\Forms\Fields;

require_once('Date.php');

class Time extends Date
{
	protected $type = 'time';

	protected $format = 'H:i';

	protected $validators = ['Time'];

	// Min and Max shoud be set as strings in 24 hours format like 8:00, 22:00
}
