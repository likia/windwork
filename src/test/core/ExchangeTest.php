<?php
require_once 'core/wx/Exchange.php';
require_once 'PHPUnit/Framework/TestCase.php';

use \core\util\wx\Exchange;

/**
 * exchange test case.
 */
class ExchangeTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var exchange
	 */
	private $exchange;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$_SERVER['REQUEST_METHOD'] = 'POST';	
		
		$GLOBALS['HTTP_RAW_POST_DATA'] = '<xml>
 <ToUserName><![CDATA[biz@wx]]></ToUserName>
 <FromUserName><![CDATA[guest@wx]]></FromUserName> 
 </xml>';
		$this->exchange = new Exchange();	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->exchange = null;
		
		parent::tearDown ();
	}
		
	/**
	 * Tests exchange->responseText()
	 */
	public function testResponseText() {
		$this->exchange->responseText('<h2>这是响应的内容。。。。</h2>');
	}
	
	/**
	 * Tests exchange->responseMusic()
	 */
	public function testResponseMusic() {
		$music = array(			
	      'Title'        => '一首歌',
	      'Description'  => '这是一首非常经典的歌曲。。',
	      'MusicUrl'     => 'http://www.bax.com/x/x/x.mp3',
	      'HQMusicUrl'   => 'http://www.bax.com/hixxx.mp3',
	      'ThumbMediaId' => 'xxxxxxxxxxx',
		);
		$this->exchange->responseMusic($music);
	}
	
	/**
	 * Tests exchange->responseArticles()
	 */
	public function testResponseArticles() {
		$articles = array(
			array(
			    'Title' => '文章标题1',
			    'Description' => '文章描述信息。。。。。。。',
			    'PicUrl' => 'http://www.xxc.com/jdsjo.png',
			    'Url' => 'http://www.xxc.com/',
		    ),
			array(
			    'Title' => '文章标题2',
			    'Description' => '文章2描述信息。。。。。。。',
			    'PicUrl' => 'http://www.xxc.com/jdsjo.png',
			    'Url' => 'http://www.xxc.com/',
		    ),
			array(
			    'Title' => '文章标题3',
			    'Description' => '文章3描述信息。。。。。。。',
			    'PicUrl' => 'http://www.xxc.com/jdsjo.png',
			    'Url' => 'http://www.xxc.com/',
		    ),
		);
		
		$this->exchange->responseArticles($articles);
	}
	
	/**
	 * Tests exchange->responseImage()
	 */
	public function testResponseImage() {	
		$this->exchange->responseImage($id = 'xxxxxx');
	}
	
	/**
	 * Tests exchange->responseVoice()
	 */
	public function testResponseVoice() {
		$this->exchange->responseVoice($id = 'abcdeffff');
	}
	
	/**
	 * Tests exchange->responseVideo()
	 */
	public function testResponseVideo() {
		$this->exchange->responseVideo($vid = 'vxxxx');
	}
}

