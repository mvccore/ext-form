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

/**
 * Trait for class `MvcCore\Ext\Form` containing rendering logic and methods.
 * @property \MvcCore\Ext\Forms\View $view View instance.
 */
trait Rendering
{
	/**
	 * Render whole `<form>` with all content into HTML string to display it.
	 * - If form is not initialized, there is automatically
	 *   called `$form->Init();` method.
	 * - If form is not pre-dispatched for rendering, there is
	 *   automatically called `$form->PreDispatch();` method.
	 * - Create new form view instance and set up the view with local
	 *   context variables.
	 * - Render form naturally or by custom template.
	 * - Clean session errors, because errors should be rendered
	 *   only once, only when it's used and it's now - in this rendering process.
	 * @return string
	 */
	public function Render ($controllerDashedName = NULL, $actionDashedName = NULL) {
		/** @var $this \MvcCore\Ext\Form */
		$this->PreDispatch();
		if ($this->viewScript) {
			$result = $this->view->RenderTemplate();
		} else {
			$result = $this->view->RenderNaturally();
		}
		$this->cleanSessionErrorsAfterRender();
		return $result;
	}

	/**
	 * Render form inner content, all field controls, content inside `<form>` tag,
	 * without form errors. Go through all `$form->fields` and call `$field->Render();`
	 * on every field instance and put field render result into an empty `<div>`
	 * element. Render each field in full possible way - naturally by label
	 * configuration with possible errors configured beside or with custom field template.
	 * @return string
	 */
	public function RenderContent () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderContent();
	}

	/**
	 * Render form errors to display them inside `<form>` element.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into `div.errors` with `div.error` elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderErrors();
	}

	/**
	 * Render form begin - opening `<form>` tag and automatically
	 * prepared hidden input with CSRF (Cross Site Request Forgery) tokens.
	 * @return string
	 */
	public function RenderBegin () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderBegin();
	}

	/**
	 * Render form end - closing `</form>` tag and supporting javascript and css files
	 * only if there is necessary to add any supporting javascript or css files by
	 * form configuration and if form is not using external JS/CSS renderer(s).
	 * @return string
	 */
	public function RenderEnd () {
		/** @var $this \MvcCore\Ext\Form */
		$this->PreDispatch();
		$this->SetFormTagRenderingStatus(FALSE);
		$result = '</form>'
			. $this->RenderSupportingJs()
			. $this->RenderSupportingCss();
		$this->cleanSessionErrorsAfterRender();
		return $result;
	}

	/**
	 * Render all supporting CSS files directly
	 * as `<style>` tag content inside HTML template
	 * placed directly after `</form>` end tag or
	 * render all supporting CSS files by configured external
	 * CSS files renderer to add only links to HTML response `<head>`
	 * section, linked to external CSS source files.
	 * @return string
	 */
	public function RenderSupportingCss () {
		/** @var $this \MvcCore\Ext\Form */
		if (!$this->cssSupportFiles) return '';
		$cssFiles = $this->completeSupportingFilesToRender(FALSE);
		if (!$cssFiles) return '';
		$cssFilesContent = '';
		$useExternalRenderer = is_callable($this->cssSupportFilesRenderer);
		foreach ($cssFiles as $cssFile) {
			$this->renderSupportingFile(
				$cssFilesContent, $cssFile,
				$useExternalRenderer, $this->cssSupportFilesRenderer
			);
		}
		if ($useExternalRenderer) return '';
		return '<style type="text/css">'.$cssFilesContent.'</style>';
	}

	/**
	 * Render all supporting JS files directly
	 * as `<script>` tag content inside HTML template
	 * placed directly after `</form>` end tag or
	 * render all supporting JS files by configured external
	 * JS files renderer to add only links to HTML response `<head>`
	 * section, linked to external JS source files.
	 * Anyway there is always created at least one `<script>` tag
	 * placed directly after `</form>` end tag with supporting javascripts
	 * initializations - `new MvcCoreForm(/*javascript*\/);` - by rendered form fields
	 * options, names, counts, values etc...
	 * @return string
	 */
	public function RenderSupportingJs () {
		/** @var $this \MvcCore\Ext\Form */
		if (!$this->jsSupportFiles) return '';
		$jsFiles = $this->completeSupportingFilesToRender(TRUE);
		if (!$jsFiles) return '';
		$jsFilesContent = '';
		$fieldsConstructors = [];
		$useExternalRenderer = is_callable($this->jsSupportFilesRenderer);
		if (!isset(self::$allJsSupportFiles[static::$jsBaseSupportFile])) {
			$jsBaseSupportFile = $this->absolutizeSupportingFilePath(static::$jsBaseSupportFile, 'js');
			self::$allJsSupportFiles[static::$jsBaseSupportFile] = TRUE;
			$this->renderSupportingFile(
				$jsFilesContent, $jsBaseSupportFile,
				$useExternalRenderer, $this->jsSupportFilesRenderer
			);
		}
		foreach ($jsFiles as $jsFile)
			$this->renderSupportingFile(
				$jsFilesContent, $jsFile,
				$useExternalRenderer, $this->jsSupportFilesRenderer
			);
		foreach ($this->jsSupportFiles as $jsSupportFile) {
			list(, $jsFullClassName, $constructParams) = $jsSupportFile;
			$constructParamsEncoded = json_encode($constructParams);
			// remove beginning char and ending char from javascript array: `[`, `]`
			$constructParamsEncoded = mb_substr(
				$constructParamsEncoded, 1, mb_strlen($constructParamsEncoded) - 2
			);
			$fieldsConstructors[] = 'new ' . $jsFullClassName . '(' . $constructParamsEncoded . ')';
		}
		$result = $jsFilesContent . 'new MvcCoreForm('
			. 'document.getElementById(\'' . $this->id . '\'),'
			. '[' . implode(',', $fieldsConstructors) . ']'
		. ')';
		$viewDocType = \MvcCore\View::GetDoctype();
		if (
			$this->response->IsXmlOutput() ||
			strpos($viewDocType, \MvcCore\View::DOCTYPE_XHTML) !== FALSE ||
			strpos($viewDocType, \MvcCore\View::DOCTYPE_XML) !== FALSE
		) $result = '/*<![CDATA[*/' . $result . '/*]]>*/';
		return '<script type="text/javascript">' . $result . '</script>';
	}

	/**
	 * Call this function after form has been rendered
	 * to clear session errors, because there is not necessary
	 * to have there those errors anymore, because will be
	 * displayed in rendered form.
	 * @return \MvcCore\Ext\Form
	 */
	protected function cleanSessionErrorsAfterRender () {
		/** @var $this \MvcCore\Ext\Form */
		$this->errors = [];
		$session = & $this->getSession();
		$session->errors = [];
		return $this;
	}
}
