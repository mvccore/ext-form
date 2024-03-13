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
	 * @inheritDoc
	 * @return string
	 */
	public function Render ($controllerDashedName = NULL, $actionDashedName = NULL) {
		$this->DispatchStateCheck(static::DISPATCH_STATE_RENDERED, $this->submit);
		$this->view->SetChildren($this->GetChildren(TRUE), FALSE);
		if ($this->viewScript) {
			$result = $this->view->RenderTemplate();
		} else {
			$result = $this->view->RenderNaturally();
		}
		$this->view->SetChildren([], FALSE); // frees memory
		$this->cleanSessionErrorsAfterRender();
		$this->dispatchMoveState(static::DISPATCH_STATE_RENDERED);
		return $result;
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderContent () {
		$this->DispatchStateCheck(static::DISPATCH_STATE_RENDERED, $this->submit);
		return $this->view->RenderContent();
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderErrors () {
		$this->DispatchStateCheck(static::DISPATCH_STATE_RENDERED, $this->submit);
		if (!$this->view->GetChildren())
			$this->view->SetChildren($this->GetChildren(TRUE), FALSE);
		return $this->view->RenderErrors();
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderBegin () {
		$this->DispatchStateCheck(static::DISPATCH_STATE_RENDERED, $this->submit);
		if (!$this->view->GetChildren())
			$this->view->SetChildren($this->GetChildren(TRUE), FALSE);
		return $this->view->RenderBegin();
		
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderEnd () {
		$this->DispatchStateCheck(static::DISPATCH_STATE_RENDERED, $this->submit);
		$this->view->SetChildren([], FALSE); // frees memory
		$this->SetFormTagRenderingStatus(FALSE);
		$result = '</form>'
			. $this->RenderSupportingJs()
			. $this->RenderSupportingCss();
		$this->cleanSessionErrorsAfterRender();
		$this->dispatchMoveState(static::DISPATCH_STATE_RENDERED);
		return $result;
	}

	/**
	 * @inheritDoc
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
		$nonceCspAttr = static::getSupportingAssetsNonce($this->response, FALSE);
		return "<style type=\"text/css\"{$nonceCspAttr}>".$cssFilesContent.'</style>';
	}

	/**
	 * @inheritDoc
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
		$nonceCspAttr = static::getSupportingAssetsNonce($this->response, TRUE);
		return "<script type=\"text/javascript\"{$nonceCspAttr}>" . $result . '</script>';
	}
	
	/**
	 * @inheritDoc
	 * @param  string $key          A key to translate.
	 * @param  array  $replacements An array of replacements to process in translated result.
	 * @throws \Exception           An exception if translations store is not successful.
	 * @return string               Translated key or key itself if there is no key in translations store.
	 */
	public function Translate ($key, $replacements = []) {
		return call_user_func_array($this->translator, func_get_args());
	}

	/**
	 * View instance factory method.
	 * @param  bool $actionView
	 * @return \MvcCore\View
	 */
	protected function createView ($actionView = TRUE) {
		$viewClass = $this->viewClass;
		$view = $viewClass::CreateInstance()
			->SetController($this->parentController)
			->SetEncoding($this->response->GetEncoding())
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
	 * @param  \MvcCore\IResponse $res
	 * @param  bool               $js 
	 * @return string
	 */
	protected static function getSupportingAssetsNonce (\MvcCore\IResponse $res, $js = TRUE) {
		$nonceIndex = $js ? 1 : 0;
		if (self::$assetsNonces[$nonceIndex] !== NULL) 
			return self::$assetsNonces[$nonceIndex] === FALSE
				? ''
				: ' nonce="' . self::$assetsNonces[$nonceIndex] . '"';
		$cspClassFullName = static::$cspClassFullName;
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
			$rawHeaderValues = $res->GetHeader('Content-Security-Policy');
			$rawHeaderValues = is_array($rawHeaderValues) 
				? array_map('trim', $rawHeaderValues) 
				: [trim($rawHeaderValues)];
			$sections = ['script'	=> FALSE, 'style' => FALSE, 'default' => FALSE];
			foreach ($rawHeaderValues as $rawHeaderValue)
				foreach ($sections as $sectionKey => $sectionValue) 
					if (preg_match_all("#{$sectionKey}\-src\s+(?:[^;]+\s)?\'nonce\-([^']+)\'#i", $rawHeaderValue, $sectionMatches)) 
						$sections[$sectionKey] = $sectionMatches[1][0];
			self::$assetsNonces = [
				$sections['style']  ? $sections['style']  : $sections['default'],
				$sections['script'] ? $sections['script'] : $sections['default']
			];
		}
		return static::getSupportingAssetsNonce($res, $js);
	}

}
