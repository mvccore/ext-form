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

namespace MvcCore\Ext\Form;

/**
 * Trait for class `MvcCore\Ext\Form` containing rendering logic and methods.
 * @property \MvcCore\Ext\Forms\View $view View instance.
 */
trait Rendering {

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function Render ($controllerDashedName = NULL, $actionDashedName = NULL) {
		/** @var $this \MvcCore\Ext\Form */
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->PreDispatch(FALSE);
		if ($this->viewScript) {
			$result = $this->view->RenderTemplate();
		} else {
			$result = $this->view->RenderNaturally();
		}
		$this->cleanSessionErrorsAfterRender();
		$this->dispatchState = \MvcCore\IController::DISPATCH_STATE_RENDERED;
		return $result;
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContent () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderContent();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderErrors () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderErrors();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderBegin () {
		/** @var $this \MvcCore\Ext\Form */
		return $this->view->RenderBegin();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderEnd () {
		/** @var $this \MvcCore\Ext\Form */
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->PreDispatch(FALSE);
		$this->SetFormTagRenderingStatus(FALSE);
		$result = '</form>'
			. $this->RenderSupportingJs()
			. $this->RenderSupportingCss();
		$this->cleanSessionErrorsAfterRender();
		return $result;
	}

	/**
	 * @inheritDocs
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
	 * @inheritDocs
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
