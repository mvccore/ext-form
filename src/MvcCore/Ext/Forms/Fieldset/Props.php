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
	 * Form fieldset specific name, used to identify 
	 * fieldset between each other and between fields.
	 * This value is required and it's used mostly internally.
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
	 * Form fieldset `<legend>` tag content, it could 
	 * contains HTML code, default `NULL`.
	 * Allowed HTML tags are container in constant:
	 * `\MvcCore\Ext\Forms\IFielset::ALLOWED_LEGEND_ELEMENTS`.
	 * @var string|NULL
	 */
	protected $legend = NULL;
	
	/**
	 * Boolean to translate legend text, `TRUE` by default.
	 * @var bool|NULL
	 */
	protected $translateLegend = NULL;
	
	/**
	 * Form fieldset `disabled` boolean attribute, 
	 * default `FALSE`. Browsers render all fields 
	 * disabled in `<fieldset>` with disabled attribute.
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
	 * @var bool|NULL
	 */
	protected $translateTitle = NULL;

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
	 * Form content rendering mode (only inside fieldset), configuration how errors, 
	 * labels, constrols and submit buttons will be rendered - with or without 
	 * any structural HTML elements like `<div>` or `<table>` elements.
	 * Default value is to render form content with `<div>` elements structure.
	 * This value could be uset to change form rendering mode only inside fieldset,
	 * not in whole form. If this `value` is not configured, it's used form settings.
	 * @var int|NULL
	 */
	protected $formRenderMode = NULL;

	/**
	 * Parent fieldset if any, fieldset could be inside another.
	 * If there is no parent fieldset and the fieldset is directly 
	 * in form, then this value is `NULL`.
	 * @var \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	protected $parentFieldset = NULL;

	/**
	 * Fields contained only inside this fieldset. Not contained in 
	 * whole form and not contained inside nested fields.
	 * @var \MvcCore\Ext\Forms\Field[]
	 */
	protected $fields = [];
	
	/**
	 * Fieldsets contained only inside this fieldset. Not contained in 
	 * whole form and not contained inside nested fieldsets.
	 * @var \MvcCore\Ext\Forms\Fieldset[]
	 */
	protected $fieldsets = [];

	/**
	 * Fieldset template for natural rendering (not customized with `*.phtml` view).
	 * Default value: `<fieldset name={name}{attrs}>{legend}{content}</fieldset>`.
	 * @var string
	 */
	protected $template = '<fieldset name="{name}"{attrs}>{legend}{content}</fieldset>';
	

	/************************************************************************************************
	 *                                     Internal Properties                                      *
	 ************************************************************************************************/
	
	/**
	 * Content objects hierarchy for rendering fields and fielsets.
	 * @internal
	 * @var \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	protected $children = [];

	/**
	 * Internal fields and fieldsets order to render and validate fields.
	 * @internal
	 * @var array|\stdClass
	 */
	protected $sorting = [
		'sorted'	=> FALSE,
		'numbered'	=> [],
		'naturally'	=> [],
	];

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
		'form', 'sorting', 'children'
	];
}
