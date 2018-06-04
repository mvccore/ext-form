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

trait Assets
{
	/**
	 * Complete JS/CSS supporting file(s) to add them after rendered `<form>` element
	 * or to add them into response by external renderer. This function processes all
	 * assets necessary to and and filters them for asset files aready added into response.
	 * @param bool $javascriptFiles `TRUE` to complete supporting files from `$form->jsSupportFiles`, `FALSE` to complete them from `$form->cssSupportFiles`.
	 * @return array
	 */
	protected function completeSupportingFilesToRender ($javascriptFiles = TRUE) {
		$files = [];
		if ($javascriptFiles) {
			$instanceCollection = & $this->jsSupportFiles;
			$staticCollection = & self::$allJsSupportFiles;
		} else {
			$instanceCollection = & $this->cssSupportFiles;
			$staticCollection = & self::$allCssSupportFiles;
		}
		foreach ($instanceCollection as $item) {
			$absoluteSupportingFilePath = static::absolutizeSupportingFilePath($item[0], $javascriptFiles);
			$files[$absoluteSupportingFilePath] = TRUE;
		}
		$files = array_keys($files);
		foreach ($files as $key => $file) {
			if (isset($staticCollection[$file])) {
				unset($files[$key]);
			} else {
				$staticCollection[$file] = TRUE;
			}
		}
		return array_values($files);
	}

	/**
	 * Absolutize supporting JS/CSS relative file path. Every field has cofigured 
	 * it's supporting css or js file with `absolute path replacement` inside supporting 
	 * file path string by `__MVCCORE_FORM_ASSETS_DIR__` substring.
	 * Replace now the replacement substring by prepared properties values 
	 * `$form->jsAssetsRootDir` or `$form->cssAssetsRootDir` to set path into 
	 * library assets folder by default or to set path into any other customized defined directory.
	 * @param string $supportingFileRelPath Supporting file relative path with `__MVCCORE_FORM_ASSETS_DIR__` replacement substring.
	 * @param string $javascriptFiles `TRUE` to complete supporting files from `$form->jsSupportFiles`, `FALSE` to complete them from `$form->cssSupportFiles`.
	 * @return string Return absolute path to suporting javascript.
	 */
	protected static function absolutizeSupportingFilePath ($supportingFileRelPath = '', $javascriptFiles = TRUE) {
		$assetsRootDir = $javascriptFiles 
			? static::$jsSupportFilesRootDir 
			: static::$cssSupportFilesRootDir;
		return str_replace(
			[\MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT, '\\'],
			[$assetsRootDir, '/'],
			$supportingFileRelPath
		);
	}

	/**
	 * Render supporting js/css file. Add it's content after given first 
	 * argument string `$content` or call extenal renderer handler.
	 * @param string	$content				HTML content code with rendered supporting JS/CSS files.
	 * @param string	$absolutePath			Absolute path to supporting JS/CSS file.
	 * @param bool		$useExternalRenderer	`TRUE` to use any external cofngired renderer `callable`, default `FALSE`.
	 * @param callable	$rendererHandler		External renderer `callable` accepting first argument as `\SplFileInfo` about supporting JS/CSS file.
	 * @return void
	 */
	protected function renderSupportingFile (
		& $content, 
		& $absolutePath, 
		$useExternalRenderer = FALSE, 
		& $rendererHandler = NULL
	) {
		if ($useExternalRenderer) {
			call_user_func($rendererHandler, new \SplFileInfo($absolutePath));
		} else {
			$content .= trim(file_get_contents($absolutePath), "\n\r;") . ';';
		}
	}
}
