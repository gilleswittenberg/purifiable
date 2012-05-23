<?php
App::uses('Set', 'Utility');
require_once(App::pluginPath('Purifiable') . 'Vendor' . DS . 'htmlpurifier' . DS . 'library' . DS . 'HTMlPurifier.auto.php');
/**
 * HTMLPurifier wrapper class
 *
 * Creates and wraps HTMLPurifier instance with specified configuration
 *
 * @package HTMLPurifier
 * @author Gilles Wittenberg
 */
class HTMLPurifierWrapper {

/**
 * Default configurations
 *
 * @var array
 * @access protected
 */
	protected $_defaultConfig = array();

/**
 * Configurations
 *
 * @var array
 * @access protected
 */
	protected $_config = array();

/**
 * HTMLPurifier instance reference
 *
 * @var HTMLPurifier
 * @access protected
 */
	protected $_HTMLPurifier = null;

/**
 * Constructor
 *
 * @param array $config
 * @return void
 * @access public
 */
	public function __construct($config = null) {
		$this->configure($config);
	}

/**
 * Create HTMLPurifier instance
 *
 * @param array $config Configuration
 * @return void
 * @access public
 */
	public function configure($config = null) {
		// merge configuration
		$this->_config = Set::merge($this->_defaultConfig, $config);
		// configuration
		$config = HTMLPurifier_Config::createDefault();
		foreach ($this->_config as $namespace => $values) {
			switch ($namespace) {
				case 'Filter':
					$filters = array();
					foreach ($values as $filter) {
						if (strpos($filter, 'HTMLPurifier_Filter_') !== 0) {
							$filter = 'HTMLPurifier_Filter_' . $filter;
						}
						$filters[] = new $filter;
					}
					if ($filters) {
						$config->set('Filter.Custom', $filters);
					}
					break;
				default:
					foreach ($values as $key => $value) {
						$config->set($namespace . '.' . $key, $value);
					}
					break;
			}
		}
		// create HTMLPurifier instance
		$this->_HTMLPurifier = new HTMLPurifier($config);
	}

/**
 * Recursively purify values of array
 *
 * @param array $arr Array to be purified
 * @return array Purified array
 * @access public
 */
	public function purifyArray($arr) {
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$arr[$key] = $this->purifyArray($value);
			} else {
				$arr[$key] = $this->purify($value);
			}
		}
		return $arr;
	}

/**
 * Purify string
 *
 * @param string $str String to be purified
 * @return string Purified string
 * @access public
 */
	public function purify($str) {
		return $this->_HTMLPurifier->purify($str);
	}
}
