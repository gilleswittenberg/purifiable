<?php
/**
 * PurifiableModelFixture
 *
 */
class PurifiableModelFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	 public $fields = array(
          'id' => array('type' => 'integer', 'key' => 'primary'),
          'title' => array('type' => 'string', 'length' => 255, 'null' => false),
          'body' => 'text',
	);
}
