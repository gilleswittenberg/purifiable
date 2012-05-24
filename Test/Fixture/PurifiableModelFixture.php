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

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'title' => 'Dirty record',
			'body' => '<p>test</p><script>alert("XSS");</script>'
		)
	);
}
