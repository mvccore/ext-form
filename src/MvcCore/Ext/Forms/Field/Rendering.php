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
	 * Render field in full mode, naturaly or by custom template.
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
	 * Render field by configured template.
	 * This method creates $view = new \MvcCore\Ext\Form\Core\View
	 * sets all local context variables into it and renders it into string.
	 * @return string
	 */
	public function RenderTemplate () {
		include_once('View.php');
		$view = new View($this->Form);
		$this->Field = $this;
		$view->SetUp($this);
		return $view->Render(
			\MvcCore\Ext\Forms\View::GetFormsDir(),
			is_bool($this->viewScript) ? $this->type : $this->viewScript
		);
	}
	/**
	 * Render field naturaly by render mode.
	 * Field shoud be rendered with label beside, label around
	 * or without label by local field configuration. Also there
	 * could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally () {
		$result = '';
		if ($this->RenderMode == Configuration::FIELD_RENDER_MODE_NORMAL && $this->Label) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->RenderMode == Configuration::FIELD_RENDER_MODE_LABEL_AROUND && $this->Label) {
			$result = $this->RenderControlInsideLabel();
		} else if ($this->RenderMode == Configuration::FIELD_RENDER_MODE_NO_LABEL || !$this->Label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			if ($this->Form->ErrorsRenderMode !== Configuration::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($this->Form->ErrorsRenderMode !== Configuration::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}
	/**
	 * Render field control and label by local configuration in left or in right side,
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderLabelAndControl () {
		$result = "";
		if ($this->LabelSide == 'left') {
			$result = $this->RenderLabel() . $this->RenderControl();
		} else {
			$result = $this->RenderControl() . $this->RenderLabel();
		}
		$errors = $this->RenderErrors();
		if ($this->Form->ErrorsRenderMode == Configuration::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($this->Form->ErrorsRenderMode == Configuration::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}
	/**
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->RenderMode == Configuration::FIELD_RENDER_MODE_NO_LABEL) return $this->RenderControl();
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		$template = $this->LabelSide == 'left' ? static::$Templates->togetherLabelLeft : static::$Templates->togetherLabelRight;
		$result = $this->Form->View->Format($template, array(
			'id'		=> $this->Id,
			'label'		=> $this->Label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		$errors = $this->RenderErrors();
		if ($this->Form->ErrorsRenderMode == Configuration::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($this->Form->ErrorsRenderMode == Configuration::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}
	/**
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		return $this->Form->View->Format(static::$Templates->control, array(
			'id'		=> $this->Id,
			'name'		=> $this->Name,
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
	/**
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->RenderMode == Configuration::FIELD_RENDER_MODE_NO_LABEL) return '';
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		return $this->Form->View->Format(static::$Templates->label, array(
			'id'		=> $this->Id,
			'label'		=> $this->Label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
	/**
	 * Render field specific errors only without control or label.
	 * @return string
	 */
	public function RenderErrors () {
		$result = "";
		if ($this->Errors && $this->Form->ErrorsRenderMode !== Configuration::ERROR_RENDER_MODE_ALL_TOGETHER) {
			$result .= '<span class="errors">';
			foreach ($this->Errors as $key => $errorMessage) {
				$errorCssClass = 'error';
				if (isset($this->Fields[$key])) $errorCssClass .= " $key";
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
	protected function renderLabelAttrsWithFieldVars ($fieldVars = array()) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->LabelAttrs, $this->CssClasses
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
	protected function renderControlAttrsWithFieldVars ($fieldVars = array()) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->ControlAttrs, $this->CssClasses, TRUE
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
	 * @param bool $controlRendering
	 * @return string
	 */
	protected function renderAttrsWithFieldVars (
		$fieldVars = array(), $fieldAttrs = array(), $cssClasses = array(), $controlRendering = FALSE
	) {
		$attrs = array();
		foreach ($fieldVars as $fieldVar) {
			if (!is_null($this->$fieldVar)) {
				$attrName = \MvcCore\Tool::GetDashedFromPascalCase($fieldVar);
				$attrs[$attrName] = $this->$fieldVar;
			}
		}
		$boolFieldVars = array('Disabled', 'Readonly', 'Required');
		foreach ($boolFieldVars as $fieldVar) {
			if ($this->$fieldVar) {
				$attrName = lcfirst($fieldVar);
				if ($controlRendering) $attrs[$attrName] = $attrName;
				$cssClasses[] = $attrName;
			}
		}
		$cssClasses[] = \MvcCore\Tool::GetDashedFromPascalCase($this->Name);
		$attrs['class'] = implode(' ', $cssClasses);
		include_once('View.php');
		return View::RenderAttrs(
			array_merge($fieldAttrs, $attrs)
		);
	}
}
