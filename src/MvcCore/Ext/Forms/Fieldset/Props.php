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

namespace MvcCore\Ext\Forms\Fieldset;

/**
 * @mixin \MvcCore\Ext\Forms\Fieldset
 */
trait Props {
	
	/************************************************************************************************
	*                                    Configurable Properties                                    *
	************************************************************************************************/

	/**
	 * Form fieldset specific name, used to identify fields in fielsets for rendering.
	 * This value is required for all form fieldsets.
	 * @requires
	 * @var string|NULL
	 */
	protected $name = NULL;
	
	/**
	 * Fixed fieldset order number, `NULL` by default.
	 * @var int|NULL
	 */
	protected $fieldOrder = NULL;
	
	/**
	 * Form fieldset `<legend>` tag content, it could contains HTML code, default `NULL`.
	 * @var string|NULL
	 */
	protected $legend = NULL;
	
	/**
	 * Boolean to translate legend text, `TRUE` by default.
	 * @var bool
	 */
	protected $translateLegend = TRUE;
	
	/**
	 * Form fieldset `disabled` boolean attribute, default `FALSE`.
	 * Browsers render all fields disabled in `<fieldset>` with disabled attribute.
	 * @var bool
	 */
	protected $disabled = FALSE;
	
	/**
	 * Form fieldset HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @var \string[]
	 */
	protected $cssClasses = [];
	
	/**
	 * Fieldset title, global HTML attribute, optional.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-title
	 * @var string|NULL
	 */
	protected $title = NULL;

	/**
	 * Boolean to translate title text, `TRUE` by default.
	 * @var boolean
	 */
	protected $translateTitle = TRUE;

	/**
	 * Collection with fieldset HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `name`, `disabled`, `class` or `title` ...
	 * Those attributes has it's own configurable properties by setter methods or 
	 * by constructor config array. Default value is an empty array to not  render 
	 * any additional attributes.
	 * @var array
	 */
	protected $controlAttrs = [];

	/**
	 * 
	 * @var \MvcCore\Ext\Forms\Field[]
	 */
	protected $fields = [];
	
	/**
	 * 
	 * @var \MvcCore\Ext\Forms\Fieldset[]
	 */
	protected $fieldsets = [];
	

	/************************************************************************************************
	 *                                     Internal Properties                                      *
	 ************************************************************************************************/

	/**
	 * Internal reference to form instance, where current fieldset is added.
	 * @internal
	 * @var \MvcCore\Ext\Form
	 */
	protected $form = NULL;

	/**
	 * Protected properties, which is not possible
	 * to configure through field constructor config array.
	 * @internal
	 * @var \string[]
	 */
	protected static $declaredProtectedProperties = [
		'form'
	];
}
