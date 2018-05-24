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

require_once('Core/FieldGroup.php');

class RadioGroup extends Core\FieldGroup
{
	public $Type = 'radio';
	public $Value = '';
	public $Validators = array('ValueInOptions');
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$Templates = (object) array_merge((array)parent::$Templates, (array)self::$Templates);
	}
}
