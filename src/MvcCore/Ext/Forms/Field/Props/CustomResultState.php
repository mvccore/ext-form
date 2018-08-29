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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Image`
 * - `\MvcCore\Ext\Forms\Fields\SubmitButton`
 * - `\MvcCore\Ext\Forms\Fields\SubmitInput`
 */
trait CustomResultState
{
	/**
	 * Submit button custom result state to move submit request to custom specific target url.
	 * There are already build in result states:
	 * - `0` (`\MvcCore\Ext\Forms\IForm::RESULT_ERRORS`)		- to move submit request to configured error url.
	 * - `1` (`\MvcCore\Ext\Forms\IForm::RESULT_SUCCESS`)		- to move submit request to configured success url.
	 * - `2` (`\MvcCore\Ext\Forms\IForm::RESULT_PREV_PAGE`)		- to move submit request to configured previous step url.
	 * - `3` (`\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`)		- to move submit request to configured next step url.
	 * If you define any new custom result state for your submit button (`4`, `5` ...), 
	 * you have to implement `$form->SubmittedRedirect();` method by your own to redirect
	 * user submit request by this state to your own specific target url.
	 * @var int|NULL
	 */
	protected $customResultState = NULL;

	/**
	 * Get submit button custom result state to move submit request to custom specific target url.
	 * There are already build in result states:
	 * - `0` (`\MvcCore\Ext\Forms\IForm::RESULT_ERRORS`)		- to move submit request to configured error url.
	 * - `1` (`\MvcCore\Ext\Forms\IForm::RESULT_SUCCESS`)		- to move submit request to configured success url.
	 * - `2` (`\MvcCore\Ext\Forms\IForm::RESULT_PREV_PAGE`)		- to move submit request to configured previous step url.
	 * - `3` (`\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`)		- to move submit request to configured next step url.
	 * If you define any new custom result state for your submit button (`4`, `5` ...), 
	 * you have to implement `$form->SubmittedRedirect();` method by your own to redirect
	 * user submit request by this state to your own specific target url.
	 * @return int|NULL
	 */
	public function GetCustomResultState () {
		return $this->customResultState;
	}

	/**
	 * Set submit button custom result state to move submit request to custom specific target url.
	 * There are already build in result states:
	 * - `0` (`\MvcCore\Ext\Forms\IForm::RESULT_ERRORS`)		- to move submit request to configured error url.
	 * - `1` (`\MvcCore\Ext\Forms\IForm::RESULT_SUCCESS`)		- to move submit request to configured success url.
	 * - `2` (`\MvcCore\Ext\Forms\IForm::RESULT_PREV_PAGE`)		- to move submit request to configured previous step url.
	 * - `3` (`\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`)		- to move submit request to configured next step url.
	 * If you define any new custom result state for your submit button (`4`, `5` ...), 
	 * you have to implement `$form->SubmittedRedirect();` method by your own to redirect
	 * user submit request by this state to your own specific target url.
	 * @param int|NULL $customResultState 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCustomResultState ($customResultState = \MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE) {
		$this->customResultState = $customResultState;
		return $this;
	}
}
