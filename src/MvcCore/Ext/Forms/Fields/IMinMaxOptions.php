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

namespace MvcCore\Ext\Forms\Fields;

interface IMinMaxOptions
{
    /**
	 * Get minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMinOptions ();
	
	/**
	 * Set minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param int $minOptions
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMinOptions ($minOptions);

	/**
	 * Get maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMaxOptions ();
	
	/**
	 * Set maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param int $maxOptions
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxOptions ($maxOptions);

	/**
	 * Get minimum options bubble message for javascript.
	 * @return string
	 */
	public function GetMinOptionsBubbleMessage ();

	/**
	 * Set minimum options bubble message for javascript.
	 * @param string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMinOptionsBubbleMessage ($minOptionsBubbleMessage);

	/**
	 * Get maximum options bubble message for javascript.
	 * @return string
	 */
	public function GetMaxOptionsBubbleMessage ();

	/**
	 * Set maximum options bubble message for javascript.
	 * @param string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxOptionsBubbleMessage ($maxOptionsBubbleMessage);
}
