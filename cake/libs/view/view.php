<?php
/* SVN FILE: $Id: view.php,v 1.2 2006/11/07 20:19:51 walambre Exp $ */

/**
 * Methods for displaying presentation data in the view.
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.view
 * @since			CakePHP v 0.10.0.1076
 * @version			$Revision: 1.2 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006/11/07 20:19:51 $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Included libraries.
 */
uses (DS . 'view' . DS . 'helper');

/**
 * View, the V in the MVC triad.
 *
 * Class holding methods for displaying presentation data.
 *
 * @package			cake
 * @subpackage		cake.cake.libs.view
 */
class View extends Object{
/**
 * Name of the controller.
 *
 * @var string Name of controller
 * @access public
 */
	var $name = null;

/**
 * Stores the current URL (for links etc.)
 *
 * @var string Current URL
 */
	var $here = null;

/**
 * Not used. 2006-09
 *
 * @var unknown_type
 * @access public
 */
	var $parent = null;

/**
 * Action to be performed.
 *
 * @var string Name of action
 * @access public
 */
	var $action = null;

/**
 * An array of names of models the particular controller wants to use.
 *
 * @var mixed A single name as a string or a list of names as an array.
 * @access protected
 */
	var $uses = false;

/**
 * An array of names of built-in helpers to include.
 *
 * @var mixed A single name as a string or a list of names as an array.
 * @access protected
 */
	var $helpers = array('Html');

/**
 * Path to View.
 *
 * @var string Path to View
 */
	var $viewPath;

/**
 * Variables for the view
 *
 * @var array
 * @access private
 */
	var $_viewVars = array();

/**
 * Title HTML element of this View.
 *
 * @var boolean
 * @access private
 */
	var $pageTitle = false;

/**
 * An array of model objects.
 *
 * @var array Array of model objects.
 * @access public
 */
	var $models = array();

/**
 * Path parts for creating links in views.
 *
 * @var string Base URL
 * @access public
 */
	var $base = null;

/**
 * Name of layout to use with this View.
 *
 * @var string
 * @access public
 */
	var $layout = 'default';

/**
 * Turns on or off Cake's conventional mode of rendering views. On by default.
 *
 * @var boolean
 * @access public
 */
	var $autoRender = true;

/**
 * Turns on or off Cake's conventional mode of finding layout files. On by default.
 *
 * @var boolean
 * @access public
 */
	var $autoLayout = true;

/**
 * Array of parameter data
 *
 * @var array Parameter data
 */
	var $params;
/**
 * True when the view has been rendered.
 *
 * @var boolean
 */
	var $hasRendered = null;

/**
 * Reference to the Controller for this view.
 *
 * @var Controller
 */
	var $controller = null;

/**
 * Array of loaded view helpers.
 *
 * @var array
 */
	var $loaded = array();

/**
 * File extension. Defaults to Cake's conventional ".thtml".
 *
 * @var array
 */
	var $ext = '.thtml';

/**
 * Sub-directory for this view file.
 *
 * @var string
 */
	var $subDir = null;

/**
 * Enter description here... Themes. New in Cake RC4.
 *
 * @var array
 */
	var $themeWeb = null;

/**
 * Plugin name. A Plugin is a sub-application. New in Cake RC4.
 *
 * @link http://wiki.cakephp.org/docs:plugins
 * @var string
 */
	var $plugin = null;

/**
/**
 * List of variables to collect from the associated controller
 *
 * @var array
 * @access protected
 */
	var $__passedVars = array('_viewVars', 'action', 'autoLayout', 'autoRender', 'ext', 'base', 'webroot', 'helpers', 'here', 'layout', 'modelNames', 'name', 'pageTitle', 'viewPath', 'params', 'data', 'webservices', 'plugin');
/**
 * Constructor
 *
 * @return View
 */
	function __construct(&$controller) {
		if(is_object($controller)) {
			$this->controller   =& $controller;

			$c = count($this->__passedVars);
			for ($j = 0; $j < $c; $j++) {
				$var = $this->__passedVars[$j];
				$this->{$var} = $controller->{$var};
			}
		}
		parent::__construct();
	}

/**
 * Renders view for given action and layout. If $file is given, that is used
 * for a view filename (e.g. customFunkyView.thtml).
 *
 * @param string $action Name of action to render for
 * @param string $layout Layout to use
 * @param string $file Custom filename for view
 */
	function render($action = null, $layout = null, $file = null) {

		if (isset($this->hasRendered) && $this->hasRendered) {
			return true;
		} else {
			$this->hasRendered = false;
		}

		if (!$action) {
			$action = $this->action;
		}

		if ($layout) {
			$this->setLayout($layout);
		}

		if ($file) {
			$viewFileName = $file;
		} else {
			$viewFileName = $this->_getViewFileName($action);
		}

		if (!is_null($this->plugin) && is_null($file)) {
			return $this->pluginView($action, $layout);
		}

		if (!is_file($viewFileName) && !fileExistsInPath($viewFileName) || $viewFileName === '/' || $viewFileName === '\\') {
			if (strpos($action, 'missingAction') !== false) {
				$errorAction = 'missingAction';
			} else {
				$errorAction = 'missingView';
			}

			foreach(array($this->name, 'errors') as $viewDir) {
				$errorAction = Inflector::underscore($errorAction);

				if (file_exists(VIEWS . $viewDir . DS . $errorAction . $this->ext)) {
					$missingViewFileName = VIEWS . $viewDir . DS . $errorAction . $this->ext;
				} elseif($missingViewFileName = fileExistsInPath(LIBS . 'view' . DS . 'templates' . DS . $viewDir . DS . $errorAction . '.thtml')) {
				} else {
					$missingViewFileName = false;
				}

				$missingViewExists = is_file($missingViewFileName);

				if ($missingViewExists) {
					break;
				}
			}

			if (strpos($action, 'missingView') === false) {
				return $this->cakeError('missingView', array(array('className' => $this->controller->name,
							'action' => $action,
							'file' => $viewFileName,
							'base' => $this->base
				)));

				$isFatal = isset($this->isFatal) ? $this->isFatal : false;

				if (!$isFatal) {
					$viewFileName = $missingViewFileName;
				}
			} else {
				$missingViewExists = false;
			}

			if (!$missingViewExists || $isFatal) {
				if (DEBUG) {
					trigger_error(sprintf(__("No template file for view %s (expected %s), create it first'"), $action, $viewFileName), E_USER_ERROR);
				} else {
					$this->error('404', 'Not found', sprintf("The requested address %s was not found on this server.", '', "missing view \"{$action}\""));
				}
				die();
			}
		}

		if ($viewFileName && !$this->hasRendered) {
			if (substr($viewFileName, -5) === 'thtml') {
				$out = View::_render($viewFileName, $this->_viewVars);
			} else {
				$out = $this->_render($viewFileName, $this->_viewVars);
			}

			if ($out !== false) {
				if ($this->layout && $this->autoLayout) {
					$out = $this->renderLayout($out);
				}

				print $out;
				$this->hasRendered = true;
			} else {
				$out = $this->_render($viewFileName, $this->_viewVars);
				trigger_error(sprintf(__("Error in view %s, got: <blockquote>%s</blockquote>"), $viewFileName, $out), E_USER_ERROR);
			}
			return true;
		}
	}
/**
 * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
 *
 * This realizes the concept of Elements, (or "partial layouts")
 * and the $params array is used to send data to be used in the
 * Element.
 *
 * @link
 * @param string $name Name of template file in the/app/views/elements/ folder
 * @param array $params Array of data to be made available to the for rendered view (i.e. the Element)
 * @return string Rendered output
 */
	function renderElement($name, $params = array()) {
		$params = array_merge_recursive($params, $this->loaded);

		if(isset($params['plugin'])) {
			$this->plugin = $params['plugin'];
		}

		if (!is_null($this->plugin)) {
			if (file_exists(APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . 'elements' . DS . $name . $this->ext)) {
				$elementFileName = APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . 'elements' . DS . $name . $this->ext;
				return $this->_render($elementFileName, array_merge($this->_viewVars, $params), false);
			}
		}

		$paths = Configure::getInstance();
		foreach($paths->viewPaths as $path) {
			if (file_exists($path . 'elements' . DS . $name . $this->ext)) {
				$elementFileName = $path . 'elements' . DS . $name . $this->ext;
				return $this->_render($elementFileName, array_merge($this->_viewVars, $params), false);
			}
		}
		return "(Error rendering Element: {$name})";
	}

