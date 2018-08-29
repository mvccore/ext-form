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

trait Rendering
{
	/**
	 * Render field in full mode (with configured label), naturally or by custom template.
	 * @return string
	 */
	public function Render () {
		if ($this->viewScript) {
			return $this->RenderTemplate();
		} else {
			return $this->RenderNaturally();
		}
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Renders field by configured custom template property `$field->viewScript`.
	 * This method creates `$view = new \MvcCore\Ext\Form\Core\View();`,
	 * sets all local context variables into view instance and renders 
	 * configured view instance into result string.
	 * @return string
	 */
	public function RenderTemplate () {
		$viewClass = $this->form->GetViewClass();
		$formParentController = $this->form->GetParentController();
		$view = $viewClass::CreateInstance()
			->SetController($formParentController)
			->SetView($formParentController->GetView())
			->SetForm($this->form)
			->SetField($this);
		return $view->Render(
			$viewClass::GetFieldsDir(),
			is_bool($this->viewScript) ? $this->type : $this->viewScript
		);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field naturally by configured property `$field->renderMode` if any 
	 * or by default render mode without any label. Field shoud be rendered with 
	 * label beside, label around or without label by local field configuration. 
	 * Also there could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally () {
		$result = '';
		$renderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL;
		$label = NULL;
		if ($this instanceof \MvcCore\Ext\Forms\Fields\ILabel) {
			$renderMode = $this->GetRenderMode();
			$label = $this->GetLabel();
		}
		if ($renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL && $label) {
			$result = $this->RenderLabelAndControl();
		} else if ($renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_LABEL_AROUND && $label) {
			$result = $this->RenderControlInsideLabel();
		} else if ($renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL || !$label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
			if ($formErrorsRenderMode !== \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($formErrorsRenderMode !== \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field control and label by local configuration in left or in right side,
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderLabelAndControl () {
		$result = '';
		if ($this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
			$result = $this->RenderLabel() . $this->RenderControl();
		} else {
			$result = $this->RenderControl() . $this->RenderLabel();
		}
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return $this->RenderControl();
		$template = $this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT
			? static::$templates->togetherLabelLeft 
			: static::$templates->togetherLabelRight;
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format($template, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		$renderMode = $this instanceof \MvcCore\Ext\Forms\Fields\ILabel
			? $this->GetRenderMode()
			: \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL;
		if ($renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return '';
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->label, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field specific errors only without control or label.
	 * @return string
	 */
	public function RenderErrors () {
		$result = "";
		if (
			$this->errors && 
			$this->form->GetErrorsRenderMode() !== \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER
		) {
			$result .= '<span class="errors">';
			foreach ($this->errors as $key => $errorMessage) {
				$errorCssClass = 'error error-' . $this->name . ' error-' . $key;
				$result .= "<span class=\"$errorCssClass\">$errorMessage</span>";
			}
			$result .= '</span>';
		}
		return $result;
	}


	/* protected renderers *******************************************************************/

	/**
	 * Complete HTML attributes and css classes strings for label element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * @param string[] $fieldVars
	 * @return string
	 */
	protected function renderLabelAttrsWithFieldVars ($fieldVars = []) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->labelAttrs, $this->cssClasses, FALSE
		);
	}
	/**
	 * Complete HTML attributes and css classes strings for control element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * @param string[] $fieldVars
	 * @return string
	 */
	protected function renderControlAttrsWithFieldVars ($fieldVars = []) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->controlAttrs, $this->cssClasses, TRUE
		);
	}
	/**
	 * Complete HTML attributes and css classes strings for label/control element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * Only if fourth param is false, do not add system attributes in boolean
	 * mode into attributes, only into css class.
	 * @param string[] $fieldVars
	 * @param array $fieldAttrs
	 * @param array $cssClasses
	 * @param bool $controlRendering `TRUE` value means control rendering, `FALSE` means label rendering.
	 * @return string
	 */
	protected function renderAttrsWithFieldVars (
		$fieldVars = [], 
		$fieldAttrs = [], 
		$cssClasses = [], 
		$controlRendering = FALSE
	) {
		$attrs = [];
		foreach ($fieldVars as $fieldName) {
			if ($this->{$fieldName} !== NULL) {
				$attrName = strtolower($fieldName);
				$fieldType = gettype($this->{$fieldName});
				if ($fieldType == 'array') {
					$attrs[$attrName] = implode(',', $this->{$fieldName});
				} else {
					$attrs[$attrName] = (string) $this->{$fieldName};
				}
			}
		}
		if ($this instanceof \MvcCore\Ext\Forms\Fields\IVisibleField) {
			$boolFieldVars = [
				'accessKey'	=> FALSE, 
				'autoFocus' => TRUE, 
				'disabled'	=> TRUE, 
				'readOnly'	=> TRUE, 
				'required'	=> TRUE,
			];
			foreach ($boolFieldVars as $fieldName => $addAlsoAsCssClass) {
				if (isset($this->{$fieldName}) && $this->{$fieldName} !== NULL && $this->{$fieldName} !== FALSE) {
					$attrName = strtolower($fieldName);
					if ($controlRendering) $attrs[$attrName] = $attrName;
					if ($addAlsoAsCssClass) $cssClasses[] = $attrName;
				}
			}
			if ($this->tabIndex !== NULL)
				$attrs['tabindex'] = $this->tabIndex + $this->form->GetBaseTabIndex();
		}
		$cssClasses[] = \MvcCore\Tool::GetDashedFromPascalCase($this->name);
		$attrs['class'] = implode(' ', $cssClasses);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::RenderAttrs(
			array_merge($fieldAttrs, $attrs)
		);
	}
}
