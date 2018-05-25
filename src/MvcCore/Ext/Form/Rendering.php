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

namespace MvcCore\Ext\Form;

trait Rendering
{

	/**
	 * Rendering process alias.
	 * @see \MvcCore\Ext\Form::Render();
	 * @return string
	 */
	public function __toString () {
		return $this->Render();
	}
	/**
	 * Render form into string to display it.
	 * - If form is not initialized, there is automaticly
	 *   called `$form->Init();` method.
	 * - If form is not prepared for rendering, there is
	 *   automaticly called `$form->prepareForRendering();` method.
	 * - Create new form view instance and set up the view with local
	 *   context variables.
	 * - Render form naturaly or by custom template.
	 * - Clean session errors, because errors shoud be rendered
	 *   only once, only when it's used and it's now - in this rendering process.
	 * @return string
	 */
	public function Render ($controllerDashedName = '', $actionDashedName = '') {
		$this->preDispatchIfNecessary();
		if ($this->viewScript) {
			$result = $this->View->RenderTemplate();
		} else {
			$result = $this->View->RenderNaturally();
		}
		$this->cleanUpRenderIfNecessary();
		return $result;
	}
	/**
	 * Render form content.
	 * Go through all $form->Fields and call $field->Render(); on every field
	 * and put it into an empty <div> element. Render each field in full possible
	 * way - naturaly by label configuration with possible errors configured beside
	 * or with custom field template.
	 * @return string
	 */
	public function RenderContent () {
		$this->preDispatchIfNecessary();
		return $this->View->RenderContent();
	}
	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into div.errors with div.error elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		$this->preDispatchIfNecessary();
		return $this->View->RenderErrors();
	}
	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with csrf tokens.
	 * @return string
	 */
	public function RenderBegin () {
		$this->preDispatchIfNecessary();
		return $this->View->RenderBegin();
	}
	/**
	 * Render form end.
	 * Render html closing </form> tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderEnd () {
		if ($this->dispatchState < 1) $this->Init();
		$result = $this->View->RenderEnd();
		$this->cleanUpRenderIfNecessary();
		return $result;
	}
	/**
	 * Render all supporting css files directly
	 * as <style> tag content inside html template
	 * called usualy right after form end tag
	 *	or
	 * render all supporting css files by external
	 * css assets renderer to add only links to html head
	 * linked to external css source files.
	 * @return string
	 */
	public function RenderCss () {
		if (!$this->Css) return '';
		$cssFiles = $this->completeAssets('css');
		$cssFilesContent = '';
		$loadCssFilesContents = !is_callable($this->CssRenderer);
		foreach ($cssFiles as $cssFile) {
			$this->renderAssetFile($cssFilesContent, $this->CssRenderer, $loadCssFilesContents, $cssFile);
		}
		if (!$loadCssFilesContents) return '';
		return '<style type="text/css">'.$cssFilesContent.'</style>';
	}
	/**
	 * Render all supporting js files directly
	 * as <script> tag content inside html template
	 * called usualy right after form end tag
	 *	or
	 * render all supporting javascript files by external
	 * assets renderer to add only scripts to html head
	 * linked to external script source files. But there is still created
	 * one <script> tag right after form tag end with supporting javascripts
	 * initializations by rendered form fieds options, names, counts, values etc...
	 * @return string
	 */
	public function RenderJs () {
		if (!$this->Js) return '';
		$jsFiles = $this->completeAssets('js');
		$jsFilesContent = '';
		$fieldsConstructors = array();
		$loadJsFilesContents = !is_callable($this->JsRenderer);
		if (!isset(self::$js[$this->JsBaseFile])) {
			$this->JsBaseFile = $this->absolutizeAssetPath($this->JsBaseFile, 'js');
			self::$js[$this->JsBaseFile] = TRUE;
			$this->renderAssetFile($jsFilesContent, $this->JsRenderer, $loadJsFilesContents, $this->JsBaseFile);
		}
		foreach ($jsFiles as $jsFile) {
			$this->renderAssetFile($jsFilesContent, $this->JsRenderer, $loadJsFilesContents, $jsFile);
		}
		foreach ($this->Js as $item) {
			$paramsStr = json_encode($item[2]);
			$paramsStr = mb_substr($paramsStr, 1, mb_strlen($paramsStr) - 2);
			$fieldsConstructors[] = "new " . $item[1] . "(" . $paramsStr . ")";
		}
		$result = $jsFilesContent."new MvcCoreForm("
			."document.getElementById('".$this->Id."'),"
			."[".implode(',', $fieldsConstructors)."]"
		.")";
		include_once('Form/Core/View.php');
		if (class_exists('\MvcCore\View') && strpos(\MvcCore\View::GetDoctype(), 'XHTML') !== FALSE) {
			$result = '/* <![CDATA[ */' . $result . '/* ]]> */';
		}
		return '<script type="text/javascript">' . $result . '</script>';
	}

	/**
	 * Clean up after rendering.
	 * - clean session errors
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm|\MvcCore\Ext\Form\Core\Base
	 */
	protected function cleanUpRenderIfNecessary () {
		$this->Errors = array();
		include_once('Helpers.php');
		Helpers::SetSessionErrors($this->Id, array());
		return $this;
	}
	/**
	 * Prepare for rendering.
	 * - process all defined fields and call $field->setUp();
	 *   to prepare field for rendering process.
	 * - load any possible error from session and set up
	 *   errors into fields and into form object to render them properly
	 * - load any possible previously submitted or stored data
	 *   from session and set up form with them.
	 * - set initialized state to 2, which means - prepared for rendering
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function preDispatchIfNecessary () {
		if ($this->dispatchState > 2) return $this;
		parent::PreDispatch(); // code: `if ($this->dispatchState < 1) $this->Init();` is executed by parent
		xxx($this->fields);
		foreach ($this->fields as & $field)
			// translate fields if necessary and do any rendering preparation stuff
			$field->PreDispatch();
		$session = & $this->getSession();
		foreach ($session->errors as & $errorMsgAndFieldNames) {
			list($errorMsg, $fieldNames) = array_merge(array(), $errorMsgAndFieldNames);
			$this->AddError($errorMsg, $fieldNames);
		}
		if ($session->values) 
			$this->SetValues(array_merge(array(), $session->values));
		$viewClass = $this->viewClass;
		$this->view = $viewClass::CreateInstance()
			->SetController($this->parentController)
			->SetForm($this)
			->SetUpValuesFromController($this->parentController, TRUE)
			->SetUpValuesFromView($this->parentController->GetView(), TRUE)
			->SetUpValuesFromController($this, TRUE);
		$this->dispatchState = 2;
		return $this;
	}
}
