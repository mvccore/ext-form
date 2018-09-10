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

/**
 * Responsibility: init, predispatch and render `<button type="reset">`
 *				   with default text `Reset` and it's supporting JS code.
 *				   Button has no validator and no custom result state.
 */
class ResetButton extends \MvcCore\Ext\Forms\Fields\Button
{
	/**
	 * Possible values: `reset`.
	 * @var string
	 */
	protected $type = 'reset';

	/**
	 * Default visible button text - `Reset`.
	 * This button text is automaticly checked, if there is at least any 
	 * visible text and automaticly translated, if any translator `callable` 
	 * defined in form instance.
	 * @var string
	 */
	protected $value = 'Reset';

	/**
	 * Supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->jsSupportingFile` property to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsClassName = 'MvcCoreForm.Reset';

	/**
	 * Field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/reset.js';
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * - Translate value text if necessary.
	 * - Set up tabindex if necessary.
	 * - Add supporting javascript file.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->form->AddJsSupportFile(
			$this->jsSupportingFile, $this->jsClassName, [$this->name]
		);
	}
}
