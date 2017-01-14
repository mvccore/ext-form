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

require_once('Date.php');

class SimpleForm_Time extends SimpleForm_Date
{
	public $Type = 'time';
	public $Format = 'H:i';
	public $Validators = array('Time');
	// Min and Max shoud be set as strings in 24 hours format like 8:00, 22:00
}
