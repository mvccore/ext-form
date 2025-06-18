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

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: define getters and setters for field property `options`.
 * Interface for classes:
 * - `\MvcCore\Ext\Forms\Fields\DataList`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 * - `\MvcCore\Ext\Forms\Validators\ValueInOptions`
 */
interface IOptions {

	/**
	 * Value defining that current field value is scalar type.
	 * @var int
	 */
	const VALUE_TYPE_SCALAR				= 0;
	
	/**
	 * Value defining that current field value is array type.
	 * @var int
	 */
	const VALUE_TYPE_ARRAY				= 1;


	/**
	 * Value defining that options have mixed types.
	 * @var int
	 */
	const OPTION_TYPE_MIXED				= 0;
	
	/**
	 * Value defining that options have only integer and float types.
	 * @var int
	 */
	const OPTION_TYPE_NUMERIC			= 1;


	/**
	 * Options loader instance method context in current form class.
	 * @var int
	 */
	const LOADER_CONTEXT_FORM			= 1;
	
	/**
	 * Options loader static method context in current form class.
	 * @var int
	 */
	const LOADER_CONTEXT_FORM_STATIC	= 2;
	
	/**
	 * Options loader instance method context in parent controller class of current form.
	 * @var int
	 */
	const LOADER_CONTEXT_CTRL			= 4;
	
	/**
	 * Options loader static method context in parent controller class of current form.
	 * @var int
	 */
	const LOADER_CONTEXT_CTRL_STATIC	= 8;
	
	/**
	 * Options loader instance method context in current model instance of current model form.
	 * For this value you need to install extension `mvccore/ext-model-form` and use features for model forms.
	 * @var int
	 */
	const LOADER_CONTEXT_MODEL			= 16;
	
	/**
	 * Options loader static method context in current model instance of current model form.
	 * For this value you need to install extension `mvccore/ext-model-form` and use features for model forms.
	 * @var int
	 */
	const LOADER_CONTEXT_MODEL_STATIC	= 32;


	/**
	 * Set form control or group control options to render
	 * more values for more specified submitted keys.
	 * 
	 * Example:
	 * ````
	 *   // To configure for example radio buttons named: `gender` for `Female` and `Male`:
	 *   //   <label for="gender-f">Female:</label>
	 *   //   <input id="gender-f" type="radio" name="gender" value="f" />
	 *   //   <label for="gender-m">Male:</label>
	 *   //   <input id="gender-m" type="radio" name="gender" value="m" />
	 *   // use this configuration:
	 *   $field->SetName('gender')->SetOptions([
	 *       // field values will be automatically translated, 
	 *       // if form has configured translator `callable`
	 *       'f' => 'Female',
	 *       'm' => 'Male',
	 *   ]);
	 *   
	 *   // Or you can use more advanced configuration with css class names 
	 *   // and html element attributes, let's consider html code like this:
	 *   //   <label for="gender-f" class="female">Female:</label>
	 *   //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 *   //   <label for="gender-m" class="male">Male:</label>
	 *   //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 *   // For that use this configuration:
	 *   $field->SetName('gender')->SetOptions([
	 *      'f' => [
	 *          'text'  => 'Female',	// text key will be also automatically translated
	 *          'class' => 'female',
	 *          'attrs' => ['data-any' => 'something-for-females'],
	 *      ],
	 *      'm' => [
	 *          'text'  => 'Male', // text key will be also automatically translated
	 *          'class' => 'male',
	 *          'attrs' => ['data-any' => 'something-for-males'],
	 *      ],
	 *   ]);
	 * ````
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptions (array $options = []);

	/**
	 * Add form control or group control options to render
	 * more values for more specified submitted keys.
	 * Previous options will be merged with given options.
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddOptions (array $options = []);

	/**
	 * Return reference to configured options array.
	 * @return array
	 */
	public function & GetOptions ();

	/**
	 * Set callable or dynamic callable definition to load control options.
	 * Value could be:
	 * - Standard PHP callable or `\Closure` function.
	 * - Dynamic callable definition by array with first item to define context
	 *   definition int flag, where the method (second array item) is located, 
	 *   you can use constants:
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 *   Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * @param  callable|\Closure|array|string $optionsLoader
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptionsLoader ($optionsLoader);
	
	/**
	 * Get callable or dynamic callable definition to load control options.
	 * Value could be:
	 * - Standard PHP callable or `\Closure` function.
	 * - Dynamic callable definition by array with first item to define context
	 *   definition int flag, where the method (second array item) is located, 
	 *   you can use constants:
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 *   Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * @return callable|\Closure|array|string|NULL
	 */
	public function GetOptionsLoader ();

	/**
	 * Set `FALSE` if you don't want to translate options texts, default `TRUE`.
	 * @param  bool|NULL $translateOptions 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslateOptions ($translateOptions = TRUE);

	/**
	 * Return boolean if options are translated or not.
	 * @return bool|NULL
	 */
	public function GetTranslateOptions ();

	/**
	 * Merge given field options with possible grouped options into single 
	 * level flatten array for submit checking purposes.
	 * If second argument is `TRUE` (by default), array values is option visible text (`string`),
	 * if second argument is `FALSE`, array values is original option values (`string` or `array`).
	 * @param  array|NULL $fieldOptions
	 * @param  bool       $asKeyValue   `TRUE` by default.
	 * @return array
	 */
	public function & GetFlattenOptions ($fieldOptions = NULL, $asKeyValue = TRUE);
}
