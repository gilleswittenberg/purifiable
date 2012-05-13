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
require_once(APP . 'Plugin' . DS . 'Purifiable' . DS . 'Vendor' . DS . 'htmlpurifier' . DS . 'HTMlPurifier.standalone.php');

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
		'config' => array(
			'HTML' => array(
				'DefinitionID' => 'purifiable',
				'DefinitionRev' => 1,
				'TidyLevel' => 'heavy',
				'Doctype' => 'XHTML 1.0 Transitional'
			),
			'Core' => array(
				'Encoding' => 'ISO-8859-1'
			),
		),
		'customFilters' => array(
		)
	);

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
		$this->_configure($Model, $this->settings[$Model->alias]['config'], $this->settings[$Model->alias]['customFilters']);
	}

/**
 * Before save callback
 *
 * @param object $Model Model using this behavior
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	public function beforeSave(Model $Model) {
		foreach($this->settings[$Model->alias]['fields'] as $fieldName) {
			if (!isset($Model->data[$Model->alias][$fieldName]) or empty($Model->data[$Model->alias][$fieldName])) {
				continue;
			}

			if ($this->settings[$Model->alias]['overwrite']) {
				$Model->data[$Model->alias][$fieldName] = $this->purify($Model, $Model->data[$Model->alias][$fieldName]);
			} else {
				$affix = $this->settings[$Model->alias]['affix'];
				$affixedFieldName = "{$fieldName}{$affix}";
				if ($this->settings[$Model->alias]['affix_position'] == 'prefix') {
					$affixedFieldName = "{$affix}{$fieldName}";
				}
				$Model->data[$Model->alias][$affixedFieldName] = $this->purify($Model, $Model->data[$Model->alias][$fieldName]);
			}
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

/**
 * Configure HTMLPurifier
 *
 * @param Model $Model Model using Purifiable
 * @param array $settings Configuration settings
 * @param array $customFilters Custom Filters
 * @return object HTMLPurifier
 * @access protected
 */
	protected function _configure(Model $Model, $settings, $customFilters) {
		$config = HTMLPurifier_Config::createDefault();
		// configuration
		foreach ($settings as $namespace => $values) {
			foreach ($values as $key => $value) {
				$config->set($namespace . '.' . $key, $value);
			}
		}
		// custom filters
		$filters = array();
		foreach ($customFilters as $customFilter) {
			$filters[] = new $customFilter;
		}
		if ($filters) {
			$config->set('Filter.Custom', $filters);
		}
		// create HTMLPurifier instance
		$this->_purifiers[$Model->alias] = new HTMLPurifier($config);
	}
}
?>