	function element($name) {
		return ELEMENTS . $name . $this->ext;
	}

/**
 * Renders a layout. Returns output from _render(). Returns false on error.
 *
 * @param string $content_for_layout Content to render in a view, wrapped by the surrounding layout.
 * @return mixed Rendered output, or false on error
 */
	function renderLayout($content_for_layout) {
		$layout_fn = $this->_getLayoutFileName();

		if (DEBUG > 2 && $this->controller != null) {
			$debug = View::_render(LIBS . 'view' . DS . 'templates' . DS . 'elements' . DS . 'dump.thtml', array('controller' => $this->controller), false);
		} else {
			$debug = '';
		}

		if ($this->pageTitle !== false) {
			$pageTitle = $this->pageTitle;
		} else {
			$pageTitle = Inflector::humanize($this->viewPath);
		}

		$data_for_layout = array_merge(
								$this->_viewVars,
								array(
									'title_for_layout'   => $pageTitle,
									'content_for_layout' => $content_for_layout,
									'cakeDebug'          => $debug
								)
		);

		if (is_file($layout_fn)) {
			if (empty($this->loaded) && !empty($this->helpers)) {
				$loadHelpers = true;
			} else {
				$loadHelpers = false;
				$data_for_layout = array_merge($data_for_layout, $this->loaded);
			}

			if (substr($layout_fn, -5) === 'thtml') {
				$out = View::_render($layout_fn, $data_for_layout, $loadHelpers, true);
			} else {
				$out = $this->_render($layout_fn, $data_for_layout, $loadHelpers);
			}

			if ($out === false) {
				$out = $this->_render($layout_fn, $data_for_layout);
				trigger_error(sprintf(__("Error in layout %s, got: <blockquote>%s</blockquote>"), $layout_fn, $out), E_USER_ERROR);
				return false;
			} else {
				return $out;
			}
		} else {
			return $this->cakeError('missingLayout', array(
					array(
						'layout' => $this->layout,
						'file' => $layout_fn,
						'base' => $this->base
					)
			));
		}
	}

/**
 * Sets layout to be used when rendering.
 *
 * @param string $layout		Name of layout.
 */
	function setLayout($layout) {
		$this->layout = $layout;
	}

/**
 * Displays an error page to the user. Uses layouts/error.html to render the page.
 *
 * @param int $code HTTP Error code (for instance: 404)
 * @param string $name Name of the error (for instance: Not Found)
 * @param string $message Error message as a web page
 */
	function error($code, $name, $message) {
		header ("HTTP/1.0 {$code} {$name}");
		print ($this->_render(VIEWS . 'layouts/error.thtml', array('code'    => $code,
																						'name'    => $name,
																						'message' => $message)));
	}

/**************************************************************************
 * Private methods.
 *************************************************************************/

/**
 * Returns filename of given action's template file (.thtml) as a string. CamelCased action names will be under_scored! This means that you can have LongActionNames that refer to long_action_names.thtml views.
 *
 * @param string $action Controller action to find template filename for
 * @return string Template filename
 * @access private
 */
	function _getViewFileName($action) {
		$action = Inflector::underscore($action);
		$paths = Configure::getInstance();

		if (!is_null($this->webservices)) {
			$type = strtolower($this->webservices) . DS;
		} else {
			$type = null;
		}

		$position = strpos($action, '..');

		if ($position === false) {
		} else {
			$action = explode('/', $action);
			$i = array_search('..', $action);
			unset($action[$i - 1]);
			unset($action[$i]);
			$action='..' . DS . implode(DS, $action);
		}

		foreach($paths->viewPaths as $path) {
			if (file_exists($path . $this->viewPath . DS . $this->subDir . $type . $action . $this->ext)) {
				$viewFileName = $path . $this->viewPath . DS . $this->subDir . $type . $action . $this->ext;
				
				return $viewFileName;
			}
		}

		if ($viewFileName = fileExistsInPath(LIBS . 'view' . DS . 'templates' . DS . 'errors' . DS . $type . $action . '.thtml')) {
		} else if($viewFileName = fileExistsInPath(LIBS . 'view' . DS . 'templates' . DS . $this->viewPath . DS . $type . $action . '.thtml')) {
		} else {
			$viewFileName = VIEWS . $this->viewPath . DS . $this->subDir . $type . $action . $this->ext;
		}

		return $viewFileName;
	}

/**
 * Returns layout filename for this template as a string.
 *
 * @return string Filename for layout file (.thtml).
 * @access private
 */
	function _getLayoutFileName() {
		if (isset($this->webservices) && !is_null($this->webservices)) {
			$type = strtolower($this->webservices) . DS;
		} else {
			$type = null;
		}

		if (isset($this->plugin) && !is_null($this->plugin)) {
			if (file_exists(APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . 'layouts' . DS . $this->layout . $this->ext)) {
				$layoutFileName = APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . 'layouts' . DS . $this->layout . $this->ext;
				return $layoutFileName;
			}
		}
		$paths = Configure::getInstance();

		foreach($paths->viewPaths as $path) {
			if (file_exists($path . 'layouts' . DS . $this->subDir . $type . $this->layout . $this->ext)) {
				$layoutFileName = $path . 'layouts' . DS . $this->subDir . $type . $this->layout . $this->ext;
				return $layoutFileName;
			}
		}


		if($layoutFileName = fileExistsInPath(LIBS . 'view' . DS . 'templates' . DS . 'layouts' . DS . $type . $this->layout . '.thtml')) {
		} else {
			$layoutFileName = LAYOUTS . $type . $this->layout.$this->ext;
		}
		return $layoutFileName;
	}

/**
 * Renders and returns output for given view filename with its
 * array of data.
 *
 * @param string $___viewFn Filename of the view
 * @param array $___dataForView Data to include in rendered view
 * @return string Rendered output
 * @access private
 */
	function _render($___viewFn, $___dataForView, $loadHelpers = true, $cached = false) {
		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = array();
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);

