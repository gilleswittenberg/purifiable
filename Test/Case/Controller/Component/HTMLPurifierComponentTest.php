<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('HTMLPurifierComponent', 'Purifiable.Controller/Component');

class TestHTMLPurifierController extends Controller {}

class HTMLPurifierComponentTest extends CakeTestCase {
	public $HTMLPurifierComponent = null;
	public $Controller = null;

	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->HTMLPurifierComponent = new HTMLPurifierComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestHTMLPurifierController($CakeRequest, $CakeResponse);
		$this->HTMLPurifierComponent->startup($this->Controller);
	}

	public function testPurifyArray() {
		$this->assertInternalType('array', $this->HTMLPurifierComponent->purifyArray(array()));
	}

	public function testPurify() {
		$this->assertInternalType('string', $this->HTMLPurifierComponent->purify(''));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->HTMLPurifierComponent);
		unset($this->Controller);
	}
}
