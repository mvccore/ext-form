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
	 * @inheritDocs
	 * @return string
	 */
	public function Render () {
		$cssClasses = array_merge([], $this->cssClasses, [
			\MvcCore\Tool::GetDashedFromPascalCase($this->name)
		]);
		$attrs = [
			'name'	=> $this->name,
			'class'	=> implode(' ', array_unique($cssClasses)),
		];
		if ($this->disabled)
			$attrs['disabled'] = 'disabled';
		if ($this->title !== NULL)
			$attrs['title'] = $this->title;
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrs['form'] = $this->form->GetId();
		if (count($this->controlAttrs))
			$attrs = array_merge([], $this->controlAttrs, $attrs);
		$result = ['<fieldset'];
		foreach ($attrs as $attrName => $attrValue) 
			$result[] = ' ' . $attrName . '="' . $attrValue . '"';
		$result[] = '>';
		$result[] = $this->RenderLegend();
		$result[] = $this->RenderErrorsAndContent();
		$result[] = '</fieldset>';
		return implode('', $result);
	}
	
	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderLegend () {
		$legendContent = strip_tags($this->legend, static::ALLOWED_LEGEND_ELEMENTS);
		return '<legend>' . $legendContent . '</legend>';
	}
	
	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderErrorsAndContent () {
		/** @var \MvcCore\Ext\Forms\View $view */
		$view = $this->form->GetView();
		$parentChildren = $view->GetChildren();
		$view->SetChildren($this->GetChildren(TRUE), TRUE);
		$result = $view->RenderErrorsAndContent();
		$view->SetChildren($parentChildren, FALSE);
		return $result;
	}
}
