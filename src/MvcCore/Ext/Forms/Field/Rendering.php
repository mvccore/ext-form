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

namespace MvcCore\Ext\Forms\Field;

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field rendering methods.
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait Rendering {
	
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function __toString () {
		return $this->Render();
	}

	/**
	 * @inheritDoc
	 * @param  string|NULL $labelAndControlSeparator
	 * @return string
	 */
	public function Render ($labelAndControlSeparator = NULL) {
		if ($this->viewScript !== NULL) {
			return $this->RenderTemplate();
		} else {
			return $this->RenderNaturally($labelAndControlSeparator);
		}
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @param  string|NULL $labelAndControlSeparator
	 * @return string
	 */
	public function RenderTemplate ($labelAndControlSeparator = NULL) {
		/** @var $viewClass string|\MvcCore\Ext\Forms\View */
		/** @var \MvcCore\Ext\Forms\View $view */
		$view = $this->createView();
		if ($labelAndControlSeparator !== NULL)
			$view->__set('labelAndControlSeparator', $labelAndControlSeparator);
		$viewClass = $this->form->GetViewClass();
		$result = $view->Render(
			$viewClass::GetFieldsDir(),
			is_bool($this->viewScript) ? $this->type : $this->viewScript
		);
		unset($view);
		return $result;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @param  string|NULL $labelAndControlSeparator
	 * @return string
	 */
	public function RenderNaturally ($labelAndControlSeparator = NULL) {
		$result = '';
		$renderMode = $this->form->GetFieldsRenderModeDefault();
		$label = NULL;
		if ($this instanceof \MvcCore\Ext\Forms\Fields\ILabel) {
			$renderModeLocal = $this->GetRenderMode();
			if ($renderModeLocal !== NULL) $renderMode = $renderModeLocal;
			$label = $this->GetLabel();
		}
		if ($renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL && $label !== NULL) {
			$result = $this->RenderLabelAndControl($labelAndControlSeparator);
		} else if ($renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND && $label !== NULL) {
			$result = $this->RenderControlInsideLabel();
		} else if ($renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL || $label === NULL) {
			$result = $this->RenderControl();
			$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
			if ($formErrorsRenderMode !== \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $this->RenderErrors() . $result;
			} else if ($formErrorsRenderMode !== \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $this->RenderErrors();
			}
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @param  string|NULL $labelAndControlSeparator
	 * @return string
	 */
	public function RenderLabelAndControl ($labelAndControlSeparator = NULL) {
		$result = '';
		$errors = '';
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		$allErrorsTogether = (
			$formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_ALL_TOGETHER ||
			$formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AT_FIELDSET_BEGIN
		);
		if (!$allErrorsTogether) 
			$errors = $this->RenderErrors();
		if ($labelAndControlSeparator === NULL) 
			$labelAndControlSeparator = '';

		if ($this->labelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
			if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $this->RenderLabel() . $labelAndControlSeparator . $errors . $this->RenderControl();
			} else if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result = $this->RenderLabel() . $labelAndControlSeparator . $this->RenderControl() . $errors;
			} else if ($allErrorsTogether) {
				$result = $this->RenderLabel() . $labelAndControlSeparator . $this->RenderControl();
			}
			
		} else {
			if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $this->RenderControl() . $labelAndControlSeparator . $this->RenderLabel();
			} else if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result = $this->RenderControl() . $errors . $labelAndControlSeparator . $this->RenderLabel();
			} else if ($allErrorsTogether) {
				$result = $this->RenderControl() . $labelAndControlSeparator . $this->RenderLabel();
			}
		}
		
		return $result;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return $this->RenderControl();
		$template = $this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT
			? static::$templates->togetherLabelLeft 
			: static::$templates->togetherLabelRight;
		$attrsStr = $this->RenderLabelAttrsWithFieldVars();
		$formViewClass = $this->form->GetViewClass();
		$view = $this->form->GetView() ?: $this->form->GetController()->GetView();
		$result = $formViewClass::Format($template, [
			'id'		=> $this->id,
			'label'		=> $view->EscapeHtml($this->label),
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " {$attrsStr}" : '',
		]);
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return string
	 */
	public function RenderControl () {
		$attrsStrItems = [$this->RenderControlAttrsWithFieldVars()];
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStrItems[] = 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		$view = $this->form->GetView() ?: $this->form->GetController()->GetView();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $view->EscapeAttr($this->value),
			'attrs'		=> count($attrsStrItems) > 0 ? ' ' . implode(' ', $attrsStrItems) : '',
		]);
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return string
	 */
	public function RenderLabel () {
		$renderMode = $this instanceof \MvcCore\Ext\Forms\Fields\ILabel
			? $this->GetRenderMode()
			: \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL;
		if ($renderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return '';
		$attrsStr = $this->RenderLabelAttrsWithFieldVars();
		$formViewClass = $this->form->GetViewClass();
		$view = $this->form->GetView() ?: $this->form->GetController()->GetView();
		return $formViewClass::Format(static::$templates->label, [
			'id'		=> $this->id,
			'label'		=> $view->EscapeHtml($this->label),
			'attrs'		=> $attrsStr ? " {$attrsStr}" : '',
		]);
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @return string
	 */
	public function RenderErrors () {
		$result = [];
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if (
			$this->errors && 
			$formErrorsRenderMode !== \MvcCore\Ext\IForm::ERROR_RENDER_MODE_ALL_TOGETHER &&
			$formErrorsRenderMode !== \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AT_FIELDSET_BEGIN
		) {
			$result[] = '<span class="errors">';
			foreach ($this->errors as $key => $errorMessage) {
				$errorCssClass = 'error error-' . $this->name . ' error-' . $key;
				$result[] = "<span class=\"{$errorCssClass}\">{$errorMessage}</span>";
			}
			$result[] = '</span>';
		}
		return implode('', $result);
	}
	
	/**
	 * @inheritDoc
	 * @param  \string[] $fieldVars
	 * @return string
	 */
	public function RenderLabelAttrsWithFieldVars ($fieldVars = []) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->labelAttrs, $this->cssClasses, FALSE
		);
	}

	/**
	 * @inheritDoc
	 * @param  \string[] $fieldVars
	 * @return string
	 */
	public function RenderControlAttrsWithFieldVars ($fieldVars = []) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->controlAttrs, $this->cssClasses, TRUE
		);
	}


	/* protected renderers *******************************************************************/

	/**
	 * View instance factory method.
	 * @return \MvcCore\View
	 */
	protected function createView () {
		$viewClass = $this->form->GetViewClass();
		$formParentController = $this->form->GetParentController();
		$view = $viewClass::CreateInstance();
		$view
			->SetController($formParentController)
			->SetEncoding($formParentController->GetResponse()->GetEncoding())
			->SetView($formParentController->GetView())
			->SetForm($this->form)
			->SetField($this);
		return $view;
	}

	/**
	 * Complete HTML attributes and css classes strings for label/control element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automatically render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * Only if fourth param is false, do not add system attributes in boolean
	 * mode into attributes, only into css class.
	 * @param  \string[] $fieldVars
	 * @param  array     $fieldAttrs
	 * @param  array     $cssClasses
	 * @param  bool      $controlRendering `TRUE` value means control rendering, `FALSE` means label rendering.
	 * @return string
	 */
	protected function renderAttrsWithFieldVars (
		$fieldVars = [], 
		$fieldAttrs = [], 
		$cssClasses = [], 
		$controlRendering = FALSE
	) {
		$attrs = [];
		foreach ($fieldVars as $key => $value) {
			if (is_numeric($key)) {
				$fieldName = $value;
				$attrName = strtolower($fieldName);
			} else {
				$fieldName = $key;
				$attrName = strtolower($value);
			}
			if ($this->{$fieldName} !== NULL) {
				if (is_array($this->{$fieldName})) {
					$attrs[$attrName] = implode(',', $this->{$fieldName});
				} else {
					$attrs[$attrName] = (string) $this->{$fieldName};
				}
			}
		}
		if ($this instanceof \MvcCore\Ext\Forms\Fields\IVisibleField) {
			$boolFieldVars = [
				'accessKey'	=> FALSE, 
				'autoFocus'	=> TRUE, 
				'disabled'	=> TRUE, 
				'readOnly'	=> TRUE, 
				'required'	=> TRUE,
			];
			foreach ($boolFieldVars as $fieldName => $addAlsoAsCssClass) {
				if (
					isset($this->{$fieldName}) && 
					$this->{$fieldName} !== NULL && 
					$this->{$fieldName} !== FALSE
				) {
					$attrName = strtolower($fieldName);
					if ($controlRendering) $attrs[$attrName] = $attrName;
					if ($addAlsoAsCssClass) $cssClasses[] = $attrName;
				}
			}
			if ($controlRendering && $this->tabIndex !== NULL)
				$attrs['tabindex'] = $this->tabIndex + $this->form->GetBaseTabIndex();
			if ($this->title !== NULL)
				$attrs['title'] = $this->title;
		}
		$toolClass = $this->form->GetApplication()->GetToolClass();
		$cssClasses[] = $toolClass::GetDashedFromPascalCase($this->name);
		$cssClasses[] = $this->type;
		$attrs['class'] = implode(' ', array_unique($cssClasses));
		$formViewClass = $this->form->GetViewClass();
		$view = $this->form->GetView() ?: $this->form->GetController()->GetView();
		return $formViewClass::RenderAttrs(
			array_merge($fieldAttrs, $attrs), $view->EscapeAttr
		);
	}
}
