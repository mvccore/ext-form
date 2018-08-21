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

/**
 * 
 * @see http://php.net/manual/en/features.file-upload.php
 * @see http://php.net/manual/en/features.file-upload.common-pitfalls.php
 */
class Files 
	extends		\MvcCore\Ext\Forms\Validator
	implements	\MvcCore\Ext\Forms\Fields\IMultiple,
				\MvcCore\Ext\Forms\Fields\IFiles
{
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Files;

	const UPLOAD_ERR_NOT_POSTED = 9;
	const UPLOAD_ERR_NOT_FILE = 10;
	const UPLOAD_ERR_EMPTY_FILE = 11;
	const UPLOAD_ERR_TOO_LARGE_FILE = 12;
	const UPLOAD_ERR_UNKNOWN_ACCEPT = 13;
	const UPLOAD_ERR_NO_FILEINFO = 14;
	const UPLOAD_ERR_NO_MIMES_EXTS = 15;
	const UPLOAD_ERR_NOT_ACCEPTED = 16;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		UPLOAD_ERR_OK					=> "There is no error, the file uploaded with success.",				// 0
		UPLOAD_ERR_INI_SIZE				=> "The uploaded file exceeds maximum size to upload. (`{1}` bytes).",	// 1
		/** @bugfix: http://php.net/manual/en/features.file-upload.php#74692 */
		//UPLOAD_ERR_FORM_SIZE			=> "The uploaded file exceeds max. size to upload: `{1}`.",				// 2
		UPLOAD_ERR_PARTIAL				=> "The uploaded file was only partially uploaded.",					// 3
		UPLOAD_ERR_NO_FILE				=> "No file was uploaded.",												// 4
		UPLOAD_ERR_NO_TMP_DIR			=> "Missing a temporary folder for uploaded file.",						// 6
		UPLOAD_ERR_CANT_WRITE			=> "Failed to write uploaded file to disk.",							// 7
		UPLOAD_ERR_EXTENSION			=> "A PHP extension stopped the file upload.",							// 8
		self::UPLOAD_ERR_NOT_POSTED		=> "The file wasn't uploaded via HTTP POST.",							// 9
		self::UPLOAD_ERR_NOT_FILE		=> "The uploaded file is not valid file.",								// 10
		self::UPLOAD_ERR_NOT_FILE		=> "The uploaded file is empty.",										// 11
		self::UPLOAD_ERR_EMPTY_FILE		=> "The uploaded file is too large.",									// 12
		self::UPLOAD_ERR_UNKNOWN_ACCEPT	=> "Unknown accept atribute value found: `{1}`.",						// 13
		self::UPLOAD_ERR_NO_FILEINFO	=> "A PHP function for magic bytes "
											. "recognition is missing (`finfo`).",								// 14
		self::UPLOAD_ERR_NO_MIMES_EXTS	=> "MvcCore extension library to complete accepting mime "
											. "type(s) by file extension(s) not installed "
											. "(`mvccore/ext-form-validator-file-mimes-exts`).",				// 15
		self::UPLOAD_ERR_NOT_ACCEPTED	=> "The uploaded file is not in the expected file format (`{1}`)."
	];

	/**
	 * @var \stdClass[]
	 */
	protected $files = [];

	/**
	 * @var \string[]|NULL
	 */
	protected $mimeTypes = [];

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * Check if given field implements `\MvcCore\Ext\Forms\Fields\IAccept`
	 * and `\MvcCore\Ext\Forms\Fields\IMultiple`.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMultiple) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `Files` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IMultiple`.'
			);
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IFiles) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `Files` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IFiles`.'
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
		
		if ($this->allowedFileNameChars === NULL && $field->GetAllowedFileNameChars() !== NULL) {
			// if this validator is added into field as instance - check field if it has allowedFileNameChars field defined:
			$field->SetAllowedFileNameChars($this->allowedFileNameChars);
		} else if ($this->allowedFileNameChars === NULL && $field->GetAllowedFileNameChars() !== NULL) {
			// if validator is added as string - get allowedFileNameChars property from field:
			$this->allowedFileNameChars = $field->GetAllowedFileNameChars();
		}
		
		return parent::SetField($field);
	}

	/**
	 * Validate `$_FILES` array items storeg in request object. Check if file is valid
	 * uploaded file, sanitize file name and check file mimetype by `finfo` extension by accept attribute values.
	 * Return `NULL` for failure or success result as array with `\stdClass`ses for each file.
	 * @param string|array	$rawSubmittedValue Raw user input - for this validator always `NULL`.
	 * @return float|NULL	Safe submitted files array or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// 1. Complete files array from global `$_FILES` stored in request object:
		if (!$this->completeFiles()) return NULL;
		// 2. Prepare all accept mimetype regular expressions for `finfo_file()` function result.
		if (!$this->readAccept()) return NULL;
		// 3. check if `finfo_file()` function exists. File info extension is 
		// presented from PHP 5.3+ by default, so this error probably never happend.
		if (!function_exists('finfo_file')) return $this->handlePhpError(self::UPLOAD_ERR_NO_FILEINFO);
		foreach ($this->files as $file) {
			// 4. Check errors completed by PHP:
			if ($file->error !== 0) return $this->handlePhpUploadError($file->error);
			// 5. Check file by `is_uploaded_file()`, `is_file()` and by `filesize()`:
			if (!$this->validateValidFileAndFilesSize($file)) return NULL;
			// 6. Sanitize safe file name and sanitize max. file name length:
			$this->validateSanitizeFileName($file);
			// 7. Validate file by allowed mime type if any mime type defined by `finfo_file()`:
			if (!$this->validateAllowedMimeType($file)) return NULL;
			
		}
		return $this->files;
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
					'name'			=> basename($fileName),
					'type'			=> $filesFieldItems['type'][$index],
					'tmpFullPath'	=> $filesFieldItems['tmp_name'][$index],
					'error'			=> $filesFieldItems['error'][$index],
					'size'			=> $filesFieldItems['size'][$index],
				];
			}
		} else {
			$this->files[] = (object) [
				'name'			=> basename($filesFieldItems['name']),
				'type'			=> $filesFieldItems['type'],
				'tmpFullPath'	=> $filesFieldItems['tmp_name'],
				'error'			=> $filesFieldItems['error'],
				'size'			=> $filesFieldItems['size'],
			];
		}
		if ($this->files) return TRUE;
		return $this->handlePhpError(UPLOAD_ERR_NO_FILE);
	}

	/**
	 * Read input file accept atribute value for mimetypes and extension files validation.
	 * @return bool|NULL
	 */
	protected function readAccept () {
		$extensions = [];
		$mimeTypes = [];
		foreach ($this->accept as $rawAccept) {
			$accept = trim($rawAccept);
			if (substr($accept, 0, 1) === '.' && strlen($accept) > 1) {
				$ext = strtolower(substr($accept, 1));
				$extensions[$ext] = 1;
			} else if (preg_match("#^([a-z-]+)/(.*)#", $accept)) {
				// mimes from accept could have strange values like: audio/*;capture=microphone
				$mimeTypeRegExp = $this->readAcceptPrepareMimeTypeRegExp($accept);
				$mimeTypes[$mimeTypeRegExp] = 1;
			} else {
				return $this->handlePhpError(self::UPLOAD_ERR_UNKNOWN_ACCEPT, [$rawAccept]);
			}
		}
		// Get possible mime types for extensions defined by developer 
		// and determinate if file is allowed by mimetype assigned by extension(s):
		if ($mimeTypes && !$extensions) {
			$this->mimeTypes = array_keys($mimeTypes);
			return TRUE;
		}
		$extFormValidatorFileMimesExtsClass = '\\MvcCore\\Ext\\Forms\\Validators\\FilesMimesAndExts';
		if (!class_exists($extFormValidatorFileMimesExtsClass)) 
			return $this->handlePhpUploadError(self::UPLOAD_ERR_NO_MIMES_EXTS);
		foreach ($this->extensions as $extension) {
			$mimeTypesByExt = $extFormValidatorFileMimesExtsClass::GetMimeTypesByExtension($extension);
			foreach ($mimeTypesByExt as $mimeTypeByExt) {
				$mimeTypeRegExp = $this->readAcceptPrepareMimeTypeRegExp($mimeTypeByExt);
				$mimeTypes[$mimeTypeRegExp] = 1;
			}
		}
		$this->mimeTypes = array_keys($mimeTypes);
		return TRUE;
	}

	/**
	 * Prepare regular expression match pattern from mimetype string.
	 * @param string $mimeType 
	 * @return string
	 */
	protected function readAcceptPrepareMimeTypeRegExp ($mimeType) {
		$semiColonPos = strpos($mimeType, ';');
		if ($semiColonPos !== FALSE) $mimeType = substr($mimeType, 0, $semiColonPos);
		// escape all regular expression special characters, 
		// which could be inside correct mimetype string except `*`:
		$mimeType = addcslashes(trim($mimeType), "-.+");
		return '#^' . str_replace('*', '(.*)', $mimeType) . '$#';
	}

	/**
	 * Check file by `is_uploaded_file()`, `is_file()` and by `filesize()`.
	 * @param \stdClass & $file
	 * @return bool|NULL
	 */
	protected function validateValidFileAndFilesSize (& $file) {
		if (!is_uploaded_file($file->tmpFullPath))
			return $this->handlePhpUploadError(self::UPLOAD_ERR_NOT_POSTED);
		if (!is_file($file->tmpFullPath))
			return $this->handlePhpUploadError(self::UPLOAD_ERR_NOT_FILE);
		$fileSize = filesize($file->tmpFullPath);
		if ($fileSize < 1)
			return $this->handlePhpUploadError(self::UPLOAD_ERR_EMPTY_FILE);
		if ($fileSize === FALSE)
			return $this->handlePhpUploadError(self::UPLOAD_ERR_TOO_LARGE_FILE);
		return TRUE;
	}

	/**
	 * Sanitize safe file name and sanitize max. file name length.
	 * @param \stdClass & $file
	 * @return void
	 */
	protected function validateSanitizeFileName (& $file) {
		// Sanitize safe file name:
		$allowedFileNameCharsPattern = '#[^' 
			. addcslashes($$this->allowedFileNameChars, "#[](){}<>?!=^$.+|:\\") 
		. ']#';
		$file->name = preg_replace($allowedFileNameCharsPattern, '', $file->name);
		// Sanitize max. file name length:
		$pathInfo = pathinfo($file->name);
		if (mb_strlen($file->name) > 255) {
			$extension = mb_strtolower($pathInfo['extension']);
			$extensionLength = mb_strlen($extension);
			if ($extensionLength > 0) {
				$fileName = basename($file->name, '.' . $extension);
				$file->name = mb_substr($fileName, 0, 255 - 1 - $extensionLength) . '.' . $extension;
			} else {
				$file->name = mb_substr($file->name, 0, 255);
			}
		}
	}

	/**
	 * Validate file by allowed mime type if any mime type defined by `finfo_file()`
	 * @param \stdClass & $file
	 * @return bool|NULL
	 */
	protected function validateAllowedMimeType (& $file) {
		$allowed = FALSE;
		$finfo = finfo_open(FILEINFO_MIME);
		$fileMimeType = @finfo_file($finfo, $file->tmpFullPath);
		finfo_close($finfo);
		if ($this->mimeTypes) {
			foreach ($this->mimeTypes as $mimeTypePattern) {
				if (preg_match($mimeTypePattern, $fileMimeType)) {
					$allowed = TRUE;
					break;
				}
			}
		}
		if (!$allowed) 
			return $this->handlePhpUploadError(self::UPLOAD_ERR_NOT_ACCEPTED, [$file->name]);
		return TRUE;
	}

	/**
	 * Add error message arguments for specific PHP build-in errors,
	 * add error message into form session namespace, remove all tmp files and return NULL.
	 * @see http://php.net/manual/en/features.file-upload.php
	 * @see http://php.net/manual/en/features.file-upload.common-pitfalls.php
	 * @param int   $errorNumber
	 * @param array $errorMsgArgs
	 * @return NULL
	 */
	protected function handlePhpError ($errorNumber, $errorMsgArgs = []) {
		if ($errorNumber === UPLOAD_ERR_INI_SIZE) {
			$form = $this->form;
			// `post_max_size` is always handled at submit process begin.
			$errorMsgArgs = [
				$form::GetPhpIniSizeLimit('upload_max_filesize')
			];
		}
		$this->field->AddValidationError(
			static::GetErrorMessage((int) $errorNumber), 
			$errorMsgArgs
		);
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
}
