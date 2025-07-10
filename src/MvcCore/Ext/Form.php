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

namespace MvcCore\Ext;

/**
 * Responsibility: Main form class with all `<form>` configuration, attributes,
 *                 field instances and it's validators. To create any HTML form,
 *                 you need to instantiate this class, configure an id, action 
 *                 and more.
 */
class		Form 
extends		\MvcCore\Controller
implements	\MvcCore\Ext\IForm {

	/**
	 * MvcCore Extension - Form - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.3.3';

	/**
	 * Initial value after form has been instantiated.
	 * @var int
	 */
	const DISPATCH_STATE_CREATED			= 0;

	/**
	 * Value after executing the `Init()` method.
	 * @var int
	 */
	const DISPATCH_STATE_INITIALIZED		= 1;
	
	/**
	 * Value after executing the `PreDispatch()` method.
	 * @var int
	 */
	const DISPATCH_STATE_PRE_DISPATCHED		= 2;
	
	/**
	 * Value after executing the submit method.
	 * @var int
	 */
	const DISPATCH_STATE_SUBMITTED			= 6;
	
	/**
	 * Value after executing the `Render ()` method.
	 * @var int
	 */
	const DISPATCH_STATE_RENDERED			= 7;

	use \MvcCore\Ext\Form\InternalProps;
	use \MvcCore\Ext\Form\ConfigProps;
	use \MvcCore\Ext\Form\GetMethods;
	use \MvcCore\Ext\Form\SetMethods;
	use \MvcCore\Ext\Form\AddMethods;
	use \MvcCore\Ext\Form\FieldMethods;
	use \MvcCore\Ext\Form\FieldsetMethods;
	use \MvcCore\Ext\Form\Session;
	use \MvcCore\Ext\Form\Csrf;
	use \MvcCore\Ext\Form\Dispatching;
	use \MvcCore\Ext\Form\Submitting;
	use \MvcCore\Ext\Form\Rendering;
	use \MvcCore\Ext\Form\Assets;
	
}