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
 * Responsibility - define getters and setters for attributes: `accessKey`, 
 * `autoFocus`, `disabled`, `readOnly`, `required` and `tabIndex`.
 */
interface IVisibleField
{
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey
	 * @return string|NULL
	 */
	public function GetAccessKey ();

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/accesskey
	 * @param string $accessKey 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetAccessKey ($accessKey);

	/**
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @return bool|NULL
	 */
	public function GetAutoFocus ();

	/**
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. If there is already defined any previously configured 
	 * autofocused form field, you can use second argument `$duplicateBehaviour` to solve the problem.
	 * Second argument possible values:
	 * - `0` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION`)
	 *   Default value, an exception is thrown when there is already defined other autofocused form element.
	 * - `1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW`)
	 *   There will be removed previously defined autofocused element and configured new given one.
	 * - `-1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW`)
	 *   There will be quietly configured another field autofocused. Be carefull!!! This is not standard behaviour!
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @param bool|NULL $autoFocus 
	 * @param int $duplicateBehaviour
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetAutoFocus ($autoFocus = TRUE, $duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION);

	/**
	 * Get form field attribute `disabled`, determinating if field value will be 
	 * possible to change by user and if user will be graphicly informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sended under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetDisabled () {
		return $this->disabled;
	}

	/**
	 * Set form field attribute `disabled`, determinating if field value will be 
	 * possible to change by user and if user will be graphicly informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sended under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param bool|NULL $readonly
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetDisabled ($disabled);

	/**
	 * Get form field attribute `readonly`, determinating if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sended under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetReadOnly () {
		return $this->readOnly;
	}

	/**
	 * Set form field attribute `readonly`, determinating if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sended under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param bool|NULL $readonly
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetReadOnly ($readOnly = TRUE);

	/**
	 * Get form field attribute required, determinating
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automaticly considered as required by default form configuration.
	 * But this method return only value stored inside this field instance.
	 * @return bool|NULL
	 */
	public function GetRequired () {
		return $this->required;
	}

	/**
	 * Set form field attribute required, determinating
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automaticly considered required by default form configuration.
	 * @param bool|NULL $required
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetRequired ($required = TRUE);

	/**
	 * Get an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. Tabindex for every field in form could be indexed as yu wish or it could
	 * be indexed from value `1` and moved to specific higher value by place, where form is 
	 * currently rendered by form instance method `$form->SetBaseTabIndex()` to move tabindex 
	 * for each field into final values. Tabindex can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @return int|NULL
	 */
	public function GetTabIndex ();

	/**
	 * Set an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. Tabindex for every field in form could be indexed as yu wish or it could
	 * be indexed from value `1` and moved to specific higher value by place, where form is 
	 * currently rendered by form instance method `$form->SetBaseTabIndex()` to move tabindex 
	 * for each field into final values. Tabindex can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @param int $tabIndex 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetTabIndex ($tabIndex);
}
