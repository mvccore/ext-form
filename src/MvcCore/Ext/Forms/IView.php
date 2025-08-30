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

namespace MvcCore\Ext\Forms;

interface IView {

	/**
	 * Get controller instance as reference.
	 * @return \MvcCore\View
	 */
	public function GetView ();

	/**
	 * Set controller and it's view instance.
	 * @param  \MvcCore\View $view
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetView (\MvcCore\IView $view);

	/**
	 * Get form instance to render.
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm ();

	/**
	 * Set form instance to render.
	 * @param  \MvcCore\Ext\Form $form
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetForm (\MvcCore\Ext\IForm $form);

	/**
	 * Get rendered field.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function GetField ();

	/**
	 * Set rendered field.
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetField (/*\MvcCore\Ext\Forms\IField*/ $field = NULL);
	
	/**
	 * Get rendered fieldset.
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function GetFieldset ();

	/**
	 * Set rendered fieldset.
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetFieldset (/*\MvcCore\Ext\Forms\IFieldset*/ $fieldset = NULL);

	/**
	 * Get form fields or fieldsets to render in current form/fieldset level.
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ();

	/**
	 * Set form fields or fieldsets to render in current form/fieldset level.
	 * @param  \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[] $children
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetChildren (array $children);

	/**
	 * Get any value by given name existing in local store. If there is no value
	 * in local store by given name, try to get result value into store by
	 * field reflection class from field instance property if view is used for
	 * field rendering. If there is still no value found, try to get result value
	 * into store by form reflection class from form instance property and if
	 * still no value found, try to get result value from local view instance
	 * `__get()` method.
	 * @param  string $name
	 * @return mixed
	 */
	public function & __get ($name);

	/**
	 * Get `TRUE` by given name existing in local store. If there is no value
	 * in local store by given name, try to get result value into store by
	 * field reflection class from field instance property if view is used for
	 * field rendering. If there is still no value found, try to get result value
	 * into store by form reflection class from form instance property and if
	 * still no value found, try to get result value from local view instance
	 * `__get()` method.
	 * @param  string $name
	 * @return bool
	 */
	public function __isset ($name);

	/**
	 * Call public field method if exists in field instance and view is used for
	 * field rendering or call public form method if exists in form instance or
	 * try to call view helper by parent `__call()` method.
	 * @param  string $method
	 * @param  mixed  $arguments
	 * @return mixed
	 */
	public function __call ($method, $arguments);

	/**
	 * Render configured form template.
	 * @return string
	 */
	public function RenderTemplate ();

	/**
	 * Render form naturally by cycles inside php scripts.
	 * All form fields will be rendered inside empty <div> elements.
	 * @return string
	 */
	public function RenderNaturally ();

	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with CSRF tokens.
	 * @return string
	 */
	public function RenderBegin ();

	/**
	 * Render hidden input with CSRF tokens.
	 * This method is not necessary to call, it's
	 * called internally by `$form->View->RenderBegin();`.
	 * 
	 * This function is deprecated but still possible to use
	 * for maximum compatibility. New solution is to enable 
	 * global CSRF protection by http cookie in `Bootstrap.php`:
	 * ```
	 * \MvcCore\Application::GetInstance()->SetSecurityProtection(
	 *     \MvcCore\IApplication::SECURITY_PROTECTION_COOKIE
	 * );
	 * ```
	 * @return string
	 */
	public function RenderCsrf ();

	/**
	 * Return current CSRF (Cross Site Request Forgery) hidden
	 * input name and it's value as `\stdClass`.
	 * Result `\stdClass` has keys: `name` and `value`.
	 * 
	 * This function is deprecated but still possible to use
	 * for maximum compatibility. New solution is to enable 
	 * global CSRF protection by http cookie in `Bootstrap.php`:
	 * ```
	 * \MvcCore\Application::GetInstance()->SetSecurityProtection(
	 *     \MvcCore\IApplication::SECURITY_PROTECTION_COOKIE
	 * );
	 * ```
	 * @throws \Exception
	 * @return \stdClass
	 */
	public function GetCsrf ();

	/**
	 * Render form errors for current form/fielset level and
	 * form/fielset children controls. If there is configured
	 * table form rendering mode, render all hidden fields at 
	 * the beginning.
	 * @return string
	 */
	public function RenderErrorsAndContent ();

	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into `div.errors` with `div.error` elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors ();

	/**
	 * Render form content - form fields.
	 * Go through all `$form->fields` and call `$field->Render();` on every field
	 * and put it into an empty `<div>` element. Render each field in full possible
	 * way - naturally by label configuration with possible errors configured beside
	 * or with custom field template.
	 * @return string
	 */
	public function RenderContent ();

	/**
	 * Return array with separated content fields groups.
	 * First result item is array with all form hidden fields except CSRF token field.
	 * Second result item is array with all form fields except submit buttons.
	 * Third result item is array with all form submit buttons.
	 * @return \array[] [\MvcCore\Ext\Forms\Fields\Hidden[], \MvcCore\Ext\Forms\Field[], \MvcCore\Ext\Forms\Fields\ISubmit[]]
	 */
	public function RenderContentGetFieldsGroups ();

	/**
	 * Render form content with `<div>` elements structure.
	 * @return string
	 */
	public function RenderContentWithDivStructure ();

	/**
	 * Render form content with `<table>` elements structure.
	 * @return string
	 */
	public function RenderContentWithTableStructure ();
	
	/**
	 * Render form content with no HTML elements structure.
	 * @return string
	 */
	public function RenderContentWithoutStructure ();

	/**
	 * Render form end.
	 * Render html closing `</form>` tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderEnd ();

	/**
	 * Format string function.
	 * @param string $str Template with replacements like `{0}`, `{1}`, `{anyStringKey}`...
	 * @param array $args Each value under it's index is replaced as
	 *                    string representation by replacement in form `{arrayKey}`
	 * @return string
	 */
	public static function Format ($str = '', array $args = []);

	/**
	 * Render content of html tag attributes by key/value array.
	 * @param  array                  $attributes
	 * @param  callable|\Closure|NULL $escapeFn
	 * @return string
	 */
	public static function RenderAttrs (array $attributes = [], $escapeFn = NULL);
}
