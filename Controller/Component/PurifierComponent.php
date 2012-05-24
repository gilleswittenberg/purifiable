<?php
//App::uses('Component', 'Controller.Component');
App::uses('HTMLPurifierWrapper', 'HTMLPurifier.Lib');
class PurifierComponent extends Component {
/**
 * HTMLPurifierWrapper instance reference
 *
 * @var HTMLPurifier
 * @access protected
 */
	protected $_HTMLPurifierWrapper = null;

/**
 * Constructor method
 *
 * @param Controller $controller
 * @param array $settings
 * @return void
 * @access public
 */
	public function startup(Controller $controller, $settings = null) {
		$this->_HTMLPurifierWrapper = new HTMLPurifierWrapper($settings);
	}

/**
 * Facade to HTMLPurifierWrapper purifyArray method
 *
 * @param array $arr
 * @return array
 * @access public
 */
	public function purifyArray($arr) {
		return $this->_HTMLPurifierWrapper->purifyArray($arr);
	}

/**
 * Facade to HTMLPurifierWrapper purify method
 *
 * @param string $str
 * @return string
 * @access public
 */
	public function purify($str) {
		return $this->_HTMLPurifierWrapper->purify($str);
	}
}