			foreach(array_keys($loadedHelpers) as $helper) {
				$replace = strtolower(substr($helper, 0, 1));
				$camelBackedHelper = preg_replace('/\\w/', $replace, $helper, 1);

				${$camelBackedHelper} =& $loadedHelpers[$helper];

				if (isset(${$camelBackedHelper}->helpers) && is_array(${$camelBackedHelper}->helpers)) {
					foreach(${$camelBackedHelper}->helpers as $subHelper) {
						${$camelBackedHelper}->{$subHelper} =& $loadedHelpers[$subHelper];
					}
				}
				$this->loaded[$camelBackedHelper] = (${$camelBackedHelper});
			}
		}

		extract($___dataForView, EXTR_SKIP);
		$BASE = $this->base;
		$params =& $this->params;
		$page_title = $this->pageTitle;

		ob_start();

		if (DEBUG) {
			include ($___viewFn);
		} else {
			@include ($___viewFn);
		}

		if ($this->helpers != false && $loadHelpers === true) {
			foreach ($loadedHelpers as $helper) {
				if (is_object($helper)) {
					if (is_subclass_of($helper, 'Helper') || is_subclass_of($helper, 'helper')) {
						$helper->afterRender();
					}
				}
			}
		}

		$out = ob_get_clean();

		if (isset($this->loaded['cache']) && ((isset($this->controller) && $this->controller->cacheAction != false)) && (defined('CACHE_CHECK') && CACHE_CHECK === true)) {
			if (is_a($this->loaded['cache'], 'CacheHelper')) {
				$cache =& $this->loaded['cache'];

				if ($cached === true) {
					$cache->view = &$this;
				}

				$cache->base			= $this->base;
				$cache->here			= $this->here;
				$cache->action			= $this->action;
				$cache->controllerName	= $this->params['controller'];
				$cache->cacheAction		= $this->controller->cacheAction;
				$cache->cache($___viewFn, $out, $cached);
			}
		}

		return $out;
	}
