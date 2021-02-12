<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Field;

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field (mostly 
 * configurable) properties getter methods.
 */
interface IConstants {

	/**
	 * Render label HTML element on the left side from field HTML element.
	 * @var string
	 */
	const LABEL_SIDE_LEFT = 'left';

	/**
	 * Render label HTML element on the right side from field HTML element.
	 * @var string
	 */
	const LABEL_SIDE_RIGHT = 'right';
	
	/**
	 * Constants used internally and mostly
	 * in field autofocus setter to define additional
	 * behaviour for possible duplicate field focus.
	 * @var int
	 */
	const	AUTOFOCUS_DUPLICITY_EXCEPTION = 0,
			AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW = 1,
			AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW = -1;

	
	/**
	 * Validator instance method context in current form class.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_FORM			= 1;
	
	/**
	 * Validator static method context in current form class.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_FORM_STATIC		= 2;
	
	/**
	 * Validator instance method context in parent controller class of current form.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_CTRL			= 4;
	
	/**
	 * Validator static method context in parent controller class of current form.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_CTRL_STATIC		= 8;
	
	/**
	 * Validator instance method context in current model instance of current model form.
	 * For this value you need to install extension `mvccore/ext-model-form` and use features for model forms.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_MODEL			= 16;
	
	/**
	 * Validator static method context in current model instance of current model form.
	 * For this value you need to install extension `mvccore/ext-model-form` and use features for model forms.
	 * @var int
	 */
	const VALIDATOR_CONTEXT_MODEL_STATIC	= 32;

}