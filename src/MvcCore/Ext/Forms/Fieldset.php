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

namespace MvcCore\Ext\Forms;

class Fieldset implements \MvcCore\Ext\Forms\IFieldset {

	use \MvcCore\Ext\Forms\Fieldset\Props,
		\MvcCore\Ext\Forms\Fieldset\GettersSetters;

	public function __construct (
		array $cfg = [],
		$name = NULL,
		$fieldOrder = NULL,
		$legend = NULL,
		$translateLegend = TRUE,
		$disabled = FALSE,
		array $cssClasses = [],
		$title = NULL,
		$translateTitle = TRUE,
		array $controlAttrs = []
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		foreach ($cfg as $propertyName => $propertyValue) {
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				$this->throwNewInvalidArgumentException(
					'Property `'.$propertyName.'` is not possible '
					.'to configure by constructor `$cfg` param.'
				);
			} else {
				$this->{$propertyName} = $propertyValue;
			}
		}
	}
	
	/**
	 * Consolidate all named constructor params (except first 
	 * agument `$cfg` array) into first agument `$cfg` array.
	 * @param  array $cfg 
	 * @param  array $args 
	 * @param  int   $argsCnt 
	 * @return void
	 */
	protected function consolidateCfg (array & $cfg, array $args, $argsCnt): void {
		if ($argsCnt < 2) return;
		/** @var \ReflectionParameter[] $params */
		$params = (new \ReflectionClass($this))->getConstructor()->getParameters();
		array_shift($params); // remove first `$cfg` param
		array_shift($args);   // remove first `$cfg` param
		/** @var \ReflectionParameter $param */
		foreach ($params as $index => $param) {
			if (
				!isset($args[$index]) ||
				$args[$index] === $param->getDefaultValue()
			) continue;
			$cfg[$param->name] = $args[$index];
		}
	}

	/**
	 * @inheritDocs
	 * @return void
	 */
	public function PreDispatch () {
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function Render () {
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\IForm $form 
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {

		return $this;
	}
}