/**
 * Loads helpers, with their dependencies.
 *
 * @param array $loaded List of helpers that are already loaded.
 * @param array $helpers List of helpers to load.
 * @return array
 */
	function &_loadHelpers(&$loaded, $helpers) {
		static $tags;
		$helpers[] = 'Session';
		if (empty($tags)) {
			$helperTags = new Helper();
			$tags = $helperTags->loadConfig();
		}

		foreach($helpers as $helper) {
			$pos = strpos($helper, '/');
			if ($pos === false) {
				$plugin = $this->plugin;
			} else {
				$parts = explode('/', $helper);
				$plugin = Inflector::underscore($parts['0']);
				$helper = $parts['1'];
			}
			$helperCn = $helper . 'Helper';

			if (in_array($helper, array_keys($loaded)) !== true) {
				if (!class_exists($helperCn)) {
				    if (is_null($plugin) || !loadPluginHelper($plugin, $helper)) {
					    if (!loadHelper($helper)) {
							return $this->cakeError('missingHelperFile', array(array(
										'helper' => $helper,
										'file' => Inflector::underscore($helper) . '.php',
										'base' => $this->base
							)));
					    }
				    }
					if (!class_exists($helperCn)) {
						return $this->cakeError('missingHelperClass', array(array(
									'helper' => $helper,
									'file' => Inflector::underscore($helper) . '.php',
									'base' => $this->base
						)));
					}
				}

				$camelBackedHelper = Inflector::variable($helper);

				${$camelBackedHelper}			=& new $helperCn;
				${$camelBackedHelper}->view		=& $this;
				${$camelBackedHelper}->tags		= $tags;

				$vars = array('base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin');
				$c = count($vars);
				for ($j = 0; $j < $c; $j++) {
					${$camelBackedHelper}->{$vars[$j]} = $this->{$vars[$j]};
				}

				if (!empty($this->validationErrors)) {
					${$camelBackedHelper}->validationErrors = $this->validationErrors;
				}

				$loaded[$helper] =& ${$camelBackedHelper};

				if (isset(${$camelBackedHelper}->helpers) && is_array(${$camelBackedHelper}->helpers)) {
					$loaded = &$this->_loadHelpers($loaded, ${$camelBackedHelper}->helpers);
				}
			}
		}
		return $loaded;
	}

