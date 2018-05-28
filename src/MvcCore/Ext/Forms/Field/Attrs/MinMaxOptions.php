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

namespace MvcCore\Ext\Forms\Field\Attrs;

trait MinMaxOptions
{
	protected $minOptionsCount = 0;
	protected $maxOptionsCount = 0;
	protected $minOptionsBubbleMessage = NULL;
	protected $maxOptionsBubbleMessage = NULL;
	protected $maxOptionsClassName = 'max-selected-options';

	public function SetMinOptionsCount ($minOptionsCount) {
		$this->minOptionsCount = $minOptionsCount;
		return $this;
	}
	public function SetMaxOptionsCount ($maxOptionsCount) {
		$this->maxOptionsCount = $maxOptionsCount;
		return $this;
	}

	public function SetMinOptionsBubbleMessage ($minOptionsBubbleMessage) {
		$this->minOptionsBubbleMessage = $minOptionsBubbleMessage;
		return $this;
	}
	public function SetMaxOptionsBubbleMessage ($maxOptionsBubbleMessage) {
		$this->maxOptionsBubbleMessage = $maxOptionsBubbleMessage;
		return $this;
	}
}
