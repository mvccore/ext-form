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
 * @mixin \MvcCore\Ext\Form
 */
trait Rendering {

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function Render ($controllerDashedName = NULL, $actionDashedName = NULL) {
		if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->PreDispatch(FALSE);
		$this->view->SetChildren($this->GetChildren(TRUE), FALSE);
		if ($this->viewScript) {
			$result = $this->view->RenderTemplate();
		} else {
			$result = $this->view->RenderNaturally();
		}
		$this->view->SetChildren([], FALSE); // frees memory
		$this->cleanSessionErrorsAfterRender();
		$this->dispatchState = \MvcCore\IController::DISPATCH_STATE_RENDERED;
		return $result;
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContent () {
		return $this->view->RenderContent();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderErrors () {
		return $this->view->RenderErrors();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderBegin () {
		return $this->view->RenderBegin();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderEnd () {
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
		$nonceCspAttr = static::getSupportingAssetsNonce(FALSE);
		return "<style type=\"text/css\"{$nonceCspAttr}>".$cssFilesContent.'</style>';
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderSupportingJs () {
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
		$nonceCspAttr = static::getSupportingAssetsNonce(TRUE);
		return "<script type=\"text/javascript\"{$nonceCspAttr}>" . $result . '</script>';
	}

	/**
	 * View instance factory method.
	 * @return \MvcCore\View
	 */
	protected function createView () {
		$viewClass = $this->viewClass;
		$view = $viewClass::CreateInstance()
			->SetController($this->parentController)
			->SetForm($this);
		if ($this->viewScript) 
			$view->SetView($this->parentController->GetView());
		return $view;
	}

	/**
	 * Call this function after form has been rendered
	 * to clear session errors, because there is not necessary
	 * to have there those errors anymore, because will be
	 * displayed in rendered form.
	 * @return \MvcCore\Ext\Form
	 */
	protected function cleanSessionErrorsAfterRender () {
		$this->errors = [];
		$session = & $this->getSession();
		$session->errors = [];
		return $this;
	}

	/**
	 * Get inline `<script>` or `<style>` nonce attribute from CSP header if any.
	 * If no CSP header exists or if CSP header exist with no nonce or `strict-dynamic`, 
	 * return an empty string.
	 * @param  bool $js 
	 * @return string
	 */
	protected static function getSupportingAssetsNonce ($js = TRUE) {
		$nonceIndex = $js ? 1 : 0;
		if (self::$assetsNonces[$nonceIndex] !== NULL) 
			return self::$assetsNonces[$nonceIndex] === FALSE
				? ''
				: ' nonce="' . self::$assetsNonces[$nonceIndex] . '"';
		$cspClassFullName = '\\MvcCore\\Ext\\Tools\\Csp';
		if (class_exists($cspClassFullName)) {
			/** @var \MvcCore\Ext\Tools\Csp $csp */
			$assetsNonce = FALSE;
			$csp = $cspClassFullName::GetInstance();
			$defaultScrNonce = $csp->IsAllowedNonce($cspClassFullName::FETCH_DEFAULT_SRC);
			if ((
				$js && ($csp->IsAllowedNonce($cspClassFullName::FETCH_SCRIPT_SRC) || $defaultScrNonce)
			) || (
				!$js && ($csp->IsAllowedNonce($cspClassFullName::FETCH_STYLE_SRC) || $defaultScrNonce)
			)) $assetsNonce = $csp->GetNonce();
			self::$assetsNonces[$nonceIndex] = $assetsNonce;
		} else {
			foreach (headers_list() as $rawHeader) {
				if (!preg_match_all('#^Content\-Security\-Policy\s*:\s*(.*)$#i', trim($rawHeader), $matches)) continue;
				$rawHeaderValue = $matches[1][0];
				$sections = ['script'	=> FALSE, 'style' => FALSE, 'default' => FALSE];
				foreach ($sections as $sectionKey => $sectionValue) 
					if (preg_match_all("#{$sectionKey}\-src\s+(?:[^;]+\s)?\'nonce\-([^']+)\'#i", $rawHeaderValue, $sectionMatches)) 
						$sections[$sectionKey] = $sectionMatches[1][0];
				self::$assetsNonces = [
					$sections['style']  ? $sections['style']  : $sections['default'],
					$sections['script'] ? $sections['script'] : $sections['default']
				];
				break;
			}
		}
		return static::getSupportingAssetsNonce($js);
	}
}
