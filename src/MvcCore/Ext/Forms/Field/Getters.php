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

namespace MvcCore\Ext\Forms\Field;

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field (mostly 
 * configurable) properties getter methods.
 */
trait Getters
{
	/**
	 * Get form field HTML id attribute, completed from form name and field name.
	 * This value is completed automatically, but you can customize it.
	 * @return string|NULL
	 */
	public function GetId () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->id;
	}

	/**
	 * Get form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @return string|NULL
	 */
	public function GetName () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->name;
	}

	/**
	 * Get form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @return string|NULL
	 */
	public function GetType () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->type;
	}

	/**
	 * Get form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @return string|array|int|float|NULL
	 */
	public function GetValue () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->value;
	}

	/**
	 * Get form field HTML element css classes strings as array.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->cssClasses;
	}

	/**
	 * Get field title, global HTML attribute, optional.
	 * @return string|NULL
	 */
	public function GetTitle () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->title;
	}

	/**
	 * Get collection with field HTML element 
	 * additional attributes by array keys/values.
	 * There are no system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable 
	 * properties with it's own getters.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * Default value is an empty array to not 
	 * render any additional attributes.
	 * @return array
	 */
	public function & GetControlAttrs () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->controlAttrs;
	}

	/**
	 * Get field HTML element additional attribute 
	 * by attribute name and value.
	 * There are no system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable 
	 * properties with it's own getters.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * Get list of predefined validator classes ending names or validator instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every validator class (ending name) or validator instance has to 
	 * implement interface `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * @return \string[]|\MvcCore\Ext\Forms\IValidator[]
	 */
	public function & GetValidators () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->validators;
	}

	/**
	 * Get `TRUE`, if field has configured in it's validators array
	 * given validator class ending name or validator instance.
	 * @param string|\MvcCore\Ext\Forms\IValidator $validatorNameOrInstance
	 * @return bool
	 */
	public function HasValidator ($validatorNameOrInstance) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		if (is_string($validatorNameOrInstance)) {
			$validatorClassName = $validatorNameOrInstance;
		} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
			$validatorClassName = get_class($validatorNameOrInstance);
		} else {
			return $this->throwNewInvalidArgumentException(
				'Unknown validator type given: `' . $validatorNameOrInstance 
				. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
			);
		}
		$slashPos = strrpos($validatorClassName, '\\');
		$validatorName = $slashPos !== FALSE 
			? substr($validatorClassName, $slashPos + 1)
			: $validatorClassName;
		return isset($this->validators[$validatorName]);
	}

	/**
	 * Get boolean `TRUE` or string with template relative path 
	 * without `.phtml` or `.php` extension to render 
	 * field by any custom template. 
	 * 
	 * If `TRUE`, path to template is always completed by configured 
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);`
	 * value, which is `/App/Views/Forms/Fields` by default.
	 * 
	 * If returned any string with relative path, path is always relative from configured
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, which is again 
	 * `/App/Views/Forms/Fields` by default.
	 * 
	 * `FALSE` or `NULL` (`NULL` is default) is returned to render field naturally.
	 * 
	 * Example:
	 * ```
	 * // Render field template prepared in:
	 * // '/App/Views/Forms/Fields/my-specials/my-field-type.phtml':
	 * 
	 * \MvcCore\Ext\Forms\View::GetFieldsDir(); // returned by default: 'Forms/Fields'
	 * $field->GetViewScript(); // returned 'my-specials/my-field-type'
	 * 
	 * // Or the same by:
	 * \MvcCore\Ext\Forms\View::GetFieldsDir(); // returned 'Forms/Fields/my-specials'
	 * $field->GetType(); // returned 'my-field-type'
	 * $field->GetViewScript(); // returned TRUE
	 * ```
	 * @return bool|string|NULL
	 */
	public function GetViewScript () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->viewScript;
	}

	/**
	 * Get supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->SetJsSupportingFile(...)` to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetJsClassName () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->jsClassName;
	}

	/**
	 * Get field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetJsSupportingFile () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->jsSupportingFile;
	}

	/**
	 * Get field supporting css file relative path.
	 * If you want to use any custom supporting css file 
	 * for any additional purposes for your custom field, you need to 
	 * define path to your css file relatively from configured 
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting css file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your 
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetCssSupportingFile () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->cssSupportingFile;
	}

	/**
	 * Get boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @var bool
	 */
	public function GetTranslate () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->translate;
	}

	/**
	 * Get fields (and labels) default templates 
	 * for natural (not customized) field rendering.
	 * @return array
	 */
	public static function & GetTemplates () {
		return (array) static::$templates;
	}
}
