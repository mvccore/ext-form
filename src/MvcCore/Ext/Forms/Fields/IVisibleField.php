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
 * Responsibility: define getters and setters for properties/attributes: 
 *				   `accessKey`, `autoFocus`, `disabled`, `readOnly`, 
 *				   `required` and `tabIndex`.
 * Interface for all field classes:
 * - `\MvcCore\Ext\Forms\Field`
 *    - `\MvcCore\Ext\Forms\Field\Rendering`
 * - `\MvcCore\Ext\Forms\Fields\Button`
 *    - `\MvcCore\Ext\Forms\Fields\SubmitButton`
 *    - `\MvcCore\Ext\Forms\Fields\ResetButton`
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * - `\MvcCore\Ext\Forms\Fields\Image`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\ResetInput`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\Fields\SubmitInput`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
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
	 *   There will be quietly configured another field autofocused. Be careful!!! This is not standard behaviour!
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/select#attr-autofocus
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea#attr-autofocus
	 * @param bool|NULL $autoFocus 
	 * @param int $duplicateBehaviour
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetAutoFocus ($autoFocus = TRUE, $duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION);

	/**
	 * Get form field attribute `disabled`, determination if field value will be 
	 * possible to change by user and if user will be graphically informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sent under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetDisabled ();

	/**
	 * Set form field attribute `disabled`, determination if field value will be 
	 * possible to change by user and if user will be graphically informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sent under field name 
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
	 * Get form field attribute `readonly`, determination if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sent under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetReadOnly ();

	/**
	 * Set form field attribute `readonly`, determination if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sent under field name 
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
	 * Get form field attribute required, determination
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically considered as required by default form configuration.
	 * But this method return only value stored inside this field instance.
	 * @return bool|NULL
	 */
	public function GetRequired ();

	/**
	 * Set form field attribute required, determination
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically considered required by default form configuration.
	 * @param bool|NULL $required
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetRequired ($required = TRUE);

	/**
	 * Get an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. Tab-index for every field in form could be indexed as you wish or it could
	 * be indexed from value `1` and moved to specific higher value by place, where form is 
	 * currently rendered by form instance method `$form->SetBaseTabIndex()` to move tab-index 
	 * for each field into final values. Tab-index can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tab-index. If several elements share the same tab-index, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tab-index
	 * @return int|NULL
	 */
	public function GetTabIndex ();

	/**
	 * Set an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. Tab-index for every field in form could be indexed as yu wish or it could
	 * be indexed from value `1` and moved to specific higher value by place, where form is 
	 * currently rendered by form instance method `$form->SetBaseTabIndex()` to move tab-index 
	 * for each field into final values. Tab-index can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tab-index. If several elements share the same tab-index, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tab-index
	 * @param int $tabIndex 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetTabIndex ($tabIndex);
}
