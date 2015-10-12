<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util\wx;

/**
 * 与微信公众号API交互
 *
 * @package     core.util.wx
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */ 
class Exchange {
	/**
	 * 是否已响应微信API请求
	 * @var bool
	 */
	public $isResponsed = false;
	
	/**
	 * 接收到公众号发来的消息
	 * @var SimpleXMLElement
	 */
	protected $received = array();

	public function __set($name, $value) {
		$this->received[$name] = $value;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->received)) {
			return $this->received[$name];
		} else {
			return null;
		}
	}
	
	public function __isset($name) {
		return isset($this->received[$name]);
	}
	
	public function __unset($name) {
		unset($this->received[$name]);
	}
	
	/**
	 * 接收微信消息并解析成数组格式
	 */
	public function __construct($wxToken) {
		if(!$this->checkSignature($wxToken)){
			die('Illegal access!');
		}

		if(@$_SERVER['REQUEST_METHOD'] == 'GET'){
			echo htmlspecialchars($_GET["echostr"]);
			exit;
		} else {
			$xml = $GLOBALS['HTTP_RAW_POST_DATA'];//file_get_contents("php://input");
			$xmlObj = new \SimpleXMLElement($xml);
			$this->received['xml'] = $xml;
			foreach ($xmlObj as $k => $v) {
				$this->received[$k] = (string)$v;
			}
		}
	}
	
	/**
	 * 响应内容，key=>val 结构
	 * @param string $content
	 * @param string $type
	 * @param number $flag
	 */
	protected function response($content, $type = 'text', $flag = 0) {		
		$data = array(
			'ToUserName'    => $this->received['FromUserName'], 
			'FromUserName'  => $this->received['ToUserName'], 
			'CreateTime'    => time(), 
			'MsgType'       => $type
		);
		
		$content && $data = array_merge($data, $content);
		
		if ($type != 'text') {
			$data = stripTagsDeep($data);
		}	

		//$data['FuncFlag'] = $flag;		
		$xml = $this->arr2Xml(array('xml' => $data));
		
		print $xml;
		
		$this->isResponsed = true;
	}
	
	/**
	 * 给微信接口响应文本消息
	 * @param string $content
	 */
	public function responseText($content) {		
		$this->response(array('Content' => $content), 'text');
	}
	
	/**
	 * 给微信接口响应音乐消息
	 * @param array $music array(
	 *     'Title'        => $Title,
	 *     'Description'  => $Description,
	 *     'MusicUrl'     => $MusicUrl,
	 *     'HQMusicUrl'   => $HQMusicUrl,
	 *     'ThumbMediaId' => $ThumbMediaId,
	 * )
	 */
	public function responseMusic($music) {
		$this->response(array('Music' => $music), 'music');
	}
	
	/**
	 * 给微信接口响应图文消息
	 * 
	 * $item = array(
	 *   'Title' => '文章标题3',
	 *   'Description' => '文章3描述信息。。。。。。。',
	 *   'PicUrl' => 'http://www.xxc.com/jdsjo.png',
	 *   'Url' => 'http://www.xxc.com/',
	 * )
	 * @param array $articles 多篇文章
	 */
	public function responseArticles($articles) {
		if(!is_array(current($articles))) {
			$articles = array($articles);
		}
		$data = array(
			'ArticleCount' => count($articles),
			'Articles' => $articles,
		);
		
		$this->response($data, 'news');
	}
	
	/**
	 * 响应多客服消息，使粉丝消息接入多客服系统
	 */
	public function ResponseTransferCustomerService() {
		$this->response(null, 'transfer_customer_service');
	}
	
	/**
	 * 给微信接口响应图片消息
	 * @param array $mediaId
	 */
	public function responseImage($mediaId) {		
		$this->response(array('Image' => array('MediaId' => $mediaId)), 'image');
	}
	
	/**
	 * 给微信接口响应语音消息
	 * @param array $mediaId
	 */
	public function responseVoice($mediaId) {
		$this->response(array('Voice' => array('MediaId' => $mediaId)), 'voice');
	}
	
	/**
	 * 给微信接口响应视频消息
	 * 
	 * @param array $video array(
	 *     'MediaId' => $MediaId, 
	 *     'Title' => $Title, 
	 *     'Description' => $Description
	 * )
	 */
	public function responseVideo($video) {
		$this->response(array('Video' => $video), 'video');
	}

	/**
	 * 检查签名
	 * @param string $token
	 * @return boolean
	 */
	protected function checkSignature($token) {
		$signature = @$_GET["signature"];
		$timestamp = @$_GET["timestamp"];
		$nonce     = @$_GET["nonce"];
		
		$data = array($token, $timestamp, $nonce);
		
		sort($data, SORT_STRING);
		
		$data = implode($data);
		$sign = sha1($data);
		
		return $signature == $sign;		
	}
	
	/**
	 * 数组或对象转成xml
	 *
	 * @param mixed $data variable object to convert
	 * @param string $unknown element name for numeric keys
	 * @return string
	 */
	private function arr2Xml($data, $unknown = 'item') {
		if (is_object($data)) {
			$data = (array)$data;
		}

		$xml = '';
		
		foreach($data as $k => $v) {
			if(is_numeric($k)) {
				$k = $unknown;
			}
			$xml .= "<{$k}>";
			if (is_numeric($v)) {
				$xml .= $v;
			} else if(is_scalar($v)) {
				$xml .= "<![CDATA[{$v}]]>";
			} else {
				$xml .= $this->arr2Xml($v, $unknown);
			}
			$xml .= "</{$k}>";
		}
		
		return $xml;	
	}
}

