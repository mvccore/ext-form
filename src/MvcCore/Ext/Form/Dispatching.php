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
 * Trait for class `MvcCore\Ext\Form` with dispatching methods.
 * @mixin \MvcCore\Ext\Form
 */
trait Dispatching {

	/**
	 * Create new `\MvcCore\Ext\Form` instance.
	 * Please don't forget to configure at least `$form->SetId()`, `$form->SetAction()`,
	 * any control to work with and finally any `button:submit` or `input:submit`
	 * to submit the form to any URL defined in `$form->action`.
	 * @param  \MvcCore\Controller|NULL $controller Controller instance, where the form is created.
	 * @return void
	 */
	public function __construct (/*\MvcCore\Controller*/ $controller = NULL) {
		/** @var \MvcCore\Controller $controller */
		if ($controller !== NULL) {
			if (!($controller instanceof \MvcCore\IController))
				throw new \Exception("[" . get_class($this) . "] Controller doesn't implement \MvcCore\IController interface.");
		} else {
			$controller = \MvcCore\Ext\Form::GetCallerControllerInstance();
			if ($controller === NULL) 
				$controller = \MvcCore\Application::GetInstance()->GetController();
			if ($controller === NULL) $this->throwNewInvalidArgumentException(
				'There was not possible to determinate caller controller, '
				.'where is form instance created. Provide `$controller` instance explicitly '
				.'by first `\MvcCore\Ext\Form::__construct($controller);` argument.'
			);
		}
		$controller->AddChildController($this, $this->id);
		$this->controller = $controller;
		$this->intlExtLoaded = extension_loaded('intl');
		if (static::$jsSupportFilesRootDir === NULL || static::$cssSupportFilesRootDir === NULL) {
			$toolClass = $this->application->GetToolClass();
			$baseAssetsPath = str_replace('\\', '/', $toolClass::RealPathVirtual(__DIR__ . '/..')) . '/Forms/assets';
			if (static::$jsSupportFilesRootDir === NULL)
				static::$jsSupportFilesRootDir = $baseAssetsPath;
			if (static::$cssSupportFilesRootDir === NULL)
				static::$cssSupportFilesRootDir = $baseAssetsPath;
		}
		$this->sorting = (object) $this->sorting;
		$app = $this->application;
		if (self::$sessionClass === NULL)
			self::$sessionClass = $app->GetSessionClass();
		if (self::$toolClass === NULL)
			self::$toolClass = $app->GetToolClass();
		$this->csrfEnabled = (
			($app->GetCsrfProtection() & \MvcCore\IApplication::CSRF_PROTECTION_FORM_INPUT) != 0
		);
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @param  int $state 
	 * Dispatch state, that is required to be completed. Possible values are:
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_CREATED`,
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_INITIALIZED`,
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_PRE_DISPATCHED`,
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_SUBMITTED`,
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED`,
	 * - `\MvcCore\Ext\Form::DISPATCH_STATE_TERMINATED`.
	 * @param  bool $submit
	 * Submit boolean from `Init($submit)` or `PreDispatch($submit)` method.
	 * `FALSE` by default.
	 * @return bool
	 */
	public function DispatchStateCheck ($state, $submit = FALSE) {
		if ($this->dispatchState >= $state) 
			return FALSE;
		if ($this->dispatchStateSemaphore)
			return TRUE;
		// here is always `$this->dispatchState < $state`:
		$this->dispatchStateSemaphore = TRUE;

		if ($submit === NULL && $this->submit === NULL)
			$this->submit = $this->initDetectSubmit();
		
		if ($state > static::DISPATCH_STATE_INITIALIZED)
			$this->dispatchMethods(
				$this, 'Init', 
				static::DISPATCH_STATE_INITIALIZED, FALSE
			);

		if ($state > static::DISPATCH_STATE_PRE_DISPATCHED)
			$this->dispatchMethods(
				$this, 'PreDispatch', 
				static::DISPATCH_STATE_PRE_DISPATCHED, FALSE
			);

		if ($state > static::DISPATCH_STATE_RENDERED)
			$this->dispatchRender();

		$this->dispatchStateSemaphore = FALSE;
		return TRUE;
	}
	
	/**
	 * Execute given controller method and move dispatch state.
	 * This method doesn't check if method exists on given controller context.
	 * @param  \MvcCore\IController $controller 
	 * Any level controller context.
	 * @param  string               $methodName 
	 * Controller public method name, possible values are:
	 * - `Init`,
	 * - `<action>Init`,
	 * - `PreDispatch`,
	 * - `<action>Action`.
	 * @param  int                  $targetDispatchState 
	 * Dispatch state, that is required to be completed. Possible values are:
	 * - `\MvcCore\IController::DISPATCH_STATE_INITIALIZED`,
	 * - `\MvcCore\IController::DISPATCH_STATE_ACTION_INITIALIZED`,
	 * - `\MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED`,
	 * - `\MvcCore\IController::DISPATCH_STATE_ACTION_EXECUTED`.
	 * @return void
	 */
	protected function dispatchMethod (\MvcCore\IController $controller, $methodName, $targetDispatchState) {
		if ($targetDispatchState === static::DISPATCH_STATE_ACTION_EXECUTED) {
			// This dispatch state is exceptional and it's necessary to set it before execution:
			$controller->dispatchMoveState($targetDispatchState);
		}
		$controller->{$methodName}($this->submit);
		if ($targetDispatchState !== static::DISPATCH_STATE_ACTION_EXECUTED) {
			// For cases somebody forget to call parent action method:
			$controller->dispatchMoveState($targetDispatchState);
		}
	}
	
	/**
	 * Throw new `\InvalidArgumentException` with given
	 * error message and append automatically current class name,
	 * current form id and form class type.
	 * @param  string $errorMsg 
	 * @throws \InvalidArgumentException 
	 */
	protected function throwNewInvalidArgumentException ($errorMsg) {
		$str = '['.get_class($this).'] ' . $errorMsg . ' ('
			. 'form id: `'.$this->id . '`, '
			. 'form type: `'.get_class($this->form).'`'
		.')';
		throw new \InvalidArgumentException($str);
	}

	/**
	 * @inheritDoc
	 * @param  bool $submit      `TRUE` if form is submitting, `FALSE` otherwise by default.
	 * @throws \RuntimeException No form id property defined or Form id `...` already defined.
	 * @return void
	 */
	public function Init ($submit = FALSE) {
		if (!$this->DispatchStateCheck(static::DISPATCH_STATE_INITIALIZED, $submit))
			return;
		parent::Init();
		if ($this->id === NULL)
			throw new \RuntimeException("No form `id` property defined in `".get_class($this)."`.");
		if (isset(self::$instances[$this->id])) {
			$anotherForm = self::$instances[$this->id];
			if ($anotherForm !== $this)
				throw new \RuntimeException("Form id `".$this->id."` already defined.");
		} else {
			self::$instances[$this->id] = TRUE;
		}
		$this->translate = $this->translator !== NULL && is_callable($this->translator);
		if ($this->submit === NULL)
			$this->submit = $this->initDetectSubmit();
		$this->dispatchMoveState(static::DISPATCH_STATE_INITIALIZED);
	}

	/**
	 * Detect if currently requested URL matches form `action` attribute 
	 * with any trailing request query params or if any submit button 
	 * `formAction` attribute matches form `action` attribute also with any 
	 * trailing request query params.
	 * Return `TRUE` for matched submit request, `FALSE` otherwise.
	 * To detect form submit again, switch `$form->submit` property into `NULL`.
	 * @return bool
	 */
	protected function initDetectSubmit () {
		if ($this->submit !== NULL) return $this->submit;
		$submitPoints = [[$this->action, $this->method]];
		foreach ($this->submitFields as $submitField) {
			/** @var $submitField \MvcCore\Ext\Forms\Fields\IFormAttrs */
			$formAction = $submitField->GetFormAction();
			$formMethod = $submitField->GetFormMethod();
			if ($formAction !== NULL || $formMethod !== NULL) {
				$submitPoints[] = [
					$formAction ?: $this->action, $formMethod ?: $this->method
				];
			}
		}
		$req = $this->request;
		$reqMethod = $req->GetMethod();
		$reqScheme = NULL;
		$reqHost = NULL;
		$reqPort = NULL;
		$reqPath = NULL;
		$reqParams = NULL;
		foreach ($submitPoints as $submitPoint) {
			list ($formAction, $formMethod) = $submitPoint;
			if ($formMethod !== $reqMethod) continue;
			$actionUrl = \MvcCore\Tool::ParseUrl($this->action);
			if (isset($actionUrl['scheme'])) {
				if ($reqScheme === NULL) $reqScheme = $req->GetScheme();
				if ($actionUrl['scheme'] !== $reqScheme) continue;
			}
			if (isset($actionUrl['host'])) {
				if ($reqHost === NULL) $reqHost = $req->GetHostName();
				if ($actionUrl['host'] !== $reqHost) continue;
			}
			if (isset($actionUrl['port'])) {
				$reqPort = $req->GetPort();
				if (strval($actionUrl['port']) !== $reqPort) continue;
			}
			if (isset($actionUrl['path'])) {
				$actionPath = $actionUrl['path'];
				if ($reqPath === NULL) $reqPath = $req->GetBasePath() . $req->GetPath();
				$scriptName = $req->GetScriptName();
				$scriptNameLen = mb_strlen($scriptName);
				$actionPathScriptEndingPos = mb_strlen($actionPath) - $scriptNameLen;
				$actionPathScriptEnding = mb_strpos($actionPath, $scriptName) === $actionPathScriptEndingPos;
				$reqPathScriptEndingPos = mb_strlen($reqPath) - $scriptNameLen;
				$reqPathScriptEnding = mb_strpos($reqPath, $scriptName) === $reqPathScriptEndingPos;
				$reqPath2Compare = $actionPathScriptEnding && !$reqPathScriptEnding
					? rtrim($reqPath, '/') . $scriptName
					: $reqPath;
				if ($actionPath !== $reqPath2Compare) continue;
			}
			if (isset($actionUrl['query'])) {
				if ($reqParams === NULL)
					$reqParams = $req->GetParams(
						FALSE, [], 
						$req::PARAM_TYPE_QUERY_STRING | 
						$req::PARAM_TYPE_URL_REWRITE
					);
				$actionParams = [];
				parse_str(trim($req::HtmlSpecialChars($actionUrl['query']), '&='), $actionParams);
				if ($actionParams === NULL)
					$actionParams = [];
				$allParamsMatched = TRUE;
				foreach ($actionParams as $actionParamName => $actionParamValue) {
					$actionParamValueEncoded = rawurlencode($actionParamValue);
					if (
						!array_key_exists($actionParamName, $reqParams) ||
						rawurlencode($reqParams[$actionParamName]) !== $actionParamValueEncoded
					) {
						$allParamsMatched = FALSE;
						break;
					}
				}
				if (!$allParamsMatched) continue;
			}
			$this->submit = TRUE;
			break;
		}
		if ($this->submit === NULL)
			$this->submit = FALSE;
		return $this->submit;
	}

	/**
	 * @inheritDoc
	 * @param  bool $submit `TRUE` if form is submitting, `FALSE` otherwise by default.
	 * @return void
	 */
	public function PreDispatch ($submit = FALSE) {
		if (!$this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $submit))
			return;
		$this->viewEnabled = !$submit;
		parent::PreDispatch();
		
		$this->SortChildren();

		$session = & $this->getSession();
		$this->preDispatchLoadErrors($session);
		$this->preDispatchLoadValues($session);

		if ($this->csrfEnabled)
			$this->SetUpCsrf();
		
		if (!$submit) {
			foreach ($this->fieldsets as $fieldset)
				// translate fieldsets if necessary and do any rendering preparation stuff
				$fieldset->PreDispatch();

			foreach ($this->fields as $field) 
				// translate fields if necessary and do any rendering preparation stuff
				$field->PreDispatch();
		
			if ($this->translateTitle === NULL)
				$this->translateTitle = $this->translate;
			if ($this->translate && $this->translateTitle && $this->title !== NULL)
				$this->title = $this->Translate($this->title);
		
			$this->view = $this->createView(TRUE);
		}
		
		$this->dispatchMoveState(static::DISPATCH_STATE_PRE_DISPATCHED);
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Form
	 */
	public function SortChildren () {
		if ($this->sorting->sorted)
			return $this;
		if (count($this->sorting->numbered) > 0) {
			$naturallySortedNames = & $this->sorting->naturally;
			ksort($this->sorting->numbered);
			foreach ($this->sorting->numbered as $fieldOrderNumber => $numberSortedNames) 
				array_splice($naturallySortedNames, $fieldOrderNumber, 0, $numberSortedNames);
			$this->sorting->numbered = [];
			$fields = [];
			$fieldsets = [];
			$children = [];
			foreach ($naturallySortedNames as $childName) {
				if (isset($this->fields[$childName])) {
					/** @var \MvcCore\Ext\Forms\Field $field */
					$field = $this->fields[$childName];
					$fields[$childName] = $field;
					$children[$childName] = $field;
				} else if (isset($this->fieldsets[$childName])) {
					/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
					$fieldset = $this->fieldsets[$childName];
					$fieldsets[$childName] = $fieldset;
					$children[$childName] = $fieldset;
					$this->sortChildrenRecursive(
						$fieldset, $fields, $fieldsets
					);
				}
			}
			if (count($fields) !== count($this->fields)) {
				$missingNames = implode("`,`", array_diff(array_keys($fields), array_keys($this->fields)));
				throw new \RuntimeException(
					"[".get_class($this)."] Some fields are not connected with form instance, ".
					"form id: `{$this->id}`, fields: `{$missingNames}`."
				);
			}
			if (count($fieldsets) !== count($this->fieldsets)) {
				$missingNames = implode("`,`", array_diff(array_keys($fieldsets), array_keys($this->fieldsets)));
				throw new \RuntimeException(
					"[".get_class($this)."] Some fieldsets are not connected with form instance, ".
					"form id: `{$this->id}`, fieldsets: `{$missingNames}`."
				);
			}
			$this->fields = $fields;
			$this->fieldsets = $fieldsets;
			$this->children = $children;
		}
		$this->sorting->sorted = TRUE;
		return $this;
	}

	/**
	 * Complete recursively sorted form fields and form fieldsets.
	 * @param  \MvcCore\Ext\Forms\Fieldset   $fieldset 
	 * @param  \MvcCore\Ext\Forms\Field[]    $fields 
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets 
	 * @return void
	 */
	protected function sortChildrenRecursive (\MvcCore\Ext\Forms\IFieldset $fieldset, & $fields, & $fieldsets) {
		foreach ($fieldset->GetChildren(TRUE) as $childName => $child)  {
			if ($child instanceof \MvcCore\Ext\Forms\Field) {
				/** @var \MvcCore\Ext\Forms\Field $field */
				$fields[$childName] = $child;
			} else if ($child instanceof \MvcCore\Ext\Forms\Fieldset) {
				/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
				$fieldsets[$childName] = $child;
				$this->sortChildrenRecursive(
					$child, $fields, $fieldsets
				);
			}
		}
	}
	
	/**
	 * Initialize form errors from session for all fields in `PreDispatch()`
	 * lifecycle moment or earlier from method `GetValues()`.
	 * @param  \MvcCore\Session $session 
	 * @return void
	 */
	protected function preDispatchLoadErrors (\MvcCore\ISession $session) {
		if (!$session->errors) return;
		$this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $this->submit);
		foreach ($session->errors as $errorMsgAndFieldNames) {
			list($errorMsg, $fieldNames) = array_merge([], $errorMsgAndFieldNames);
			$this->AddError($errorMsg, $fieldNames);
		}
	}

	/**
	 * Initialize values from session for all fields in `PreDispatch()`
	 * lifecycle moment or earlier from method `GetValues()`.
	 * @param  \MvcCore\Session $session 
	 * @return void
	 */
	protected function preDispatchLoadValues (\MvcCore\ISession $session) {
		if (!$session->values) return;
		$this->DispatchStateCheck(static::DISPATCH_STATE_PRE_DISPATCHED, $this->submit);
		foreach ($session->values as $fieldName => $fieldValue) {
			if (!array_key_exists($fieldName, $this->fields)) continue;
			$field = $this->fields[$fieldName];
			$configuredFieldValue = $field->GetValue();
			$multiple = FALSE;
			if ($field instanceof \MvcCore\Ext\Forms\Fields\IMultiple)
				$multiple = $field->GetMultiple() ?: FALSE;
			if (
				$configuredFieldValue === NULL || 
				($multiple && is_array($configuredFieldValue) && count($configuredFieldValue) === 0)
			) {
				$this->values[$fieldName] = $fieldValue;
				$field->SetValue($fieldValue);
			}
		}
	}

}