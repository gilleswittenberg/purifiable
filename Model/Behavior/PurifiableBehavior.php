<?php
/**
 * Purifiable Model Behavior
 *
 * Scrubs fields clean of sass
 *
 * @package default
 * @author Jose Diaz-Gonzalez
 **/
App::uses('Set', 'Utility');
App::uses('HTMLPurifierWrapper', 'Purifiable.Lib');

class PurifiableBehavior extends ModelBehavior {
/**
 * Settings array
 *
 * @var array
 * @access public
 */
	public $settings = array();

/**
 * Contains default configuration settings for use with individual model objects.
 * Individual model settings should be stored as an associative array,
 * keyed off of the model name.
 *
 * @var array
 * @access protected
 * @see Model::$alias
 */
	protected $_settings = array(
		'fields' => array(),
		'overwrite' => false,
		'affix' => '_clean',
		'affix_position' => 'suffix',
		'HTMLPurifier' => array(),
	);

/**
 * Array holding HTMLPurifier instances
 *
 * @var array
 * @access protected
 */
	protected $_purifiers = array();

/**
 * Setup Purifiable with the specified configuration settings.
 *
 * @param Model $model Model using Purifiable
 * @param array $config Configuration settings for $model
 * @return void
 * @access public
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = Set::merge($this->_settings, $config);
		if (is_string($this->settings[$Model->alias]['fields'])) {
			$this->settings[$Model->alias]['fields'] = array($this->settings[$Model->alias]['fields']);
		}
		$this->_purifiers[$Model->alias] = new HTMLPurifierWrapper($this->settings[$Model->alias]['HTMLPurifier']);
	}

/**
 * Before save callback
 *
 * @param object $Model Model using Purifiable
 * @return boolean True
 * @access public
 */
	public function beforeSave(Model $Model) {
		foreach ($this->settings[$Model->alias]['fields'] as $fieldName) {
			if (!isset($Model->data[$Model->alias][$fieldName]) || empty($Model->data[$Model->alias][$fieldName])) {
				continue;
			}
			$purifiedField = $this->purify($Model, $Model->data[$Model->alias][$fieldName]);
			if (!$this->settings[$Model->alias]['overwrite']) {
				$affix = $this->settings[$Model->alias]['affix'];
				if ($this->settings[$Model->alias]['affix_position'] === 'prefix') {
					$fieldName = $affix . $fieldName;
				} else {
					$fieldName = $fieldName . $affix;
				}
			}
			$Model->data[$Model->alias][$fieldName] = $purifiedField;
		}
		return true;
	}

/**
 * Purify string
 *
 * @param object $Model Model using Purifiable
 * @param string $str String to be purified
 * @return string Purified string
 * @access public
 */
	public function purify(Model $Model, $str) {
		return $this->_purifiers[$Model->alias]->purify($str);
	}
}
