<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('PurifierComponent', 'HTMLPurifier.Controller/Component');

class TestPurifierController extends Controller {}

class PurifierComponentTest extends CakeTestCase {
	public $PurifierComponent = null;
	public $Controller = null;

	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->PurifierComponent = new PurifierComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestPurifierController($CakeRequest, $CakeResponse);
		$this->PurifierComponent->startup($this->Controller);
	}

	public function testPurifyArray() {
		$this->assertInternalType('array', $this->PurifierComponent->purifyArray(array()));
	}

	public function testPurify() {
		$this->assertInternalType('string', $this->PurifierComponent->purify(''));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->PurifierComponent);
		unset($this->Controller);
	}
}
