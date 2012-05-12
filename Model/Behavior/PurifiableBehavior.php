<?php
/**
 * Purifiable Model Behavior
 *
 * Scrubs fields clean of sass
 *
 * @package default
 * @author Jose Diaz-Gonzalez
 **/
require_once(APP . 'Plugin' . DS . 'Purifiable' . DS . 'Vendor' . DS . 'htmlpurifier' . DS . 'HTMlPurifier.standalone.php');

class PurifiableBehavior extends ModelBehavior {

/**
 * Contains configuration settings for use with individual model objects.
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
 * Initiate Purifiable Behavior
 *
 * @param object $Model
 * @param array $config
 * @return void
 * @access public
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = $this->_settings;

		//merge custom config with default settings
		$this->settings[$Model->alias] = array_merge_recursive($this->settings[$Model->alias], (array)$config);
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
				$Model->data[$Model->alias][$fieldName] = $this->clean($Model, $Model->data[$Model->alias][$fieldName]);
			} else {
				$affix = $this->settings[$Model->alias]['affix'];
				$affixedFieldName = "{$fieldName}{$affix}";
				if ($this->settings[$Model->alias]['affix_position'] == 'prefix') {
					$affixedFieldName = "{$affix}{$fieldName}";
				}
				$Model->data[$Model->alias][$affixedFieldName] = $this->clean($Model, $Model->data[$Model->alias][$fieldName]);
			}
		}
		return true;
	}

	public function clean(Model $Model, $field) {
		//the next few lines allow the config settings to be cached
		$config = HTMLPurifier_Config::createDefault();
		foreach ($this->settings[$Model->alias]['config'] as $namespace => $values) {
			foreach ($values as $key => $value) {
				$config->set("{$namespace}.{$key}", $value);
			}
		}

		if($this->settings[$Model->alias]['customFilters']) {
			$filters = array();
			foreach($this->settings[$Model->alias]['customFilters'] as $customFilter) {
				$filters[] = new $customFilter;
			}
			$config->set('Filter.Custom', $filters);
		}

		$cleaner =& new HTMLPurifier($config);
		return $cleaner->purify($field);
	}

}
?>
