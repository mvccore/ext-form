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

namespace MvcCore\Ext\Forms\Fieldset;

/**
 * @mixin \MvcCore\Ext\Forms\Fieldset
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
	 * @return string
	 */
	public function Render () {
		$attrs = [];
		$cssClasses = array_unique(array_merge([], $this->cssClasses, [
			\MvcCore\Tool::GetDashedFromPascalCase($this->name)
		]));
		if (count($cssClasses) > 0)
			$attrs['class']	= implode(' ', $cssClasses);
		if ($this->disabled)
			$attrs['disabled'] = 'disabled';
		if ($this->title !== NULL)
			$attrs['title'] = $this->title;
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrs['form'] = $this->form->GetId();
		if (count($this->controlAttrs))
			$attrs = array_merge([], $this->controlAttrs, $attrs);
		$attrsStrItems = [];
		foreach ($attrs as $attrName => $attrValue) 
			$attrsStrItems[] = ' ' . $attrName . '="' . $attrValue . '"';
		$attrsStr = implode('', $attrsStrItems);
		// <fieldset name={name}{attrs}>{legend}{content}</fieldset>
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format($this->template, [
			'name'		=> $this->name,
			'attrs'		=> $attrsStr,
			'legend'	=> $this->RenderLegend(),
			'content'	=> $this->RenderErrorsAndContent(),
		]);
	}
	
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderLegend () {
		if ($this->legend === NULL) return '';
		$legendContent = trim(strip_tags($this->legend, static::ALLOWED_LEGEND_ELEMENTS));
		if (mb_strlen($this->legend) === 0) return '';
		return '<legend>' . $legendContent . '</legend>';
	}
	
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderErrorsAndContent () {
		/** @var \MvcCore\Ext\Forms\View $view */
		$view = $this->form->GetView();
		$parentFieldset = $view->GetFieldset();
		$parentChildren = $view->GetChildren();
		$view
			->SetFieldset($this)
			->SetChildren($this->GetChildren(TRUE));
		$result = $view->RenderErrorsAndContent();
		$view
			->SetFieldset($parentFieldset)
			->SetChildren($parentChildren);
		return $result;
	}
}
