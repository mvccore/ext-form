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
	 * Absolutize assets path. Every field has cofigured it's supporting css or js file with
	 * absolute path replacement inside file path string by '__MVCCORE_FORM_DIR__'.
	 * Replace now the replacement by prepared properties $form->jsAssetsRootDir or $form->cssAssetsRootDir
	 * to set path into library assets folder by default or to any other user defined paths.
	 * @param string $path
	 * @param string $assetsKey
	 * @return string
	 */
	protected function absolutizeAssetPath ($path = '', $assetsKey = '') {
		$assetsRootDir = $assetsKey == 'js' ? $this->jsAssetsRootDir : $this->cssAssetsRootDir;
		return str_replace(
			array('__MVCCORE_FORM_DIR__', '\\'),
			array($assetsRootDir, '/'),
			$path
		);
	}
	/**
	 * Complete css or js supporting files to add after rendered form
	 * or to add them by external renderer. This function process all
	 * added assets and filter them to add them finally only one by once.
	 * @param string $assetsKey
	 * @return array
	 */
	protected function completeAssets ($assetsKey = '') {
		$files = array();
		$assetsKeyUcFirst = ucfirst($assetsKey);
		foreach ($this->$assetsKeyUcFirst as $item) {
			$files[$this->absolutizeAssetPath($item[0], $assetsKey)] = TRUE;
		}
		$files = array_keys($files);
		foreach ($files as $key => $file) {
			if (isset(static::${$assetsKey}[$file])) {
				unset($files[$key]);
			} else {
				static::${$assetsKey}[$file] = TRUE;
			}
		}
		return array_values($files);
	}
	/**
	 * Render supporting js/css file. Add it after renderer form content or call extenal renderer.
	 * @param string	$content
	 * @param callable	$renderer
	 * @param bool		$loadContent
	 * @param string	$absPath
	 * @return void
	 */
	protected function renderAssetFile (& $content, & $renderer, $loadContent, $absPath) {
		if ($loadContent) {
			$content .= trim(file_get_contents($absPath), "\n\r;") . ';';
		} else {
			call_user_func($renderer, new \SplFileInfo($absPath));
		}
	}
}
