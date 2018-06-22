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

namespace MvcCore\Ext\Forms\Validators;

class Files 
	extends		\MvcCore\Ext\Forms\Validator
	implements	\MvcCore\Ext\Forms\Fields\IMultiple,
				\MvcCore\Ext\Forms\Fields\IAccept
{
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Accept;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		UPLOAD_ERR_OK			=> "There is no error, the file uploaded with success.",	// 0
		UPLOAD_ERR_INI_SIZE		=> "Uploaded file exceeds maximum size to upload. (single file: {1}, all files: {2}).",	// 1
		UPLOAD_ERR_FORM_SIZE	=> "Uploaded file exceeds max. size to upload: {1}.",		// 2
		UPLOAD_ERR_PARTIAL		=> "Uploaded file was only partially uploaded.",			// 3
		UPLOAD_ERR_NO_FILE		=> "No file was uploaded.",									// 4
		UPLOAD_ERR_NO_TMP_DIR	=> "Missing a temporary folder for uploaded file.",			// 6
		UPLOAD_ERR_CANT_WRITE	=> "Failed to write uploaded file to disk.",				// 7
		UPLOAD_ERR_EXTENSION	=> "A PHP extension stopped the file upload.",				// 8
	];

	/**
	 * @var \stdClass[]
	 */
	protected $files = [];

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * Check if given field implements `\MvcCore\Ext\Forms\Fields\IAccept`
	 * and `\MvcCore\Ext\Forms\Fields\IMultiple`.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IAccept) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `Files` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IAccept`.'
			);
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMultiple) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `Files` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IMultiple`.'
			);
		
		if ($this->multiple === NULL && $field->GetMultiple() !== NULL) {
			// if this validator is added into field as instance - check field if it has multiple attribute defined:
			$field->SetMultiple($this->multiple);
		} else if ($this->multiple === NULL && $field->GetMultiple() !== NULL) {
			// if validator is added as string - get multiple property from field:
			$this->multiple = $field->GetMultiple();
		}
		
		if ($this->accept === NULL && $field->GetAccept() !== NULL) {
			// if this validator is added into field as instance - check field if it has accept attribute defined:
			$field->SetAccept($this->accept);
		} else if ($this->accept === NULL && $field->GetAccept() !== NULL) {
			// if validator is added as string - get accept property from field:
			$this->accept = $field->GetAccept();
		}

		return parent::SetField($field);
	}

	/**
	 * Validate raw user input. Parse float value if possible by `Intl` extension 
	 * or try to determinate floating point automaticly and return `float` or `NULL`.
	 * @param string|array	$rawSubmittedValue Raw user input - for this validator always `NULL`.
	 * @return float|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// 1. Complete files array from global `$_FILES` stored in request object:
		$this->completeFiles();
		if (!$this->files) {
			$this->field->AddValidationError(static::GetErrorMessage(UPLOAD_ERR_NO_FILE));
			return NULL;
		}
		foreach ($this->files as $file) {
			// 2. Check errors completed by PHP:
			if ($file->error !== 0) 
				return $this->handlePhpUploadError($file->error);
			// 3. Check if any files is not greater than 2 GB for older PHP versions:

			// 4. Check files by `is_uploaded_file()`:

			// 5. Validate file name max. length 256 and allowed chars in file name, create safe file name:

			// 6. Validate file by allowed mime type if any mime type defined by `finfo_file()`:

			// 7. Validate file by allowed file extension if any file extension defined by previous `pathinfo()` call:

		}

	}

	/**
	 * Complete files array from global `$_FILES` stored in request object.
	 * @return void
	 */
	protected function & completeFiles () {
		$this->files = [];
		$filesFieldItems = $this->form->GetRequest()->GetFile($this->field->GetName());
		if (!$filesFieldItems) return;
		if ($this->multiple) {
			foreach ($filesFieldItems['name'] as $index => $fileName) {
				$this->files[] = (object) [
					'name'			=> $fileName,
					'type'			=> $filesFieldItems['type'][$index],
					'tmpFullPath'	=> $filesFieldItems['tmp_name'][$index],
					'error'			=> $filesFieldItems['error'][$index],
					'size'			=> $filesFieldItems['size'][$index],
				];
			}
		} else {
			$this->files[] = (object) [
				'name'			=> $filesFieldItems['name'],
				'type'			=> $filesFieldItems['type'],
				'tmpFullPath'	=> $filesFieldItems['tmp_name'],
				'error'			=> $filesFieldItems['error'],
				'size'			=> $filesFieldItems['size'],
			];
		}
	}

	/**
	 * TODO: dodělat i logování chyb pro vývojáře i nastavených limitech a upravit error hlášky...
	 * @param int $errorNumber 
	 * @return NULL
	 */
	protected function handlePhpError ($errorNumber) {
		$errorMsgArgs = [];
		if ($errorNumber === UPLOAD_ERR_INI_SIZE) {
			$errorMsgArgs = [static::getPhpIniLimit('upload_max_filesize'), static::getPhpIniLimit('post_max_size')];
		} else {
			$errorMsgArgs = [];
		}
		$this->field->AddValidationError(static::GetErrorMessage((int) $errorNumber), $errorMsgArgs);
		return $this->removeAllTmpFiles();
	}

	/**
	 * Remove all currently uploaded files from PHP temporary directory and return `NULL`.
	 * @return NULL
	 */
	protected function removeAllTmpFiles () {
		foreach ($this->files as & $file) {
			if (file_exists($file->tmpFullPath)) 
				@unlink($file->tmpFullPath);
		}
		return NULL;
	}

	/**
	 * Get PHP data limit as integer value by given ini variable name.
	 * @param string $iniVarName 
	 * @return int|NULL
	 */
	protected static function getPhpIniLimit ($iniVarName) {
		$rawIniValue = ini_get($iniVarName);
		if ($rawIniValue === FALSE) {
			return 0;
		} else if ($rawIniValue === NULL) {
			return NULL;
		}
		$unit = strtoupper(substr($rawIniValue, -1));
		$multiplier = (
			$unit == 'M' 
				? 1048576 
				: ($unit == 'K' 
					? 1024 
					: ($unit == 'G' 
						? 1073741824 
						: 1)));
		return intval($multiplier * floatval($rawIniValue));
	}
}
