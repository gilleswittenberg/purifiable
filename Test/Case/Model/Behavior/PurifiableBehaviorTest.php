<?php
/**
 * PurifierModel TestCase
 *
 * PHP 5
 *
 * Copyright 2012, Gilles Wittenberg (http://www.gilleswittenberg.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2012, Gilles Wittenberg
 * @license	MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Base model to load Purifiable behavior on test model.
 *
 * @package	HTMLPurifier.Test
 * @author	Gilles Wittenberg
 */
class PurifiableModel extends CakeTestModel {

/**
 * Behaviors for this model
 *
 * @var array
 * @access public
 */
	public $actsAs = array('HTMLPurifier.Purifiable');
}

/**
 * TestCase for Purifiable Behavior
 *
 * @package HtMLPurifier.Test
 * @author	Gilles Wittenberg
 */
class PurifiableTest extends CakeTestCase {

/**
 * Fixtures associated with this testcase
 *
 * @var 	array
 * @access 	public
 */
	public $fixtures = array('plugin.HTMLPurifier.purifiable_model');

/**
 * Method executed before each test
 *
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
 */
	public function endTest() {
		unset($this->PurifiableModel);
		parent::tearDown();
	}

/**
 * Test beforeSave method with suffix appending
 *
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * @return 	void
 * @access 	public
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
 * Test beforeValidate Callback
 *
 * @return 	void
 * @access 	public
 */
	public function testBeforeValidateCallback() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('body'), 'callback' => 'beforeValidate'));
		$data = array(
			'PurifiableModel' => array(
				'body' => $this->str
			)
		);
		$result = $this->PurifiableModel->save($data);
		$this->assertEquals($this->expectedStr, $result['PurifiableModel']['body_clean']);
	}

/**
 * Test afterFind Callback
 *
 * @return 	void
 * @access 	public
 */
	public function testAfterFindCallback() {
		$this->PurifiableModel->Behaviors->unload('Purifiable.Purifiable');
		$this->PurifiableModel->Behaviors->load('Purifiable.Purifiable', array('fields' => array('body'), 'callback' => 'afterFind'));
		$result = $this->PurifiableModel->find('first', array('conditions' => array('id' => 1)));
		$this->assertEquals('<p>test</p>', $result['PurifiableModel']['body_clean']);
	}

/**
 * Test clean method
 *
 * @return 	void
 * @access 	public
 */
	public function testClean() {
		$cleanStr = $this->PurifiableModel->purify($this->str);
		$this->assertEquals($this->expectedStr, $cleanStr);
	}
}
