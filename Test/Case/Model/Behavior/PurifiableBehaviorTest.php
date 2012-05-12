<?php
/**
 * Test cases for Purifiable Behavior
 *
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Base model to load Purifiable behavior on test model.
 *
 * @package App.Plugin.Purifiable
 * @subpackage App.Plugin.Purifiable.Test.Case.Model.Behavior
 */
class PurifiableModel extends CakeTestModel {
    /**
     * Behaviors for this model
     *
     * @var array
     * @access public
     */
    public $actsAs = array('Purifiable.Purifiable');
}

/**
 * Test case for Sluggable Behavior
 *
 * @package App.Plugin.Purifiable
 * @subpackage App.Plugin.Purifiable.Test.Case.Model.Behavior
 */
class PurifiableTestCase extends CakeTestCase {
    /**
     * Fixtures associated with this test case
     *
     * @var array
     * @access public
     */
    public $fixtures = array('plugin.purifiable.purifiable_model');

    /**
     * Method executed before each test
     *
     * @access public
     */
    public function startTest() {
		parent::setUp();
		$this->PurifiableModel = ClassRegistry::init('PurifiableModel');
		$this->str = '<p>test</p><script>alert("xss");</script>';
		$this->expectedStr = '<p>test</p>';
    }

    /**
     * Method executed after each test
     *
     * @access public
     */
    public function endTest() {
		unset($this->PurifiableModel);
		parent::tearDown();
    }

    /**
     * Test beforeSave method with suffix appending
     *
     * @access public
     */
	public function testBeforeSaveSuffix() {
		$suffix = '_cleaned';
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('body'), 'affix' => $suffix));
		$data = array(
			'PurifiableModel' => array(
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel']['body' . $suffix]);
	}

    /**
     * Test clean method
     *
     * @access public
     */
	public function testClean() {
		$cleanStr = $this->PurifiableModel->clean($this->str);
		$this->assertEquals($this->expectedStr, $cleanStr);
	}
}