/**
 * Enter description here...
 *
 * @param unknown_type $action
 * @param unknown_type $layout
 * @return unknown
 */
	function pluginView($action, $layout) {
		$CTRApp_custom_viewFileName = APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . $this->viewPath . DS . 'custom' . DS . $action . $this->ext;
		$viewFileName = APP . 'plugins' . DS . $this->plugin . DS . 'views' . DS . $this->viewPath . DS . $action . $this->ext;

		if (file_exists($CTRApp_custom_viewFileName)) {
			$this->render($action, $layout, $CTRApp_custom_viewFileName);
		} else if (file_exists($viewFileName)) {
			$this->render($action, $layout, $viewFileName);
		} else {
			return $this->cakeError('missingView', array(array(
						'className' => $this->controller->name,
						'action' => $action,
						'file' => $viewFileName,
						'base' => $this->base
			)));
		}
	}

	function renderCache($filename, $timeStart) {
		ob_start();
		include ($filename);

		if (DEBUG && $this->layout != 'xml') {
			echo "<!-- Cached Render Time: " . round(getMicrotime() - $timeStart, 4) . "s -->";
		}

		$out = ob_get_clean();

		if (preg_match('/^<!--cachetime:(\\d+)-->/', $out, $match)) {
			if (time() >= $match['1']) {
				@unlink($filename);
				unset ($out);
				return;
			} else {
				if($this->layout === 'xml'){
					header('Content-type: text/xml');
					$out = preg_replace('/^<!--cachetime:(\\d+)-->/', '', $out);
				}
				$out = str_replace('<!--cachetime:'.$match['1'].'-->','',$out);
				e($out);
				die();
			}
		}
	}
}

?>