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
     * Test beforeSave method with suffix appending
     *
     * @access public
     */
	public function testBeforeSaveAffix() {
		$affix = 'clean_';
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('body'), 'affix' => $affix, 'affix_position' => 'prefix'));
		$data = array(
			'PurifiableModel' => array(
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel'][$affix . 'body']);
	}

	/**
     * Test beforeSave method with overwriting
     *
     * @access public
     */
	public function testBeforeSaveOverwrite() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('body'), 'overwrite' => true));
		$data = array(
			'PurifiableModel' => array(
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel']['body']);
	}

	/**
     * Test beforeSave method with multipleFields
     *
     * @access public
     */
	public function testBeforeSaveMultipleFields() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('title', 'body')));
		$title = '<h1>Header</h1>';
		$data = array(
			'PurifiableModel' => array(
				'title' => $title,
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($title, $result['PurifiableModel']['title_clean']);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel']['body_clean']);
	}

	/**
     * Test beforeSave method with fields as string
     *
     * @access public
     */
	public function testBeforeSaveFieldsAsString() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => 'body'));
		$data = array(
			'PurifiableModel' => array(
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel']['body_clean']);
	}

	/**
     * Test beforeSave method with other fields configured
     *
     * @access public
     */
	public function testBeforeSaveOtherFields() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => 'body'));
		$data = array(
			'PurifiableModel' => array(
				'title' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->str, $result['PurifiableModel']['title']);
	}
	/**
     * Test beforeSave method with Filter.Custom
     *
     * @access public
     */
	public function testBeforeSaveWithYoutubeCustomFilter() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => 'body', 'HTMLPurifier' => array('Filter' => array('HTMLPurifier_Filter_YouTube'))));
		$youtubeObject = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/nto6EvPFO0Q /><param name="wmode" value="transparent" /><embed src="http://www.youtube.com/v/nto6EvPFO0Q" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350" /></object>';
		$data = array(
			'PurifiableModel' => array(
				'body' => $youtubeObject . '<script>alert("XSS");</script>'
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals('</object>', substr($result['PurifiableModel']['body_clean'], -9));
	}

    /**
     * Test clean method
     *
     * @access public
     */
	public function testClean() {
		$cleanStr = $this->PurifiableModel->purify($this->str);
		$this->assertEquals($this->expectedStr, $cleanStr);
	}
}
