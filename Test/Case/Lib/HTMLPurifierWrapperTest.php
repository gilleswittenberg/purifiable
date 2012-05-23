<?php
/**
 * Test cases for HTMLPurifierWrapper
 *
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::uses('HTMLPurifierWrapper', 'Purifiable.Lib');

/**
 * Test case for Sluggable Behavior
 *
 * @package App.Plugin.Purifiable
 * @subpackage App.Plugin.Purifiable.Test.Case.Model.Behavior
 */
class HTMLPurifierWrapperTestCase extends CakeTestCase {

    /**
     * Method executed before each test
     *
     * @access public
     */
    public function startTest() {
		parent::setUp();
		$this->HTMLPurifierWrapper = new HTMLPurifierWrapper();
    }

    /**
     * Method executed after each test
     *
     * @access public
     */
    public function endTest() {
		parent::tearDown();
    }

    /**
     * Test configure
     *
     * @access public
     */
	public function testConfigure() {
		$config = $this->HTMLPurifierWrapper->configure();
		//$this->assertInternalType('array', $config);
	}

    /**
     * Test purify with empty config
     *
     * @access public
     */
	public function testPurifyEmptyConfig() {
		$this->HTMLPurifierWrapper->configure();
		$this->assertEquals('<p class="test">test</p>', $this->HTMLPurifierWrapper->purify('<p class="test">test</p><script>'));
	}

	/**
     * Test purify with HTML Doctype
     *
     * @access public
     */
	public function testPurifyConfigHTML() {
		$config = array(
			'HTML' => array(
				'Doctype' => 'XHTML 1.0 Strict'
			)
		);
		$this->HTMLPurifierWrapper->configure($config);
		$this->assertEquals('<p>test<br /></p>', $this->HTMLPurifierWrapper->purify('<p>test<br></p><script>'));
	}

	/**
     * Test purify with Attr allowed classes
     *
     * @access public
     */
	public function testPurifyConfigAttr() {
		$config = array(
			'Attr' => array(
				'AllowedClasses' => array()
			)
		);
		$this->HTMLPurifierWrapper->configure($config);
		$this->assertEquals('<p>test</p>', $this->HTMLPurifierWrapper->purify('<p class="test">test</p><script>'));
	}

	/**
     * Test purify with Youtube Filter
     *
     * @access public
     */
	public function testPurifyWithYoutubeCustomFilter() {
		$config = array(
			'Filter' => array(
				'HTMLPurifier_Filter_Youtube'
			)
		);
		$youtubeObject = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/nto6EvPFO0Q /><param name="wmode" value="transparent" /><embed src="http://www.youtube.com/v/nto6EvPFO0Q" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350" /></object>';
		$this->HTMLPurifierWrapper->configure($config);
		$result = $this->HTMLPurifierWrapper->purify($youtubeObject . '<script>alert("XSS");</script>');
		$this->assertEquals('</object>', substr($result, -9));
	}

	/**
     * Test purify with Youtube Filter and prepending namespace
     *
     * @access public
     */
	public function testPurifyWithYoutubeCustomFilterPrependingNamespace() {
		$config = array(
			'Filter' => array(
				'Youtube'
			)
		);
		$youtubeObject = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/nto6EvPFO0Q /><param name="wmode" value="transparent" /><embed src="http://www.youtube.com/v/nto6EvPFO0Q" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350" /></object>';
		$this->HTMLPurifierWrapper->configure($config);
		$result = $this->HTMLPurifierWrapper->purify($youtubeObject . '<script>alert("XSS");</script>');
		$this->assertEquals('</object>', substr($result, -9));
	}

	/**
	 * Test purifyArray
	 *
	 * @access public
	 */
	public function testPurifyArray() {
		$str = '<p>test</p><script>alert("XSS");</script>';
		$expectedStr = '<p>test</p>';
		$arr = array(
			'first' => $str,
			'second' => array(
				$str, $str
			)
		);
		$result = $this->HTMLPurifierWrapper->purifyArray($arr);
		$this->assertEquals($expectedStr, $result['first']);
		$this->assertEquals($expectedStr, $result['second'][0]);
		$this->assertEquals($expectedStr, $result['second'][1]);
	}
}
